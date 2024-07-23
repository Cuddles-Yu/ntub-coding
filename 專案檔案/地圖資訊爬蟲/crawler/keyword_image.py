### 匯入模組 ###
import random
import time
from datetime import datetime

from 地圖資訊爬蟲.crawler.module.functions import *

# 資料表
from 地圖資訊爬蟲.crawler.tables.base import *

# 資料庫操作
import 地圖資訊爬蟲.crawler.module.create_database as db
import 地圖資訊爬蟲.crawler.module.modify_database as mdb

# 網頁爬蟲
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.support.ui import WebDriverWait

def search_url(name: str, kw: str, mode: str):
    #  cr=countryTW imgtype=photo
    query_url = 'https://www.google.com/search?udm=2&imgtype=photo&cr=countryTW&q='
    name = name.replace('-', ' ')
    match mode:
        case 'force':
            query_url += f'"{name}" "{kw}" -菜單'
        case 'medium':
            query_url += f'{name} "{kw}" -菜單'
        case 'normal':
            query_url += f'{name} {kw} -菜單'
        case 'static':
            query_url += f'"{kw}" -菜單'
    return query_url.replace(' ', '+')

def trying(_driver, _store_name, _keyword, modes):
    for mode in modes:
        # 瀏覽器載入指定的連結
        _driver.get(search_url(_store_name, _keyword, mode))
        random_delay(1, 5, 2)
        element = wait_for_click_index(By.CLASS_NAME, _driver, 'eA0Zlc', index=1)
        random_delay(3, 5, 2)
        if element: return element
    return None

def search(_driver, _store_name, _keyword, _store_id):
    search_result = trying(_driver, _store_name, _keyword, modes=['force', 'medium', 'normal', 'static'])
    if not search_result: return None, None
    random_delay(1, 5, 2)
    # 取得來源與圖片連結
    image = wait_for_element(By.CLASS_NAME, _driver, 'YsLeY')
    image_url = image.find_element(By.TAG_NAME, 'img').get_attribute('src')
    source_url = image.get_attribute('href')
    random_delay(3, 5, 2)
    if __name__ == "__main__": update(image_url, source_url, _store_id, _keyword)
    return image_url, source_url

def update(_image_url, _source_url, _store_id, _keyword):
    mdb.execute(connection, f'''
        UPDATE keywords
        SET image_url = {transform(_image_url)}, source_url = {transform(_source_url)}
        WHERE store_id = {_store_id} and word = {transform(_keyword)}
    ''')

### 主程式 ###
if __name__ == "__main__":
    # 連線資料庫
    driver = None
    connection = db.connect(use_database=True)
    # 取得商家關鍵字
    result = mdb.fetch(connection, 'all', '''
        SELECT name, word, id FROM stores AS s, keywords AS k
        WHERE s.id = k.store_id and source = 'recommend' and (image_url IS NULL or source_url IS NULL)
        ORDER BY count DESC
    ''')
    if result:
        # 初始化 Driver
        driver = init_driver('')
        print(f'正在爬取推薦餐點圖示至資料庫 -> {NAME}\n')
        START_TIME = datetime.now()
        for i in range(len(result)):
            print(f'\r正在儲存縮圖與來源連結({i+1}/{len(result)})...', end='')
            store_name, keyword, store_id = result[i][0], result[i][1], result[i][2]
            search(driver, store_name, keyword, store_id)
        # 計算時間差
        TIME_DIFFERENCE = datetime.now() - START_TIME
        MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60
        print(f'\r【✅已完成】耗時:{MINUTES_ELAPSE:.2f}分鐘 | {len(result)} 組推薦餐點關鍵字', end='')
    else:
        print(f'未找到需進行爬取推薦餐點縮圖的關鍵字。')

    # crawler_exit(driver, connection)
