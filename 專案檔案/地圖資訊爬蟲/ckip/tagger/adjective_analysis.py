from typing import List
from 地圖資訊爬蟲.ckip.module.functions import *
from 地圖資訊爬蟲.crawler.tables.Mark import Mark

pos_target_data = load_json(r'D:\ntub\project\repository\ntub-coding\專案檔案\地圖資訊爬蟲\ckip\doc\pos_target.json')
pos_correction_data = load_json(r'D:\ntub\project\repository\ntub-coding\專案檔案\地圖資訊爬蟲\ckip\doc\pos_correction.json')

TARGET_TAGS = ['環境', '產品', '服務', '售價']
STATE_TAGS = ['正面', '負面', '中立', '喜好']

negation_words = ["不", "沒", "無", "非"]
negative_intensifier_words = ["太", "過", "超"]

NOUN_POS_TAGS = ["Na"]
ADVERB_POS_TAGS = ["Dfa", "D", "VJ"]

marks: List[Mark] = []
scores = {
    "環境": {"正面": 0, "負面": 0, "中立": 0, "喜好": 0},
    "產品": {"正面": 0, "負面": 0, "中立": 0, "喜好": 0},
    "服務": {"正面": 0, "負面": 0, "中立": 0, "喜好": 0},
    "售價": {"正面": 0, "負面": 0, "中立": 0, "喜好": 0}
}

def correct_pos(target_word, target_pos):
    """
    檢查並修正詞性。
    Parameters:
        target_word (str): 需要檢查的詞。
        target_pos (str): 該詞的原始詞性。
    Returns:
        str: 修正後的詞性。
    """
    for new_pos, words_list in pos_correction_data.items():
        if target_word.upper() in words_list:
            return new_pos
    return target_pos

def get_adjective_type(word):
    """
    查找形容詞的類型。
    Parameters:
        word (str): 要查找的形容詞。
    Returns:
        str: 該形容詞的類型 (正面, 負面, 中立, 喜好, 忽略) 或 None。
    """
    for sentiment in ["正面", "負面"]:
        for category, words in pos_target_data["形容詞"][sentiment].items():
            if word in words:
                return sentiment
    for sentiment in ["中立", "喜好", "忽略"]:
        if word in pos_target_data["形容詞"][sentiment]:
            return sentiment
    return None

def advanced_search_for_noun(words, pos_tags, start_idx, used_nouns_positions):
    """
    進階搜尋，從 VH + DE 開始，向後搜尋名詞，收集所有中間的詞直到找到名詞。
    Parameters:
        words (list): 句中的斷詞。
        pos_tags (list): 對應的詞性標籤。
        start_idx (int): 搜尋的起始索引。
        used_nouns_positions (set): 已使用名詞的位置集合。
    Returns:
        tuple: 完整的詞組和名詞的位置索引。如果沒有找到，返回 (None, None)。
    """
    phrase = []
    for i in range(start_idx, len(words)):
        phrase.append(words[i])
        if pos_tags[i] in NOUN_POS_TAGS and (i, i) not in used_nouns_positions:
            return ' '.join(phrase), i
    return None, None

