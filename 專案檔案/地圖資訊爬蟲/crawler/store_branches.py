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
        print(f'\r正在取得商家週邊的其他商家({i+1}/{len(old_urls)})...\n', end='')
        driver.get(old_urls[i])
        search_and_scroll(connection, driver, '餐廳')

    # 計算時間差
    TIME_DIFFERENCE = datetime.now() - START_TIME
    MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

    print(f'\r【✅已完成】耗時:{MINUTES_ELAPSE:.2f}分鐘', end='')

    crawler_exit(driver, connection)
