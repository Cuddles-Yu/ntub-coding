# -*- coding: utf-8 -*-
import os
import re
from collections import defaultdict
from datetime import datetime
from ckiptagger import WS, POS, NER
from ckiptagger import construct_dictionary
from 地圖資訊爬蟲.ckip.module.functions import *
from 地圖資訊爬蟲.crawler.tables import Keyword
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

database = SqlDatabase('mapdb', 'root', '11236018')

START_TIME = datetime.now()

current_dir = os.path.dirname(os.path.abspath(__file__))
data_dir = current_dir + '/data'

# 初始化 CKIP Tagger
ws = WS(data_dir)
pos = POS(data_dir)
ner = NER(data_dir)

keywords = load_json(r'D:\ntub\project\repository\ntub-coding\專案檔案\地圖資訊爬蟲\crawler\doc\KEYWORDS.json')
dishes = keywords.get('餐點', []) + keywords.get('關鍵字', []) + keywords.get('定義', [])

# 測試文本
data = database.fetch('all', f'''
    SELECT s.id, s.name, c.contents FROM comments AS c
    LEFT JOIN stores AS s ON c.store_id = s.id
    WHERE c.contents IS NOT NULL and s.id = 2307
    ORDER BY c.store_id, s.id
''')

name_list = [item[1] for item in data]
sentence_list = [item[2] for item in data]

# 過濾文本，移除非中文字符並去掉換行符號
filtered_list = [re.sub(r'[^\u4e00-\u9fff，。；]', '', sentence.replace('\n', '')) for sentence in sentence_list]

# 斷詞
word_sentence_list = ws(
    filtered_list,
    sentence_segmentation=True,  # To consider delimiters
    segment_delimiter_set={"，", "。", "：", "？", "！", "；"},  # This is the default set of delimiters
    coerce_dictionary=construct_dictionary({d: 1 for d in dishes})
)
# 分詞和詞性標註
pos_sentence_list = pos(word_sentence_list)
# 命名實體識別
entity_sentence_list = ner(word_sentence_list, pos_sentence_list)

### 詞頻統計 ###
tagged_words = defaultdict(lambda: defaultdict(int))
total_comments = len(word_sentence_list)
for index in range(total_comments):
    for word, tag in zip(word_sentence_list[index], pos_sentence_list[index]):
        tagged_words[to_visualize(tag)][word] += 1
all_pos_dict = {tag: dict(sorted(words.items(), key=lambda item: item[1], reverse=True)) for tag, words in tagged_words.items()}

# write_json(all_pos_dict, 'results/id2307.json')
for keyword, count in extract_keywords_dict(all_pos_dict, '普通名詞(Na)', '地方詞(Nc)').items():
    Keyword.Keyword(
        store_id=2307,
        word=keyword,
        count=count,
        source='comment',
        image_url=None,
        source_url=None
    ).insert_if_not_exists(database)

print(f'\r已將分析結果輸出至 json 文件中。')

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

print(f'\n【✅已完成】CKIP Tagger | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | 過濾留言數: {len(filtered_list)}\n')

database.close()
