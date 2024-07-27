# -*- coding: utf-8 -*-
import json, os, re
from collections import defaultdict
from itertools import chain

import 地圖資訊爬蟲.crawler.module.modify_database as mdb
import 地圖資訊爬蟲.crawler.module.core_database as cdb

from ckiptagger import data_utils, construct_dictionary, WS, POS, NER

current_dir = os.path.dirname(os.path.abspath(__file__))
data_dir = current_dir + '/data'

RESULT_OUTPUT = 'results/all.json'

connection = cdb.connect(use_database=True)
if connection is None: exit()

# 讀取關鍵詞和形容詞列表
def read_keywords(filename):
    with open(current_dir + filename, 'r', encoding='utf-8') as f:
        return f.read().splitlines()

service_keywords = read_keywords('/dict/keywords/service.txt')
product_keywords = read_keywords('/dict/keywords/product.txt')
environment_keywords = read_keywords('/dict/keywords/environment.txt')
price_keywords = read_keywords('/dict/keywords/price.txt')
positive_adjectives = read_keywords('/dict/adjectives/positive.txt')
negative_adjectives = read_keywords('/dict/adjectives/negative.txt')

# 初始化 CKIP Tagger
ws = WS(data_dir)
pos = POS(data_dir)
ner = NER(data_dir)

# 測試文本
sentence_list = list(chain.from_iterable(mdb.fetch(connection, 'all', f'''
    SELECT contents FROM mapdb.comments AS c
    LEFT JOIN mapdb.stores AS s ON c.store_id = s.id
    WHERE contents IS NOT NULL
    ORDER BY `store_id`, `index`
    LIMIT 10
''')))

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

# 初始化存储标签的字典
tagged_words = defaultdict(lambda: defaultdict(int))

total_comments = len(word_sentence_list)
for index in range(total_comments):
    for word, tag in zip(word_sentence_list[index], pos_sentence_list[index]):
        tagged_words[tag][word] += 1

sentence_tag_list = {tag: dict(sorted(words.items(), key=lambda item: item[1], reverse=True)) for tag, words in tagged_words.items()}

with open(RESULT_OUTPUT, 'w', encoding='utf-8') as f:
    json.dump(sentence_tag_list, f, ensure_ascii=False, indent=4)
    print(f'\r已將分析結果輸出至 data.json 文件中。')

print(json.dumps(sentence_tag_list, ensure_ascii=False, indent=4))

# # 指定的標記類型
# target_tags = ["VH", "VA", "VJ"]
#
# # 保留符合標記類型的索引
# filtered_indices = [i for i, tag in enumerate(pos_sentence_list[0]) if tag in target_tags]
#
# # 取出符合標記類型的斷詞和標記
# filtered_words = [word_sentence_list[0][i] for i in filtered_indices]
#
# print(" ".join(filtered_words))
#
# # 初始化四大指標分數
# scores = {
#     "服務": 0,
#     "產品": 0,
#     "環境": 0,
#     "售價": 0
# }

# def print_word_pos_sentence(word_sentence, pos_sentence):
#     assert len(word_sentence) == len(pos_sentence)
#     for word, pos in zip(word_sentence, pos_sentence):
#         print(f"{word}({pos})", end="\u3000")
#     print()
#     return
#
# for i, sentence in enumerate(sentence_list):
#     print()
#     print(f"'{sentence}'")
#     print_word_pos_sentence(word_sentence_list[i], pos_sentence_list[i])
#     for entity in sorted(entity_sentence_list[i]):
#         print(entity)
#
# # 解析斷詞結果並進行情感分析
# for words, pos_tags in zip(word_sentence_list, pos_sentence_list):
#     for i, (word, pos_tag) in enumerate(zip(words, pos_tags)):
#         if pos_tag in ["VH", "VA", "VJ"]:  # 形容詞
#             # 查找受詞
#             if i > 0 and pos_tags[i - 1] in ["Na", "Nb", "Nc"]:
#                 subject = words[i - 1]
#                 adjective = word
#                 # print(f"形容詞: {adjective}, 受詞: {subject}")
#
#                 # 判斷形容詞的情感極性和受詞的類別
#                 category = None
#                 sentiment = None
#                 if subject in service_keywords:
#                     category = "服務"
#                 elif subject in product_keywords:
#                     category = "產品"
#                 elif subject in environment_keywords:
#                     category = "環境"
#                 elif subject in price_keywords:
#                     category = "售價"
#
#                 if adjective in positive_adjectives:
#                     sentiment = "正面"
#                 elif adjective in negative_adjectives:
#                     sentiment = "負面"
#
#                 if category and sentiment:
#                     print(f"指標: {category}, 情感: {sentiment}")
#                     # 根據情感增減分數
#                     if sentiment == "正面":
#                         scores[category] += 1
#                     elif sentiment == "負面":
#                         scores[category] -= 1
#
# # 輸出最終的四大指標分數
# print("最終指標分數:", scores)

# 關閉資料庫連線階段
connection.close()
