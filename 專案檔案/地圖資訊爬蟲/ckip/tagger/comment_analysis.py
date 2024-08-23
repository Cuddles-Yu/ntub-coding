# -*- coding: utf-8 -*-
import json, os, re
from collections import defaultdict
from datetime import datetime
from ckiptagger import construct_dictionary
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from 地圖資訊爬蟲.ckip.module.functions import *
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.color_code import ColorCode
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
if not settings: settings = {'store_id': 1, 'limit': None}
setting_store_id = f'AND store_id = {settings.get("store_id")}' if settings.get('store_id') else ''
setting_limit = f'LIMIT {settings.get("limit")}' if settings.get('limit') else ''

keywords = load_json(r'D:\ntub\project\repository\ntub-coding\專案檔案\地圖資訊爬蟲\crawler\doc\KEYWORDS.json')
dishes = keywords.get('餐點', [])

# 測試文本
sentence_list = database.fetch_column('all', 0, f'''
   SELECT contents FROM comments AS c
   WHERE contents IS NOT NULL {setting_store_id}
   ORDER BY `store_id`, `id`
   {setting_limit}
''')

# 統計數量
tagged_words = defaultdict(lambda: defaultdict(int))

for sentence in sentence_list:
    filtered_sentences = [f for f in [re.sub(r'[^\u4e00-\u9fff，。；、\s]', '', s) for s in re.split(r'[，；。\n]+', sentence) if s] if f]
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

    print(sentence)
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
            color = pos_color(_pos)
            if _word in dishes: _pos = 'Na'
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
        print(combine_to_dict(*combine_by_pattern(*combine_by_pos(word_list, pos_list, 'Na'), ["Na", "DE", "Na"])))
        print(f'{{"斷句": {analysis.get("斷句")}, "斷詞": [{", ".join(analysis.get("斷詞", []))}], "詞性": [{", ".join(analysis.get("詞性", []))}]}}')

    print()

all_pos_dict = {tag: dict(sorted(words.items(), key=lambda item: item[1], reverse=True)) for tag, words in tagged_words.items()}
print(all_pos_dict)

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

print(f'\n【✅已完成】CKIP Tagger | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | 留言數: {len(sentence_list)}\n')
database.close()
