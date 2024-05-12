# 匯入套件
import jieba

file_path = 'dictionaries/custom.txt'
jieba.load_userdict(file_path)

word_count_list = {}

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
s = '''買阿極造型的提拉米蘇口味蛋糕，回到家要慶生變身為草莓口味'''

# 斷詞並加入到字典列表中計算頻率
sentence = cut_sentence(s)

for word in sentence:
    if word in word_count_list:
        word_count_list[word] += 1
    else:
        word_count_list[word] = 1

print(sentence)
print(sort_by_value(word_count_list))
