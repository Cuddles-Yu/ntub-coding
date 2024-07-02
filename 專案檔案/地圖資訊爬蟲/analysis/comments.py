# 匯入套件
import jieba
import re
from 地圖資訊爬蟲.crawler.tables import Keyword
import 地圖資訊爬蟲.crawler.module.core_database as db
import 地圖資訊爬蟲.crawler.module.modify_database as mdb

TRAINING = False
TO_FILE = False

connection = db.connect(use_database=True)
if connection is None: exit()

user_dictionary_path = 'dictionaries/user.txt'
symbol_dictionary_path = 'dictionaries/symbol.txt'
level_dictionary_path = 'dictionaries/level.txt'
exception_dictionary_path = 'dictionaries/exception.txt'
adjective_dictionary_path = 'dictionaries/adjective.txt'
keywords_dictionary_path = 'dictionaries/keywords.txt'
log_path = 'log.txt'

jieba.load_userdict(user_dictionary_path)

with open(exception_dictionary_path, 'r', encoding='utf-8') as f:
    load_exception = f.read().split('\n')
with open(symbol_dictionary_path, 'r', encoding='utf-8') as f:
    load_symbol = f.read().split('\n')
with open(level_dictionary_path, 'r', encoding='utf-8') as f:
    load_level = f.read().split('\n')
with open(adjective_dictionary_path, 'r', encoding='utf-8') as f:
    load_adjective = f.read().split('\n')
with open(keywords_dictionary_path, 'r', encoding='utf-8') as f:
    load_keywords = f.read().split('\n')

content = ''

word_count_list = {}

def is_emoji(character: str) -> bool:
    emoji_pattern = re.compile(
        "["
        "\U0001F600-\U0001F64F"  # Emoticons
        "\U0001F300-\U0001F5FF"  # Symbols & Pictographs
        "\U0001F680-\U0001F6FF"  # Transport & Map Symbols
        "\U0001F700-\U0001F77F"  # Alchemical Symbols
        "\U0001F780-\U0001F7FF"  # Geometric Shapes Extended
        "\U0001F800-\U0001F8FF"  # Supplemental Arrows-C
        "\U0001F900-\U0001F9FF"  # Supplemental Symbols and Pictographs
        "\U0001FA00-\U0001FA6F"  # Chess Symbols
        "\U0001FA70-\U0001FAFF"  # Symbols and Pictographs Extended-A
        "\U00002702-\U000027B0"  # Dingbats
        "]+",
        flags=re.UNICODE
    )
    return bool(emoji_pattern.match(character))

def cut_sentence(target: str, mode: str = '預設') -> list:
    match mode:
        case '全部': return list(jieba.cut(target, cut_all=True))
        case '精確': return list(jieba.cut(target, cut_all=False))
        case '預設': return list(jieba.cut(target))
        case '搜尋': return list(jieba.cut_for_search(target))

# 將字典資料依據其value值重新排序(由大到小)
def sort_by_value(dictionary: dict) -> dict:
    return dict(sorted(dictionary.items(), key=lambda item: item[1], reverse=True))


# 宣告字串
current = 0
stores = mdb.select_table_value_by_column(connection, 'DISTINCT name, id', 'stores')
names, ids = [store[0] for store in stores], [store[1] for store in stores]
for i in range(len(names)):
    current += 1
    word_count_list = {}
    comments = mdb.select_table_value_by_where(connection, 'contents', 'comments', 'store_id', ids[i])
    if comments:
        for comment in comments:
            # 斷詞並加入到字典列表中計算頻率
            sentence = cut_sentence(comment)
            for word in sentence:
                if (
                    word.strip() != '' and
                    not is_emoji(word.strip()) and
                    not word.isnumeric() and
                    word not in load_exception and
                    word not in load_symbol and
                    word not in load_level and
                    word not in load_adjective
                ):
                    if word in word_count_list:
                        word_count_list[word] += 1
                    else:
                        word_count_list[word] = 1

        if TRAINING:
            print(f'【{names[i]}】\n{comments}\n---={sort_by_value(word_count_list)}\n\n')
            for w in sort_by_value(word_count_list):
                if w not in load_exception and w not in load_symbol and w not in load_level and w not in load_adjective and w not in load_keywords:
                    print(f'{w} [0:保留 1:排除 2:程度 3:符號 4:形容] ', end='')
                    match input():
                        case '0':
                            with open(keywords_dictionary_path, 'a', encoding='utf-8') as f:
                                f.write(w + '\n')
                        case '1':
                            with open(exception_dictionary_path, 'a', encoding='utf-8') as f:
                                f.write(w + '\n')
                        case '2':
                            with open(level_dictionary_path, 'a', encoding='utf-8') as f:
                                f.write(w + '\n')
                        case '3':
                            with open(symbol_dictionary_path, 'a', encoding='utf-8') as f:
                                f.write(w + '\n')
                        case '4':
                            with open(adjective_dictionary_path, 'a', encoding='utf-8') as f:
                                f.write(w + '\n')
                        case '-1':
                            exit()
        else:
            words = sort_by_value(word_count_list)
            print(f'\r正在儲存 -> 第 {current} 個, 共 {len(stores)} 個 | {names[i]}', end='')
            if TO_FILE:
                content += f'【{names[i]}】\n{comments}\n---={words}\n\n'
            else:
                for w, v in list(words.items())[:20]:
                    Keyword.Keyword(
                        store_id=ids[i],
                        word=w,
                        count=v,
                        source='comment'
                    ).insert_if_not_exists(connection)


if not TRAINING and TO_FILE:
    if TO_FILE:
        print('\r已完成評論關鍵字分析')
        with open(log_path, 'w+', encoding='utf-8') as f:
            f.write(content)
    else:
        print('\r已匯入關鍵字分析結果至資料庫')
