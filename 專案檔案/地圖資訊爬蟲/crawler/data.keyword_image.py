### 匯入模組 ###
import time
from datetime import datetime

from module.functions import *

# 資料表
from tables.base import *

# 資料庫操作
import module.create_database as db
import module.modify_database as mdb

# 網頁爬蟲
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.support.ui import WebDriverWait

### 主程式 ###
# 連線資料庫
connection = db.connect(use_database=True)

# 初始化 Driver
driver = init_driver('')
print(f'正在爬取推薦餐點縮圖 -> {NAME}\n')

result = mdb.fetch(connection, 'all', '''
    SELECT name, word, id FROM stores AS s, keywords AS k
    WHERE s.id = k.store_id and source = 'recommend' and (k.image_url IS NULL or k.source_url IS NULL)
    ORDER BY id, count DESC
''')

START_TIME = datetime.now()

URL = 'https://www.google.com/search?udm=2&q='

for i in range(len(result)):
    print(f'\r正在儲存縮圖與來源連結({i}/{len(result)})...', end='')
    store_name, keyword, store_id = result[i][0], result[i][1], result[i][2]
    # 瀏覽器載入指定的商家地圖連結
    driver.get(URL+store_name.replace(' ', '+')+'+'+keyword.replace(' ', '+'))
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'eA0Zlc'))
    )
    element = driver.find_element(By.CLASS_NAME, 'eA0Zlc')
    element.click()
    time.sleep(0.5)
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'YsLeY'))
    )
    image = driver.find_element(By.CLASS_NAME, 'YsLeY')
    source_url = image.get_attribute('href')
    image_url = image.find_element(By.TAG_NAME, 'img').get_attribute('src')
    mdb.execute(connection, f'''
        UPDATE keywords
        SET image_url = {transform(source_url)}, source_url = {transform(image_url)}
        WHERE store_id = {store_id} and word = {transform(keyword)}
    ''')
    time.sleep(1)

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

print(f'\r【✅已完成】耗時:{MINUTES_ELAPSE:.2f}分鐘 | {len(result)} 組推薦餐點關鍵字', end='')
crawler_exit(driver, connection)
