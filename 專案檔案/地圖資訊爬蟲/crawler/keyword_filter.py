import webbrowser
from 地圖資訊爬蟲.crawler.module.color_code import ColorCode
from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions import SqlDatabase
from 地圖資訊爬蟲.crawler.keyword_classification import Classification

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

    # recommend
    transferred = False
    ignore_keywords = adjective_list+ignore_list+foreign_list+keywords_list
    print(f"正在從 keywords.source 中搜尋需忽略之 'recommend' 關鍵字...")
    for i, ignore_keyword in enumerate(ignore_keywords):
        if not database.is_value_exists('keywords', source='recommend', word=ignore_keyword): continue
        if not transferred: transferred = True
        print(f"發現待轉換 'recommend' 關鍵字: {ignore_keyword}")
        database.update('keywords', {"source": "recommend-ignore"}, {"source": "recommend", "word": ignore_keyword})
    if transferred:
        print(f"\n已完成轉換 'recommend' -> 'recommend-ignore'")
    else:
        print(f"未發現任何需忽略之 'recommend' 關鍵字。")
