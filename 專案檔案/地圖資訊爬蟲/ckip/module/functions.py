import json
from 地圖資訊爬蟲.crawler.module.color_code import ColorCode

def load_json(file_path):
    try:
        with open(file_path, 'r', encoding='utf-8') as file:
            data = json.load(file)
            return data
    except Exception as e:
        return {}

def write_json(data, file_path):
    try:
        with open(file_path, 'w', encoding='utf-8') as file:
            json.dump(data, file, ensure_ascii=False, indent=4)
    except Exception as e:
        pass

def pos_color(pos_code: str):
    if pos_code == 'VH': return f'{ColorCode.YELLOW}{ColorCode.BOLD}'
    if pos_code == 'Na': return f'{ColorCode.BLUE}{ColorCode.BOLD}'
    if pos_code == 'Dfa': return f'{ColorCode.RED}{ColorCode.BOLD}'
    return None

def is_keyword_in_pos(data, keyword):
    for key, sub in data.items():
        if keyword in sub: return True
    return False

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

def combine_to_dict(word_list, pos_list) -> dict:
    return {"斷詞": word_list, "詞性": pos_list}

def combine_by_pos(word_list, pos_list, pos):
    combined_words = []
    combined_pos = []
    i = 0
    while i < len(word_list):
        if pos_list[i] == pos:
            temp_word = word_list[i]
            j = i + 1
            while j < len(word_list) and pos_list[j] == pos:
                temp_word += word_list[j]
                j += 1
            combined_words.append(temp_word)
            combined_pos.append(pos)
            i = j
        else:
            combined_words.append(word_list[i])
            combined_pos.append(pos_list[i])
            i += 1
    return combined_words, combined_pos

def combine_by_pattern(word_list, pos_list, pattern):
    combined_words = []
    combined_pos = []
    pattern_length = len(pattern)
    i = 0
    while i < len(word_list):
        if i + pattern_length <= len(word_list) and pos_list[i:i + pattern_length] == pattern:
            combined_word = ''.join(word_list[i:i + pattern_length])
            combined_words.append(combined_word)
            combined_pos.append(pattern[0])
            i += pattern_length
        else:
            combined_words.append(word_list[i])
            combined_pos.append(pos_list[i])
            i += 1
    return combined_words, combined_pos

