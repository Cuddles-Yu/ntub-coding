### 匯入模組 ###
import time
from datetime import datetime

from 地圖資訊爬蟲.crawler.module.functions import *

# 資料表
from 地圖資訊爬蟲.crawler.tables.base import *

# 資料庫操作
import 地圖資訊爬蟲.crawler.module.create_database as db
import 地圖資訊爬蟲.crawler.module.modify_database as mdb

# 網頁爬蟲
from selenium.webdriver import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.support.ui import WebDriverWait

def search(_driver, _url):
    while True:
        # 瀏覽器載入指定的連結
        _driver.get(_url)

        # 等待 Driver 瀏覽到指定頁面後，對搜尋框輸入關鍵字搜尋
        print(f'正在搜尋關鍵字 -> {SEARCH_KEYWORD}\n')
        search_box = wait_for_element(By.CLASS_NAME, driver, 'searchboxinput')
        search_box.clear()
        search_box.send_keys(SEARCH_KEYWORD)
        search_box.send_keys(Keys.ENTER)

        # 取得所有搜尋結果所在的'容器'物件
        print('\r正在取得搜尋結果...(可能會花費較多時間)', end='')
        WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
            ec.presence_of_element_located((By.CLASS_NAME, 'Nv2PK'))
        )
        container_search_result = driver.find_element(By.XPATH, '/html/body/div[2]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]')

        # 紀錄爬取評論的等待時間
        start_time = time.time()
        current_results = 0
        finish_scroll = True

        # 向下捲動瀏覽所有搜尋結果
        if ENABLE_SCROLL_DOWN:
            while True:
                if len(driver.find_elements(By.CLASS_NAME, 'HlvSq')) > 0: break
                ActionChains(driver).move_to_element(container_search_result.find_elements(By.CLASS_NAME, 'Nv2PK')[-1]).perform()
                container_search_result.send_keys(Keys.PAGE_DOWN)
                time.sleep(0.1)
                # 檢查是否持續一段時間皆未出現新的結果(卡住)
                results = driver.find_elements(By.CLASS_NAME, 'hfpxzc')
                if current_results != len(results): start_time = time.time()
                current_results = len(results)
                if time.time() - start_time > MAXIMUM_TIMEOUT:
                    finish_scroll = not HAVE_TO_GET_ALL_RESULTS
                    break

        if finish_scroll: break

    # 地圖連結
    return [title.get_attribute('href') for title in driver.find_elements(By.CLASS_NAME, 'hfpxzc')]


### 主程式 ###
if __name__ == "__main__":
    # 連線資料庫
    driver = None
    connection = db.connect(use_database=True)
    # 取得商家關鍵字
    old_urls = [url[0] for url in mdb.fetch(connection, 'all', '''
        SELECT link FROM stores
    ''')]
    # 初始化 Driver
    driver = init_driver('')
    print(f'正在執行擴增商家 -> {NAME}\n')
    START_TIME = datetime.now()
    for i in range(len(old_urls)):
        print(f'\r正在取得商家週邊的其他商家({i+1}/{len(old_urls)})...', end='')
        print(search(driver, old_urls[i]))

    # 計算時間差
    TIME_DIFFERENCE = datetime.now() - START_TIME
    MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

    print(f'\r【✅已完成】耗時:{MINUTES_ELAPSE:.2f}分鐘 | {len(result)} 組推薦餐點關鍵字', end='')

    crawler_exit(driver, connection)
