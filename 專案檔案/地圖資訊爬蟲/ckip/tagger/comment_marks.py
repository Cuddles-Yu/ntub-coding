# -*- coding: utf-8 -*-
import json, os, re
import time
from collections import defaultdict
from datetime import datetime
from ckiptagger import construct_dictionary
import 地圖資訊爬蟲.ckip.tagger.adjective_analysis as aa
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from 地圖資訊爬蟲.ckip.module.functions import *
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.color_code import ColorCode
from 地圖資訊爬蟲.crawler.tables import Mark
from ckiptagger import WS, POS, NER

database = SqlDatabase('mapdb', 'root', '11236018')
START_TIME = datetime.now()

### 初始化 CKIPTagger ###
data_dir = os.path.dirname(os.path.abspath(__file__)) + '/data'
ws = WS(data_dir)
pos = POS(data_dir)
ner = NER(data_dir)

### 讀取設定檔 ###
settings = str_to_json(get_args(index=1))
if not settings: settings = {'store_id': None, 'limit': None}

keywords = load_json(r'D:\ntub\project\repository\ntub-coding\專案檔案\地圖資訊爬蟲\crawler\doc\KEYWORDS.json')
aa.pos_target_data['受詞']['產品'] = list(set(aa.pos_target_data['受詞']['產品']+keywords.get('餐點', [])))
dishes = keywords.get('餐點', []) + keywords.get('關鍵字', []) + keywords.get('定義', [])

sids = database.fetch_column('all', 0, '''
    SELECT DISTINCT r.store_id FROM rates AS r
    LEFT JOIN marks AS m ON r.store_id = m.store_id
    WHERE m.store_id IS NULL
''')

### 留言分析 ###
print()

for e_sid, sid in enumerate(sids):
    settings = {'store_id': sid, 'limit': None}
    setting_store_id = f'AND store_id = {settings.get("store_id")}' if settings.get('store_id') else ''
    setting_limit = f'LIMIT {settings.get("limit")}' if settings.get('limit') else ''

    sentence_splits = []

    # 測試文本
    comments = database.fetch('all', f'''
       SELECT store_id, id, contents FROM comments AS c
       WHERE contents IS NOT NULL {setting_store_id}
       ORDER BY `store_id`, `id`
       {setting_limit}
    ''')
    if not comments: continue

    # 統計數量
    tagged_words = defaultdict(lambda: defaultdict(int))

    for e_comment, (store_id, comment_id, sentence) in enumerate(comments):

        print(f"\r評論斷句標記提取 | 商家id:{sid} | 進度:{e_sid+1}/{len(sids)} | 留言:{e_comment+1}/{len(comments)}", end='')
        filtered_sentences = [f.strip() for f in [re.sub(r'[^\u4e00-\u9fffA-Za-z，。；、\s]', '', s) for s in re.split(r'[，；。\n]+', sentence) if s] if f]
        # 斷詞
        word_sentence_list = ws(
            filtered_sentences,
            sentence_segmentation=True,  # To consider delimiters
            segment_delimiter_set={"，", "。", "：", "？", "！", "；"},  # This is the default set of delimiters
            coerce_dictionary=construct_dictionary({d: 1 for d in dishes})
        )
        # 分詞和詞性標註
        pos_sentence_list = pos(word_sentence_list)
        # 命名實體識別
        entity_sentence_list = ner(word_sentence_list, pos_sentence_list)

        ### 詞頻統計 ###
        total_comments = len(word_sentence_list)
        for index in range(total_comments):
            for word, tag in zip(word_sentence_list[index], pos_sentence_list[index]):
                tagged_words[to_visualize(tag)][word] += 1

        sentence_splits = []

        # print(sentence)
        #### 斷詞分析 ###
        for i, filter_sentence in enumerate(filtered_sentences):
            analysis = {
                '斷句': filter_sentence,
                '斷詞': [],
                '詞性': []
            }
            word_list = []
            pos_list = []
            for _word, _pos in zip(word_sentence_list[i], pos_sentence_list[i]):
                _word = _word.strip()
                if _word in dishes: _pos = 'Na'
                _pos = aa.correct_pos(_word, _pos)
                color = pos_color(_pos)
                word_list.append(_word)
                pos_list.append(_pos)
                if color:
                    analysis['斷詞'].append(f'{color}{_word}{ColorCode.DEFAULT}')
                    analysis['詞性'].append(f'{color}{_pos}{ColorCode.DEFAULT}')
                else:
                    analysis['斷詞'].append(_word)
                    analysis['詞性'].append(_pos)

            # if is_sublist([['Na', 'D', 'VH'], ['VA', 'D', 'VH'], ['Na', 'Dfa', 'VH'], ['VA', 'Dfa', 'VH']], pos_list):
            # combine_word_na, combine_pos_na =
            # word_list, pos_list = combine_by_pattern(*combine_by_pattern(*combine_by_pattern(*combine_by_pos(word_list, pos_list, 'Na'), ["Na", "DE", "Na"]),  ["VH", "VB", "DE"]), ["VH", "Na"])
            # print(f"{{'斷句': {analysis.get('斷句')}, '斷詞': [{', '.join(analysis.get('斷詞', []))}], '詞性': [{', '.join(analysis.get('詞性', []))}]}}")

            sentence_splits.append({"商家": sid, "留言": comment_id, "斷句": filter_sentence, "斷詞": word_list, "詞性": pos_list})

        aa.analyze_sentence(sentence_splits)

        # print()

    # 輸出最終得分
    # print("\n最終得分:", aa.scores)

    # database.execute(f'''
    #     TRUNCATE `marks`
    # ''')
    mid = 1
    last_store = -1
    last_comment = -1
    ana = {}
    for i, m in enumerate(aa.marks):
        if last_store != m.store_id:
            last_store = m.store_id
        if last_comment != m.comment_id:
            if ana:
                result = {}
                for target, sentiments in ana.items():
                    # 找到最大值
                    max_value = max(sentiments.values())
                    # 找到所有等於最大值的情緒
                    max_sentiments = [k for k, v in sentiments.items() if v == max_value]
                    # 判斷結果
                    if len(max_sentiments) > 1:
                        result[target] = None  # 如果多個最大值，則輸出 None
                    else:
                        result[target] = max_sentiments[0]  # 否則輸出最大值的情緒
                database.update('comments', {
                    "environment_state": result.get("環境"),
                    "price_state": result.get("售價"),
                    "product_state": result.get("產品"),
                    "service_state": result.get("服務")
                }, {"store_id": last_store, "id": last_comment})
            ana = {
                "環境": {"正面": 0, "負面": 0, "中立": 0, "喜好": 0},
                "產品": {"正面": 0, "負面": 0, "中立": 0, "喜好": 0},
                "服務": {"正面": 0, "負面": 0, "中立": 0, "喜好": 0},
                "售價": {"正面": 0, "負面": 0, "中立": 0, "喜好": 0}
            }
            last_comment = m.comment_id
            mid = 1
        ana[m.get_target()][m.get_state()] += 1
        m.id = mid
        mid += 1
        m.insert(database)

    aa.marks.clear()

# all_pos_dict = {tag: dict(sorted(words.items(), key=lambda item: item[1], reverse=True)) for tag, words in tagged_words.items()}
# print(all_pos_dict)

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds()/60

if comments:
    print(f'\n【✅已完成】CKIP Tagger 提取留言形容詞標記 | 耗時:{MINUTES_ELAPSE:.2f}分鐘\n')
else:
    print(f'\n未找到尚未進行形容詞標記的留言。')

database.close()
