import pandas as pd
from 地圖資訊爬蟲.crawler.module.color_code import ColorCode
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

pd.set_option('display.max_rows', None)  # 取消行數顯示限制
pd.set_option('display.max_columns', None)  # 取消欄位數顯示限制
pd.set_option('display.expand_frame_repr', False)  # 取消列寬限制（避免數據自動換行）

database = SqlDatabase('mapdb', 'root', '11236018')

# 1. 從資料庫讀取 Marks 資料表
marks_df = pd.DataFrame([{
    'store_id': store_id,
    'comment_id': comment_id,
    'target': target,
    'state': state
} for store_id, comment_id, target, state in database.fetch('all', f'''
    SELECT store_id, comment_id, target, state FROM marks
    ORDER BY store_id, comment_id
''')])

# 2. 從資料庫讀取 Rates 資料表
rates_df = pd.DataFrame([{
    'store_id': store_id,
    'total_withcomments': total_withcomments
} for store_id, total_withcomments in database.fetch('all', f'''
    SELECT store_id, total_withcomments FROM rates
''')])

def normalize_weights(weights_dict):
    total_weight = sum(weights_dict.values())  # 計算權重的總和
    if total_weight == 0:
        raise ValueError("Total weight cannot be zero")

    # 將權重正規化為百分比（加總為100%）
    normalized_weights = {key: value / total_weight for key, value in weights_dict.items()}
    return normalized_weights

# 定義指標權重，這裡會自動正規化為百分比
TARGET_WEIGHTS = normalize_weights({
    'Environment': 30,
    'Product': 30,
    'Service': 30,
    'Price': 30
})

print(f"Normalized Weights: {TARGET_WEIGHTS}")

# 計算 Bayesian average 的方法
def calculate_bayesian_average(store_scores, rates_df):
    # r: 各商家的初步分數
    store_scores = store_scores.merge(rates_df[['store_id', 'total_withcomments']], on='store_id')

    # m: 所有商家的總體平均分數
    overall_mean_score = store_scores['score'].mean()

    # C: 一個常量，代表評論數量的基準值（可以使用全體的平均評論數）
    C = rates_df['total_withcomments'].mean()

    # 計算 Bayes 平均分數
    store_scores['bayesian_score'] = (
        (C * overall_mean_score) + (store_scores['score'] * store_scores['total_withcomments'])
    ) / (C + store_scores['total_withcomments'])

    return store_scores, overall_mean_score, C

# 計算每則評論的正負面比例，加入留言數權重
def calculate_comment_stance(marks_df, rates_df):
    valid_states = ['正面', '負面']
    marks_df = marks_df[marks_df['state'].isin(valid_states)]

    # 計算每個商家針對每個指標的正負面比例
    stance = marks_df.groupby(['store_id', 'target', 'state']).size().unstack(fill_value=0)
    stance['total'] = stance['正面'] + stance['負面']

    # 加入留言數權重（來自 rates 資料表）
    stance = stance.reset_index().merge(rates_df[['store_id', 'total_withcomments']], on='store_id', how='left')
    stance['total_withcomments'] = stance['total_withcomments'].fillna(1)  # 防止除以0

    # 標準化留言數權重
    avg_comments = stance['total_withcomments'].mean()
    stance['normalized_comments'] = stance['total_withcomments'] / avg_comments

    # 計算加權後的正負面比例
    stance['pos_ratio'] = (stance['正面'] / stance['total']) * stance['normalized_comments']
    stance['neg_ratio'] = (stance['負面'] / stance['total']) * stance['normalized_comments']

    # 確保比例不超過 1
    stance['pos_ratio'] = stance['pos_ratio'].clip(upper=1)
    stance['neg_ratio'] = stance['neg_ratio'].clip(upper=1)

    # 將中文的指標轉換成英文
    stance['target'] = stance['target'].replace({
        '環境': 'Environment',
        '產品': 'Product',
        '服務': 'Service',
        '售價': 'Price'
    })

    return stance

# 計算每個指標的分數（加上留言數權重）
def calculate_indicator_scores(stance):
    # 初始分數計算，每個指標的分數基於正面比例，不映射到0-100
    stance['indicator_score'] = stance['pos_ratio'] * 100  # 原始分數
    return stance

# 計算每個商家的綜合分數（基於可調整權重），保留四大指標分數
def calculate_store_scores(indicator_scores):
    # 使用 pivot 將指標分數轉為一個表格
    pivot_scores = indicator_scores.pivot(index='store_id', columns='target', values='indicator_score').reset_index()

    # 確保各指標存在，避免 NaN 影響計算
    pivot_scores = pivot_scores.fillna(0)

    # 計算總分數，將正規化的權重應用於每個指標分數
    pivot_scores['score'] = (
        pivot_scores['Environment'] * TARGET_WEIGHTS['Environment'] +
        pivot_scores['Product'] * TARGET_WEIGHTS['Product'] +
        pivot_scores['Service'] * TARGET_WEIGHTS['Service'] +
        pivot_scores['Price'] * TARGET_WEIGHTS['Price']
    )

    # 保留四大指標的分數，以及計算的總分數
    return pivot_scores[['store_id', 'Environment', 'Product', 'Service', 'Price', 'score']]

# 主程式流程
def main():
    # Step 1: 計算每個商家的正負面比例
    stance = calculate_comment_stance(marks_df, rates_df)

    # Step 2: 基於正面比例計算指標分數
    indicator_scores = calculate_indicator_scores(stance)

    # Step 3: 計算每個商家的初步總分數，並保存各指標分數
    store_scores = calculate_store_scores(indicator_scores)

    # Step 4: 使用 Bayes 平均法計算最終分數，並單獨顯示 bayesian_average 和 C_value
    final_scores, bayesian_average, C_value = calculate_bayesian_average(store_scores, rates_df)

    # 顯示最終結果，包含各指標的分數
    print("\n最終商家分數（Bayesian 平均後，包含過程數據）：")
    print(final_scores)

    # 顯示通用的 bayesian_average 和 C_value
    print(f"\nBayesian 平均值 (全體商家的平均分數): {bayesian_average}")
    print(f"C 值 (評論數基準值): {C_value}")

if __name__ == "__main__":
    main()