def analyze_sentence(sentence_splits):
    """
    分析每個句子的形容詞，從最後一個句子開始處理。
    Parameters:
        sentence_splits (list): 已經斷詞的句子列表。
    Returns:
        None
    """
    used_nouns_positions = set()
    for current_sentence_idx in range(len(sentence_splits) - 1, -1, -1):
        sentence = sentence_splits[current_sentence_idx]
        store_id = sentence["商家"]
        comment_id = sentence["留言"]
        words = sentence["斷詞"]
        pos_tags = sentence["詞性"]

        for i, (word, pos) in enumerate(zip(words, pos_tags)):
            if pos == "VH":
                adjective_type = get_adjective_type(word)

                if adjective_type == "忽略":
                    print(f'{ColorCode.DARK_GRAY}忽略形容詞: {word}{ColorCode.DEFAULT}')
                    continue

                adverb = None
                if i > 0 and pos_tags[i - 1] in ADVERB_POS_TAGS:
                    adverb = words[i - 1]

                is_negation = i > 0 and words[i - 1] in negation_words
                is_negative_intensifier = i > 0 and words[i - 1] in negative_intensifier_words

                if is_negation:
                    adjective_type = "負面" if adjective_type == "正面" else "正面"

                if is_negative_intensifier and adjective_type == "喜好":
                    adjective_type = "負面"

                # if i + 1 < len(words) and pos_tags[i + 1] == "DE":
                #     advanced_phrase, noun_idx = advanced_search_for_noun(words, pos_tags, i + 2, used_nouns_positions)
                #     if advanced_phrase and noun_idx:
                #         inferred_category, color_code = infer_category_from_noun(advanced_phrase.split()[-1])
                #         phrase = f'{adverb or ""}{word} {advanced_phrase}'
                #         print(f'{color_code}{phrase} -> {inferred_category} ({adjective_type}){ColorCode.DEFAULT}')
                #         used_nouns_positions.add((current_sentence_idx, noun_idx))
                #         save_mark(store_id, comment_id, phrase, inferred_category, adjective_type)
                #         update_score(inferred_category, adjective_type)
                #         continue

                noun_idx, noun, has_category = find_noun_with_index(words, pos_tags, i, adjective_type, used_nouns_positions, sentence_splits, current_sentence_idx)
                if noun:
                    full_noun_phrase = expand_noun_phrase(noun_idx, noun, words, pos_tags, current_sentence_idx, used_nouns_positions)
                    inferred_category, color_code = infer_category_from_noun(noun)
                    obj = full_noun_phrase
                    adj = f'{adverb or ""}{word}'
                    phrase = f'{full_noun_phrase}{adverb or ""}{word}' if noun_idx < i else f'{adverb or ""}{word}{full_noun_phrase}'
                    used_nouns_positions.add((current_sentence_idx, noun_idx))
                else:
                    inferred_category, color_code = infer_category_from_adjective(word, adjective_type)
                    obj = ''
                    adj = f'{adverb or ""}{word}'
                    phrase = f'{adverb or ""}{word}'

                print(f'{color_code}{phrase} -> {inferred_category} ({adjective_type}){ColorCode.DEFAULT}')
                save_mark(store_id, comment_id, obj, adj, inferred_category, adjective_type)
                update_score(inferred_category, adjective_type)

def find_noun_with_index(words, pos_tags, index, adjective_type, used_nouns_positions, sentence_splits, current_sentence_idx):
    """
    搜尋形容詞周圍的名詞，並返回名詞的索引和值。
    Parameters:
        words (list): 句中的斷詞。
        pos_tags (list): 對應的詞性標籤。
        index (int): 形容詞的位置。
        adjective_type (str): 形容詞的類型。
        used_nouns_positions (set): 已使用名詞的位置集合。
        sentence_splits (list): 句子斷詞的集合。
        current_sentence_idx (int): 當前句子的索引。
    Returns:
        tuple: 名詞的索引、名詞值和類別。如果無法找到名詞，返回 (None, None)。
    """
    has_category = None
    for i in range(index - 1, -1, -1):
        if pos_tags[i] in NOUN_POS_TAGS and (current_sentence_idx, i) not in used_nouns_positions:
            inferred_category = infer_category_from_noun(words[i])
            if inferred_category:
                has_category = inferred_category
            return i, words[i], has_category
        if pos_tags[i] == "VH":
            return None, None, has_category

    if not has_category:
        for i in range(index + 1, len(words)):
            if pos_tags[i] in NOUN_POS_TAGS and (current_sentence_idx, i) not in used_nouns_positions:
                inferred_category = infer_category_from_noun(words[i])
                if inferred_category:
                    has_category = inferred_category
                return i, words[i], has_category
            if pos_tags[i] == "VH":
                return None, None, has_category

    for prev_idx in range(current_sentence_idx - 1, max(-1, current_sentence_idx - 3), -1):
        prev_sentence = sentence_splits[prev_idx]
        prev_words = prev_sentence["斷詞"]
        prev_pos_tags = prev_sentence["詞性"]
        for i in range(len(prev_words) - 1, -1, -1):
            if prev_pos_tags[i] in NOUN_POS_TAGS and (prev_idx, i) not in used_nouns_positions:
                inferred_category = infer_category_from_noun(prev_words[i])
                if inferred_category:
                    has_category = inferred_category
                return i, prev_words[i], has_category

    return None, None, has_category

