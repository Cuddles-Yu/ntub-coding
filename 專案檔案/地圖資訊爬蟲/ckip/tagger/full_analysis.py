# -*- coding: utf-8 -*-
import json, os, re
from collections import defaultdict
from datetime import datetime
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from 地圖資訊爬蟲.ckip.module.functions import *
from ckiptagger import WS, POS, NER

database = SqlDatabase('mapdb', 'root', '11236018')

START_TIME = datetime.now()

current_dir = os.path.dirname(os.path.abspath(__file__))
data_dir = current_dir + '/data'

# # 讀取關鍵詞和形容詞列表
# def read_keywords(filename):
#     with open(current_dir + filename, 'r', encoding='utf-8') as f:
#         return f.read().splitlines()
#
# service_keywords = read_keywords('../dict/keywords/service.txt')
# product_keywords = read_keywords('../dict/keywords/product.txt')
# environment_keywords = read_keywords('../dict/keywords/environment.txt')
# price_keywords = read_keywords('../dict/keywords/price.txt')
# positive_adjectives = read_keywords('../dict/adjectives/positive.txt')
# negative_adjectives = read_keywords('../dict/adjectives/negative.txt')

# 初始化 CKIP Tagger
ws = WS(data_dir)
pos = POS(data_dir)
ner = NER(data_dir)

# 測試文本
sentence_list = database.fetch_column('all', 0, f'''
   SELECT contents FROM comments AS c
   LEFT JOIN stores AS s ON c.store_id = s.id
   WHERE contents IS NOT NULL
   ORDER BY `store_id`, `index`
''')

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

def to_visualize(pattern: str):
    transform = None
    if pattern == "VA":
        transform = "動作不及物動詞(VA)"
    elif pattern == "Na":
        transform = "普通名詞(Na)"
    elif pattern == "D":
        transform = "副詞(D)"
    elif pattern == "VE":
        transform = "動作句賓動詞(VE)"
    elif pattern == "Dfa":
        transform = "動詞前程度副詞(Dfa)"
    elif pattern == "SHI":
        transform = "是(SHI)"
    elif pattern == "Nep":
        transform = "指代定詞(Nep)"
    elif pattern == "Neu":
        transform = "數詞定詞(Neu)"
    elif pattern == "Nf":
        transform = "量詞(Nf)"
    elif pattern == "VH":
        transform = "狀態不及物動詞(VH)"
    elif pattern == "VC":
        transform = "動作及物動詞(VC)"
    elif pattern == "VJ":
        transform = "狀態及物動詞(VJ)"
    elif pattern == "DE":
        transform = "的之得地(DE)"
    elif pattern == "Nc":
        transform = "地方詞(Nc)"
    elif pattern == "V":
        transform = "有(V_2)"
    elif pattern == "Cbb":
        transform = "關聯連接詞(Cbb)"
    elif pattern == "VD":
        transform = "雙賓動詞(VD)"
    elif pattern == "P":
        transform = "介詞(P)"
    elif pattern == "VG":
        transform = "分類動詞(VG)"
    elif pattern == "Di":
        transform = "時態標記(Di)"
    elif pattern == "VHC":
        transform = "狀態使動動詞(VHC)"
    elif pattern == "VL":
        transform = "狀態謂賓動詞(VL)"
    elif pattern == "Nh":
        transform = "代名詞(Nh)"
    elif pattern == "VI":
        transform = "狀態類及物動詞(VI)"
    elif pattern == "Nb":
        transform = "專有名詞(Nb)"
    elif pattern == "Caa":
        transform = "對等連接詞(Caa)"
    elif pattern == "Ncd":
        transform = "位置詞(Ncd)"
    elif pattern == "VK":
        transform = "狀態句賓動詞(VK)"
    elif pattern == "Neqa":
        transform = "數量定詞(Neqa)"
    elif pattern == "Da":
        transform = "數量副詞(Da)"
    elif pattern == "T":
        transform = "語助詞(T)"
    elif pattern == "Ng":
        transform = "後置詞(Ng)"
    elif pattern == "VCL":
        transform = "動作接地方賓語動詞(VCL)"
    elif pattern == "Nes":
        transform = "特指定詞(Nes)"
    elif pattern == "Nd":
        transform = "時間詞(Nd)"
    elif pattern == "Dk":
        transform = "句副詞(Dk)"
    elif pattern == "VB":
        transform = "動作類及物動詞(VB)"
    elif pattern == "Cab":
        transform = "連接詞(Cab)"
    elif pattern == "A":
        transform = "非謂形容詞(A)"
    elif pattern == "Dfb":
        transform = "動詞後程度副詞(Dfb)"
    elif pattern == "Nv":
        transform = "名物化動詞(Nv)"
    elif pattern == "Cba":
        transform = "連接詞(Cba)"
    elif pattern == "VF":
        transform = "動作謂賓動詞(VF)"
    elif pattern == "VAC":
        transform = "動作使動動詞(VAC)"
    elif pattern == "I":
        transform = "感嘆詞(I)"
    elif pattern == "DM":
        transform = "定量式(DM)"
    elif pattern == "Neqb":
        transform = "後置數量定詞(Neqb)"
    elif pattern == "FW":
        transform = "外文(FW)"
    return transform

total_comments = len(word_sentence_list)
for index in range(total_comments):
    for word, tag in zip(word_sentence_list[index], pos_sentence_list[index]):
        tagged_words[to_visualize(tag)][word] += 1

### 詞頻統計 ###
all_pos_dict = {tag: dict(sorted(words.items(), key=lambda item: item[1], reverse=True)) for tag, words in tagged_words.items()}
write_json(all_pos_dict, 'results/all_pos.json')

#### 斷詞分析 ###
all_analysis_dict = []
for i, sentence in enumerate(sentence_list):
    analysis = {
        '句子': sentence,
        '斷詞': [],
        '詞性': []
    }
    for _word, _pos in zip(word_sentence_list[i], pos_sentence_list[i]):
        analysis['斷詞'].append(_word)
        analysis['詞性'].append(_pos)
    all_analysis_dict.append(analysis)
write_json(all_analysis_dict, 'results/all_analysis.json')

print(f'\r已將分析結果輸出至 json 文件中。')

# print(json.dumps(sentence_tag_list, ensure_ascii=False, indent=4))

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

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

print(f'\n【✅已完成】CKIP Tagger | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | 過濾留言數: {len(filtered_list)}\n')

database.close()
