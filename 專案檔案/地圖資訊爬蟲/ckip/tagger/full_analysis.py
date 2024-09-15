# -*- coding: utf-8 -*-
import os
import re
from collections import defaultdict
from datetime import datetime

from ckiptagger import WS, POS, NER

from 地圖資訊爬蟲.ckip.module.functions import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

database = SqlDatabase('mapdb', 'root', '11236018')

START_TIME = datetime.now()

current_dir = os.path.dirname(os.path.abspath(__file__))
data_dir = current_dir + '/data'

# 初始化 CKIP Tagger
ws = WS(data_dir)
pos = POS(data_dir)
ner = NER(data_dir)

# 測試文本
data = database.fetch('all', f'''
    SELECT s.name, c.contents FROM comments AS c
    LEFT JOIN stores AS s ON c.store_id = s.id
    WHERE c.contents IS NOT NULL
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
write_json(all_pos_dict, 'results/test_pos.json')

#### 斷詞分析 ###
last_store_name = ''
all_analysis_dict = {}
for i, sentence in enumerate(sentence_list):
    if last_store_name != name_list[i]:
        if last_store_name: all_analysis_dict[last_store_name] = analysis.copy()
        analysis = defaultdict(lambda: defaultdict(int))
        last_store_name = name_list[i]
    for word, tag in zip(word_sentence_list[i], pos_sentence_list[i]):
        if tag == 'Na' or tag == 'Nc' or tag == 'Nb': analysis[to_visualize(tag)][word] += 1
    analysis = defaultdict(lambda: defaultdict(int), {tag: defaultdict(int, sorted(words.items(), key=lambda item: item[1], reverse=True)) for tag, words in analysis.items()})

write_json(all_analysis_dict, 'results/test_analysis.json')

print(f'\r已將分析結果輸出至 json 文件中。')

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

print(f'\n【✅已完成】CKIP Tagger | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | 過濾留言數: {len(filtered_list)}\n')

database.close()
