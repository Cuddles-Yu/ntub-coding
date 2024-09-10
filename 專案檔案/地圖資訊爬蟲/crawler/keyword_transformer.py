import webbrowser
from 地圖資訊爬蟲.crawler.module.color_code import ColorCode
from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions import SqlDatabase

class BreakLoopException(Exception):
    pass

class Classification(Enum):
    keywords = '關鍵字'
    dishes = '餐點'
    check = '觀察'
    ignore = '忽略'
    adjective = '形容詞'
    foreign = '外文'
    transform = '轉換'

JSON_FILE = f'doc/KEYWORDS.json'

if __name__ == '__main__':
    database = SqlDatabase.SqlDatabase('mapdb', 'root', '11236018')
    ### 讀取檔案 ###
    data = load_json(JSON_FILE)
    transform_dict = data.get(Classification.transform.value, {})
    keywords_list = data.get(Classification.keywords.value, [])
    dishes_list = data.get(Classification.dishes.value, [])
    check_list = data.get(Classification.check.value, [])
    adjective_list = data.get(Classification.adjective.value, [])
    ignore_list = data.get(Classification.ignore.value, [])
    foreign_list = data.get(Classification.foreign.value, [])

    try:
        print(f"進行'關鍵字keywords'轉換 [*結束;0忽略;1餐點;2關鍵字;3形容詞;4外文;-觀察]\n")
        ### 劃分資料 ###
        for i, (target, trans) in enumerate(transform_dict.items()):
            if not database.is_value_exists('keywords', word=transform(escape_quotes(target))): continue
            print(f'{ColorCode.DARK_BLUE}進度[{i+1}/{len(transform_dict.items())}] {target} -> {trans} {ColorCode.DEFAULT}', end='')
            control = input()
            match control:
                case '0':
                    if trans not in ignore_list: data[Classification.ignore.value].append(trans)
                case '1':
                    if trans not in dishes_list: data[Classification.dishes.value].append(trans)
                case '2':
                    if trans not in keywords_list: data[Classification.keywords.value].append(trans)
                case '3':
                    if trans not in adjective_list: data[Classification.adjective.value].append(trans)
                case '4':
                    if trans not in foreign_list: data[Classification.foreign.value].append(trans)
                case '-':
                    if trans not in check_list: data[Classification.check.value].append(trans)
                case _:
                    raise BreakLoopException

            database.update('keywords', {'word': transform(escape_quotes(trans))}, {'word': transform(escape_quotes(target))})
            for target_list in [keywords_list, dishes_list, check_list, ignore_list, foreign_list, adjective_list]:
                if target in target_list:
                    target_list.remove(target)
                    print(f'在裡面{target}')

    except BreakLoopException:
        pass

    print(f"\n已將'關鍵字'劃分結果輸出至 -> {JSON_FILE}")
    write_json(data, JSON_FILE)
