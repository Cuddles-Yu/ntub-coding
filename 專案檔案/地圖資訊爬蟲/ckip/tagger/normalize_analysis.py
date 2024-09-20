import pandas as pd
from 地圖資訊爬蟲.crawler.module.color_code import ColorCode
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

pd.set_option('display.max_rows', None)  # 取消行數顯示限制
pd.set_option('display.max_columns', None)  # 取消欄位數顯示限制
pd.set_option('display.expand_frame_repr', False)  # 取消列寬限制（避免數據自動換行）

database = SqlDatabase('mapdb', 'root', '11236018')

# 1. 從資料庫讀取 Comments 資料表
comments_df = pd.DataFrame([{
    'store_id': store_id,
    'environment_state': environment_state,
    'price_state': price_state,
    'product_state': product_state,
    'service_state': service_state
} for store_id, environment_state, price_state, product_state, service_state in database.fetch('all', f'''
    SELECT c.store_id, c.environment_state, c.price_state, c.product_state, c.service_state FROM comments AS c
    LEFT JOIN stores AS s ON s.id = c.store_id
    WHERE s.crawler_state IN ('完成', '超時', '成功') and (c.environment_state IS NOT NULL OR c.price_state IS NOT NULL OR c.product_state IS NOT NULL OR c.service_state IS NOT NULL)
''')])

# 2. 從資料庫讀取 Rates 資料表
rates_df = pd.DataFrame([{
    'store_id': store_id,
    'total_reviews': total_reviews,
    'total_withcomments': total_withcomments
} for store_id, total_reviews, total_withcomments in database.fetch('all', f'''
    SELECT store_id, total_reviews, total_withcomments FROM rates AS r
    LEFT JOIN stores AS s ON s.id = r.store_id
    WHERE s.crawler_state IN ('完成', '超時', '成功')
''')])

# 定義正負面狀態
positive_states = ['正面']
negative_states = ['負面']

# 定義指標權重，這裡會自動正規化為百分比
def normalize_weights(weights_dict):
    total_weight = sum(weights_dict.values())  # 計算權重的總和
    if total_weight == 0:
        raise ValueError("Total weight cannot be zero")

    # 將權重正規化為百分比（加總為100%）
    normalized_weights = {key: value / total_weight for key, value in weights_dict.items()}
    return normalized_weights

# 定義四個指標的權重
TARGET_WEIGHTS = normalize_weights({
    'Environment': 30,
    'Product': 30,
    'Service': 30,
    'Price': 30
})

# 計算每個商家的四大指標正負面比例
def calculate_comment_proportions(comments_df):
    proportions = comments_df.groupby('store_id').apply(lambda x: pd.Series({
        'environment_pos': (x['environment_state'].isin(positive_states)).sum(),
        'environment_neg': (x['environment_state'].isin(negative_states)).sum(),
        'price_pos': (x['price_state'].isin(positive_states)).sum(),
        'price_neg': (x['price_state'].isin(negative_states)).sum(),
        'product_pos': (x['product_state'].isin(positive_states)).sum(),
        'product_neg': (x['product_state'].isin(negative_states)).sum(),
        'service_pos': (x['service_state'].isin(positive_states)).sum(),
        'service_neg': (x['service_state'].isin(negative_states)).sum(),

        # 只統計正面和負面作為總數
        'environment_total': (x['environment_state'].isin(positive_states + negative_states)).sum(),
        'price_total': (x['price_state'].isin(positive_states + negative_states)).sum(),
        'product_total': (x['product_state'].isin(positive_states + negative_states)).sum(),
        'service_total': (x['service_state'].isin(positive_states + negative_states)).sum()
    }))

    # 計算每個指標的正面比例
    proportions['environment_pos_ratio'] = proportions['environment_pos'] / proportions['environment_total'].replace(0, 1)
    proportions['price_pos_ratio'] = proportions['price_pos'] / proportions['price_total'].replace(0, 1)
    proportions['product_pos_ratio'] = proportions['product_pos'] / proportions['product_total'].replace(0, 1)
    proportions['service_pos_ratio'] = proportions['service_pos'] / proportions['service_total'].replace(0, 1)

    return proportions[['environment_pos_ratio', 'price_pos_ratio', 'product_pos_ratio', 'service_pos_ratio']]

