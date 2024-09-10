import webbrowser
from 地圖資訊爬蟲.crawler.module.color_code import ColorCode
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

JSON_FILE = f'doc/KEYWORDS.json'

if __name__ == '__main__':
    database = SqlDatabase.SqlDatabase('mapdb', 'root', '11236018')
    ### 讀取檔案 ###
    data = load_json(JSON_FILE)
    keywords_list = data.get(Classification.keywords.value, [])
    dishes_list = data.get(Classification.dishes.value, [])
    check_list = data.get(Classification.check.value, [])
    adjective_list = data.get(Classification.adjective.value, [])
    ignore_list = data.get(Classification.ignore.value, [])
    foreign_list = data.get(Classification.foreign.value, [])

    try:
        print(f"進行'關鍵字keywords'監督式劃分 [*結束;0忽略;1餐點;2關鍵字;3形容詞;4外文;-觀察;?/搜尋;\\檢查]\n")
        keywords = database.fetch('all', f'''
                SELECT DISTINCT word, SUM(count) AS c, COUNT(*) AS f FROM stores AS s, keywords AS k
                WHERE s.id = k.store_id and source = 'recommend'
                GROUP BY word
                ORDER BY f DESC, c DESC
        ''')
        ### 劃分資料 ###
        for i, (keyword, count, frequency) in enumerate(keywords):
            if keyword in dishes_list or keyword in keywords_list or keyword in check_list or keyword in adjective_list or keyword in ignore_list or keyword in foreign_list: continue
            if len(keyword) > 3: continue
            # if not re.search(r'[^\u4e00-\u9fa5]', keyword): continue
            while True:
                print(f'{ColorCode.DARK_BLUE}進度[{i+1}/{len(keywords)}] {keyword} {ColorCode.DEFAULT}', end='')
                control = input()
                match control:
                    case '0':
                        data[Classification.ignore.value].append(keyword)
                        break
                    case '1':
                        data[Classification.dishes.value].append(keyword)
                        break
                    case '2':
                        data[Classification.keywords.value].append(keyword)
                        break
                    case '3':
                        data[Classification.adjective.value].append(keyword)
                        break
                    case '4':
                        data[Classification.foreign.value].append(keyword)
                        break
                    case '-':
                        data[Classification.check.value].append(keyword)
                        break
                    case '\\':
                        print(f"正在檢查資料庫中包含關鍵字的留言...")
                        results = database.fetch('all', f'''
                            SELECT s.name, c.id, c.contents FROM mapdb.comments AS c
                            LEFT JOIN stores AS s ON s.id = c.store_id
                            WHERE contents LIKE '%{keyword}%'
                            LIMIT 50
                        ''')
                        if results:
                            for name, cid, contents in results: print(f'\n{{\n\t"商家名稱": "{name}", \n\t"留言id": {cid}, \n\t"留言內容": "{contents}"\n}}')
                        print()
                    case '?' | '/':
                        print(f"正在透過瀏覽器搜尋指定關鍵字...\n")
                        webbrowser.open(f'https://www.google.com/search?udm=2&q={keyword}')
                    case _:
                        raise BreakLoopException

    except BreakLoopException:
        pass

    print(f"\n已將'關鍵字'劃分結果輸出至 -> {JSON_FILE}")
    write_json(data, JSON_FILE)
