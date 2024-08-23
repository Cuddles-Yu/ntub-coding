import json
import re
from collections import defaultdict
from datetime import datetime

from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from ckip_transformers.nlp import CkipWordSegmenter, CkipPosTagger, CkipNerChunker

database = SqlDatabase('mapdb', 'root', '11236018')

START_TIME = datetime.now()

RESULT_OUTPUT = 'results/compare_to.json'

# Initialize drivers
ws_driver = CkipWordSegmenter(model="bert-base", device=0)
pos_driver = CkipPosTagger(model="bert-base", device=0)
ner_driver = CkipNerChunker(model="bert-base", device=0)

# Input text
sentence_list = database.fetch_column('all', 0, f'''
   SELECT contents FROM comments AS c
   LEFT JOIN stores AS s ON c.store_id = s.id
   WHERE contents IS NOT NULL
   ORDER BY `store_id`, `index`
   LIMIT 1000
''')
# 過濾文本，移除非中文字符並去掉換行符號
filtered_list = [re.sub(r'[^\u4e00-\u9fff，。；]', '', sentence.replace('\n', '')) for sentence in sentence_list]

# Run pipeline
ws = ws_driver(filtered_list)
pos = pos_driver(ws)
ner = ner_driver(filtered_list)

# Enable sentence segmentation
ws = ws_driver(filtered_list, use_delim=True)
ner = ner_driver(filtered_list, use_delim=True)
pos = pos_driver(ws, delim_set='\n\t')

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

total_comments = len(filtered_list)
for index in range(total_comments):
    for word, tag in zip(ws[index], pos[index]):
        tagged_words[to_visualize(tag)][word] += 1

sentence_tag_list = {tag: dict(sorted(words.items(), key=lambda item: item[1], reverse=True)) for tag, words in tagged_words.items()}

with open(RESULT_OUTPUT, 'w', encoding='utf-8') as f:
    json.dump(sentence_tag_list, f, ensure_ascii=False, indent=4)
    print(f'\r已將分析結果輸出至 data.json 文件中。')

combine = ''
for sentence, sentence_ws, sentence_pos, sentence_ner in zip(sentence_list, ws, pos, ner):
    combine += f'\n"{sentence}"\n'
    for _word, _pos in zip(sentence_ws, sentence_pos):
        combine += f'{_word}({_pos})　'
    combine += '\n'
    for entity in sorted(sentence_ner):
        combine += f'{entity}\n'

with open('results/words.txt', 'w', encoding='utf-8') as f:
    f.write(combine)

# print(json.dumps(sentence_tag_list, ensure_ascii=False, indent=4))

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

print(f'\n【✅已完成】CKIP Transformer | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | 過濾留言數: {len(filtered_list)}\n')

database.close()
