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
ignore = keywords.get('忽略', []) + keywords.get('非關鍵字', [])

sids = database.fetch_column('all', 0,  f'''
    SELECT s.id FROM stores AS s
    LEFT JOIN rates AS r ON s.id = r.store_id
    WHERE s.crawler_state IN ('完成', '超時', '成功') and r.keywords_analysis = 0 and s.id >= 4600
''')

keyword_dict = defaultdict(int)

for sid in sids:
    data = database.fetch('all', f'''
        SELECT s.name, c.contents FROM comments AS c
        LEFT JOIN stores AS s ON c.store_id = s.id
        WHERE c.contents IS NOT NULL and s.id = {sid}
        ORDER BY c.store_id, s.id
    ''')
    name_list = [item[0] for item in data]
    sentence_list = [item[1] for item in data]

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

    for keyword, count in extract_keywords_dict(all_pos_dict, '普通名詞(Na)', '地方詞(Nc)').items():
        if keyword in ignore or count <= 1: continue
        Keyword.Keyword(
            store_id=sid,
            word=keyword,
            count=count,
            source='comment',
            image_url=None,
            source_url=None
        ).insert_if_not_exists(database)

    print(sid)
    database.update('rates', {
        "keywords_analysis": 1,
    }, {"store_id": sid})

keyword_dict = dict(sorted(keyword_dict.items(), key=lambda item: item[1], reverse=True))
print(keyword_dict)

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

print(f'\n【✅已完成】CKIP Tagger 餐廳關鍵字提取 | 耗時:{MINUTES_ELAPSE:.2f}分鐘\n')

database.close()