def expand_noun_phrase(start_idx, noun, words, pos_tags, current_sentence_idx, used_nouns_positions):
    """
    合併相連的名詞，並處理對等連接詞 (Caa)。
    Parameters:
        start_idx (int): 起始名詞索引。
        noun (str): 當前名詞。
        words (list): 句中的斷詞。
        pos_tags (list): 對應的詞性標籤。
        current_sentence_idx (int): 當前句子的索引。
        used_nouns_positions (set): 已使用名詞的位置集合。
    Returns:
        str: 合併後的完整名詞短語。
    """
    noun_phrase = [noun]
    for i in range(start_idx - 1, -1, -1):
        if i < 0 or i >= len(pos_tags):
            break
        if pos_tags[i] in NOUN_POS_TAGS and (current_sentence_idx, i) not in used_nouns_positions:
            noun_phrase.insert(0, words[i])
        elif pos_tags[i] == "Caa" and i - 1 >= 0 and pos_tags[i - 1] in NOUN_POS_TAGS and (current_sentence_idx, i - 1) not in used_nouns_positions:
            noun_phrase.insert(0, words[i - 1] + words[i])
            used_nouns_positions.add((current_sentence_idx, i - 1))
        else:
            break

    for i in range(start_idx + 1, len(words)):
        if i >= len(pos_tags):
            break
        if pos_tags[i] in NOUN_POS_TAGS and (current_sentence_idx, i) not in used_nouns_positions:
            noun_phrase.append(words[i])
        elif pos_tags[i] == "Caa" and i + 1 < len(pos_tags) and pos_tags[i + 1] in NOUN_POS_TAGS and (current_sentence_idx, i + 1) not in used_nouns_positions:
            noun_phrase.append(words[i] + words[i + 1])
            used_nouns_positions.add((current_sentence_idx, i + 1))
        else:
            break

    return ''.join(noun_phrase)

def get_color_code(category):
    """
    根據類別獲取顏色代碼。
    Parameters:
        category (str): 名詞的類別。
    Returns:
        str: 對應的顏色代碼。
    """
    return ColorCode.UNDERLINE+ColorCode.GREEN if category in TARGET_TAGS else ColorCode.DARK_RED

def infer_category_from_noun(noun):
    """
    根據名詞推斷其類別。
    Parameters:
        noun (str): 名詞。
    Returns:
        tuple: 名詞的類別和顏色代碼。
    """
    for category, nouns in pos_target_data["受詞"].items():
        if noun.upper() in nouns:
            return category, get_color_code(category)
    return "無", ''

def infer_category_from_adjective(adj, adj_type):
    """
    根據形容詞推斷其類別。
    Parameters:
        adj (str): 形容詞。
        adj_type (str): 形容詞的類型。
    Returns:
        tuple: 形容詞的類別和顏色代碼。
    """
    for adj_type in ["正面", "負面"]:
        for category, words in pos_target_data["形容詞"][adj_type].items():
            if adj in words: return category, get_color_code(category)
    return "無", ''

def update_score(category, adjective_type):
    """
    更新形容詞的數量，包含正面、負面、中立和喜好。

    Parameters:
        category (str): 名詞的類別。
        adjective_type (str): 形容詞的類型。

    Returns:
        None
    """
    if category in scores:
        if adjective_type in scores[category]:
            scores[category][adjective_type] += 1

def save_mark(store, comment, obj, adjective, target, state):
    """
    保存分析結果到 marks 列表中。
    Parameters:
        store (int): 商家id
        comment (int): 留言id
        obj (str): 分析過的受詞
        adjective (str): 分析過的形容詞組
        target (str): 該句子的指標類別 (環境, 產品, 等)
        state (str): 該句子的狀態 (正面, 負面, 等)
    Returns:
        None
    """
    if target in TARGET_TAGS and state in STATE_TAGS:
        marks.append(Mark(
            store_id=store,
            comment_id=comment,
            sid=None,
            obj=obj,
            adjective=adjective,
            target=target,
            state=state
        ))
