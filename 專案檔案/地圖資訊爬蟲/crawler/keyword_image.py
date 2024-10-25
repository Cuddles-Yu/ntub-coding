### 匯入模組 ###
from datetime import datetime

from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions.EdgeDriver import EdgeDriver
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

from selenium.webdriver.common.by import By


def search_url(name: str, kw: str, mode: str):
    #  cr=countryTW imgtype=photo
    query_url = 'https://www.google.com/search?udm=2&imgtype=photo&q='
    name = name.replace('-', ' ')
    if mode == 'force':
        query_url += f'"{name}" "{kw}" -菜單'
    elif mode == 'medium':
        query_url += f'{name} "{kw}" -菜單'
    elif mode == 'normal':
        query_url += f'{name} {kw} -菜單'
    elif mode =='static':
        query_url += f'"{kw}" -菜單'
    return query_url.replace(' ', '+')

def trying(_driver, _store_name, _keyword, modes):
    for mode in modes:
        # 瀏覽器載入指定的連結
        _driver.get(search_url(_store_name, _keyword, mode))
        random_delay(1, 3, 2)
        element = _driver.wait_for_click_index(By.CLASS_NAME, 'eA0Zlc', index=0)
        random_delay(1, 3, 2)
        if element: return element
    return None

def search(_driver, _store_name, _keyword, _store_id):
    search_result = trying(_driver, _store_name, _keyword, modes=['medium', 'normal', 'static'])
    if not search_result: return None, None
    random_delay(1, 3, 2)
    # 取得來源與圖片連結
    image = _driver.wait_for_element(By.CLASS_NAME, 'YsLeY')
    image_url = image.find_element(By.TAG_NAME, 'img').get_attribute('src')
    source_url = image.get_attribute('href')
    random_delay(1, 3, 2)
    if __name__ == "__main__": update(image_url, source_url, _store_id, _keyword)
    return image_url, source_url

def update(_image_url, _source_url, _store_id, _keyword):
    database.execute(f'''
        UPDATE keywords
        SET image_url = {transform(_image_url)}, source_url = {transform(_source_url)}
        WHERE store_id = {_store_id} and word = {transform(_keyword)}
    ''')

if __name__ == '__main__':
    # 連線資料庫
    database = SqlDatabase('mapdb', 'root', '11236018')
    driver = EdgeDriver(database)
    # 取得商家關鍵字
    result = database.fetch('all', '''
        SELECT name, word, id FROM stores AS s, keywords AS k
        WHERE s.id = k.store_id and source = 'recommend' and (image_url IS NULL or source_url IS NULL)
        ORDER BY count DESC
    ''')
    if result:
        print(f'正在爬取推薦餐點圖示至資料庫 -> {database.name}\n')
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

    driver.exit()