# 將 comments 的比例整合進去後續計算流程
def calculate_indicator_scores_from_comments(comments_proportions):
    # 根據 comments 的正面比例計算初始分數，並使用 .loc 來進行賦值
    comments_proportions.loc[:, 'Environment'] = comments_proportions['environment_pos_ratio'] * 100
    comments_proportions.loc[:, 'Product'] = comments_proportions['product_pos_ratio'] * 100
    comments_proportions.loc[:, 'Service'] = comments_proportions['service_pos_ratio'] * 100
    comments_proportions.loc[:, 'Price'] = comments_proportions['price_pos_ratio'] * 100

    return comments_proportions.loc[:, ['Environment', 'Product', 'Service', 'Price']]

# 計算每個商家的綜合分數（基於權重）
def calculate_store_scores_from_comments(indicator_scores):
    # 使用 .loc 明確修改資料框，避免警告
    indicator_scores.loc[:, 'score'] = (
        indicator_scores['Environment'] * TARGET_WEIGHTS['Environment'] +
        indicator_scores['Product'] * TARGET_WEIGHTS['Product'] +
        indicator_scores['Service'] * TARGET_WEIGHTS['Service'] +
        indicator_scores['Price'] * TARGET_WEIGHTS['Price']
    )
    return indicator_scores.loc[:, ['Environment', 'Product', 'Service', 'Price', 'score']]


# 計算 Bayesian average 的方法，降低 C 值的影響
def calculate_bayesian_average(store_scores, rates_df):
    store_scores = store_scores.merge(rates_df[['store_id', 'total_withcomments']], on='store_id')
    overall_mean_score = store_scores['score'].mean()

    # 減少 C 值對貝氏平均的影響，使用中位數評論數
    C = rates_df['total_withcomments'].median()

    # 計算 Bayes 平均分數
    store_scores['bayesian_score'] = (
        (C * overall_mean_score) + (store_scores['score'] * store_scores['total_withcomments'])
    ) / (C + store_scores['total_withcomments'])

    return store_scores, overall_mean_score, C

# 主程式流程
def main():
    # Step 1: 從 comments_df 計算每個商家的正負面比例
    comments_proportions = calculate_comment_proportions(comments_df)

    # Step 2: 基於正面比例計算指標分數
    indicator_scores = calculate_indicator_scores_from_comments(comments_proportions)

    # Step 3: 計算每個商家的初步總分數，並保存各指標分數
    store_scores = calculate_store_scores_from_comments(indicator_scores)

    # Step 4: 使用 Bayes 平均法計算最終分數，並單獨顯示 bayesian_average 和 C_value
    final_scores, bayesian_average, C_value = calculate_bayesian_average(store_scores, rates_df)

    # 按照 bayesian_score 進行排序
    final_scores = final_scores.sort_values(by='bayesian_score', ascending=False)

    # 顯示最終結果，包含原始分數和加權後的分數
    print(F"\n{ColorCode.DARK_BLUE}{ColorCode.BOLD}分數結果:{ColorCode.DEFAULT}")
    print(final_scores.to_string(index=False))

    # 顯示貝氏平均和 C 值
    print(f"\n{ColorCode.DARK_BLUE}{ColorCode.BOLD}貝氏平均:{ColorCode.DEFAULT} {bayesian_average}")
    print(f"{ColorCode.DARK_BLUE}{ColorCode.BOLD}C值基準:{ColorCode.DEFAULT} {C_value}")

if __name__ == "__main__":
    main()
