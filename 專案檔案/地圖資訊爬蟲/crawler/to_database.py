### 匯入模組 ###
import re
import time
from module.tables import *
import 地圖結果資料庫.python.core_database as db
from selenium import webdriver
from selenium.webdriver import Keys
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.support.ui import WebDriverWait


### 系統常數設定 ###
SEARCH_KEYWORD = '臺北商業大學 鹹水雞'
FILE_PATH = f'{SEARCH_KEYWORD.strip().replace(' ', '')}的搜尋結果.csv'
ENABLE_SCROLL_DOWN = False

BUTTON_TYPE = {
    '撰寫評論': 0,
    '查詢評論': 1,
    '排序評論': 2
}
ORDER_TYPE = {
    '最相關': 0,
    '最新': 1,
    '評分最高': 2,
    '評分最低': 3
}
TAB_TYPE = {
    '總覽': 0,
    '評論': 1,
    '簡介': 2
}


### 函式 ###
def get_split_from_address(address):
    matches = re.match(r'(?P<postal>\d+)(?P<city>\D+[縣市])(?P<district>\D+[鄉鎮市區])(?P<detail>.+)', address)
    return matches.group('postal'), matches.group('city'), matches.group('district'), matches.group('detail')

def switch_to_order(order_type: str):
    print(f'\r正在切換至{order_type}評論...', end='')
    # 功能按鈕 - 撰寫/查詢/[排序評論]
    WebDriverWait(driver, 10).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'S9kvJb'))
    )
    order_button = driver.find_elements(By.CLASS_NAME, 'S9kvJb')
    order_button[BUTTON_TYPE['排序評論']].click()
    # 排序選單 - 最相關/最新/評分最高/評分最低
    WebDriverWait(driver, 10).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'fxNQSd'))
    )
    order_list = driver.find_elements(By.CLASS_NAME, 'fxNQSd')
    order_list[ORDER_TYPE[order_type]].click()
    WebDriverWait(driver, 10).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'jftiEf'))
    )

def get_comments(store_name: str) -> int:
    sum_score = 0
    # 沒留言的不會爬
    filtered_comments = [
        comment for comment in commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
        if len(comment.find_elements(By.CLASS_NAME, 'MyEned')) > 0
    ]
    for index in range(len(filtered_comments)):
        try:
            sum_score += int(
                filtered_comments[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
            user_id = filtered_comments[index].find_element(By.CLASS_NAME, 'al6Kxe').get_attribute('data-href').split('/')[-2]

            # 取得評論者結構
            level = re.search(r'ba(?P<level>\d+)', filtered_comments[index].find_element(By.CLASS_NAME, 'NBa7we').get_attribute('src'))
            user_item = User(uid=user_id, level=int(level.group('level')) + 2 if level else 0)
            # 儲存至資料庫(自動檢查是否已存在)
            user_item.insert_if_not_exists(connection)

            # 取得留言結構
            comment_item = Comment(
                store_name=store_name,
                sort=index + 1,
                contents=filtered_comments[index].find_element(By.CLASS_NAME, 'MyEned').find_element(By.CLASS_NAME, 'wiI7pd').text,
                time=filtered_comments[index].find_element(By.CLASS_NAME, 'rsqaWe').text,
                rating=int(filtered_comments[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0]),
                user_id=user_id
            )
            # 儲存至資料庫
            comment_item.insert(connection)

            print(f'\r正在儲存評論結構(%d/%d)...' % (index + 1, len(filtered_comments)), end='')
        finally:
            pass
    return sum_score

### 主程式 ###
# 連線資料庫
connection = db.connect(use_database=True)

# 初始化 Driver
print('\r正在連線到GoogleMap...', end='')
options = webdriver.EdgeOptions()
options.add_experimental_option("detach", True)
# options.add_argument("--headless")  # 不顯示視窗
driver = webdriver.Edge(options=options)
# driver.minimize_window()  # 最小化視窗
driver.get('https://www.google.com.tw/maps/preview')

# 等待 Driver 瀏覽到指定頁面後，對搜尋框輸入關鍵字搜尋
print('\r正在搜尋關鍵字...', end='')
WebDriverWait(driver, 10).until(
    ec.presence_of_element_located((By.CLASS_NAME, 'searchboxinput'))
)
search_box = driver.find_element(By.CLASS_NAME, 'searchboxinput')
search_box.send_keys(SEARCH_KEYWORD)
search_box.send_keys(Keys.ENTER)

# 取得所有搜尋結果所在的'容器'物件
print('\r正在取得搜尋結果...(可能會花費較多時間)', end='')
WebDriverWait(driver, 10).until(
    ec.presence_of_element_located((By.CLASS_NAME, 'Nv2PK'))
)
container_search_result = driver.find_element(By.XPATH, '/html/body/div[1]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]')
# 向下捲動瀏覽所有搜尋結果
if ENABLE_SCROLL_DOWN:
    while True:
        if len(driver.find_elements(By.CLASS_NAME, 'HlvSq')) > 0:
            break
        ActionChains(driver).move_to_element(container_search_result.find_elements(By.CLASS_NAME, 'Nv2PK')[-1]).perform()
        container_search_result.send_keys(Keys.PAGE_DOWN)
        time.sleep(0.1)

element_search_title = driver.find_elements(By.CLASS_NAME, 'hfpxzc')
element_search_img = driver.find_elements(By.CLASS_NAME, 'Nv2PK')
# 地點名稱
names = [title.get_attribute('aria-label') for title in element_search_title]
time.sleep(5)
# 預覽圖片
images = [img.find_element(By.CLASS_NAME, 'p0Hhde').find_element(By.TAG_NAME, 'img').get_attribute('src').split('/')[-1] for img in element_search_img]
# 地圖連結
url = [title.get_attribute('href').split('/')[-1] for title in element_search_title]
# 平均評分
values = [str(value.text) for value in driver.find_elements(By.CLASS_NAME, 'MW4etd')]
# 評分總數
comment_count = [int(re.sub(r'\D', '', comment.text)) for comment in driver.find_elements(By.CLASS_NAME, 'UY7F9')]

max_count = len(element_search_title)
for i in range(max_count):
    store_item = Store(
        name=names[i],
        category=None,
        tag=None,
        preview_image=images[i],
        link=url[i],
        website=None,
        phone_number=None
    )
    rate_item = Rate(
        store_name=names[i],
        avg_ratings=float(values[i]),
        total_ratings=comment_count[i],
        total_comments=0,
        real_ratings=0.0,
        store_responses=0
    )
    location_item = Location(
        store_name=names[i],
        longitude=None,
        latitude=None,
        postal_code=None,
        city=None,
        dist=None,
        vil=None,
        details=None
    )
    is_repairing = False
    # 檢查資料庫中是否已經存在指定的商家
    if store_item.exists(connection):
        if location_item.exists(connection):
            print(f'\r【💡已存在】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {names[i]}\n', end='')
            continue
        else:
            is_repairing = True

    # 瀏覽器載入指定的商家地圖連結
    driver.get(store_item.get_link())
    WebDriverWait(driver, 10).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'lfPIob'))
    )
    tags = driver.find_elements(By.CLASS_NAME, 'RcCsl')
    print('\r正在取得地點資訊...(可能會花費較多時間)', end='')
    labels = {
        '地址': None,
        '網站': None,
        '電話號碼': None,
        'Plus Code': None
    }
    if tags:
        for tag in tags:
            items = tag.find_elements(By.CLASS_NAME, 'CsEnBe')
            if items:
                label = items[0].get_attribute('aria-label').strip()
                href = items[0].get_attribute('href')
                if ': ' in label:
                    name = label.split(': ')[0]
                    if href:
                        labels[name] = href
                    else:
                        labels[name] = label.split(': ')[1]

    # 商家欄位資料
    store_item._tag = driver.find_element(By.CLASS_NAME, 'DkEaL').text
    store_item._website = labels['網站']
    if labels['電話號碼']: store_item._phone_number = labels['電話號碼'].replace(' ', '-')
    # 儲存至資料庫
    store_item.insert_if_not_exists(connection)

    # 地點欄位資料
    if labels['地址']:
        postal, city, district, detail = get_split_from_address(labels['地址'])
        location_item._postal_code = postal
        location_item._city = city
        location_item._dist = district
        location_item._details = detail
    if labels['Plus Code']:
        village = re.search(r'(?P<village>\S+里)', labels['Plus Code'])
        location_item._vil = village.group('village') if village else None

    # 變數宣告'評分總數'
    total_ratings_count = int(rate_item._total_ratings)
    # 標籤按鈕 - 總覽/[評論]/簡介
    WebDriverWait(driver, 10).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'RWPxGd'))
    )
    tabs = driver.find_element(By.CLASS_NAME, 'RWPxGd').find_elements(By.CLASS_NAME, 'hh2c6')
    tabs[TAB_TYPE['評論']].click()
    # 評論面板
    WebDriverWait(driver, 10).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'dS8AEf'))
    )
    commentContainer = driver.find_element(By.CLASS_NAME, 'dS8AEf')

    # 取得關鍵字
    for keyword in commentContainer.find_elements(By.CLASS_NAME, 'e2moi'):
        count = keyword.find_elements(By.CLASS_NAME, 'bC3Nkc')
        if len(count) == 0: continue
        keywordItem = {
            '名稱': keyword.find_element(By.CLASS_NAME, 'uEubGf').text,
            '次數': int(count[0].text)
        }

    switch_to_order(order_type='最相關')

    # 滾動評論面板取得所有評論
    while True:
        ActionChains(driver).move_to_element(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')[-1]).perform()
        commentContainer.send_keys(Keys.PAGE_DOWN)
        comments = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
        time.sleep(0.1)
        # 按下「全文」以展開過長的評論內容
        for comment in comments:
            expand_comment = comment.find_elements(By.CLASS_NAME, 'w8nwRe')
            if len(expand_comment) > 0: expand_comment[0].click()
        print(f'\r正在取得所有評論(%d/%d)...' % (len(comments), total_ratings_count), end='')
        if len(comments) >= total_ratings_count:
            break
    total_score = get_comments(store_name=names[i])
    rate_item._store_responses = len(commentContainer.find_elements(By.CLASS_NAME, 'CDe7pd'))
    rate_item._total_comments = len(commentContainer.find_elements(By.CLASS_NAME, 'wiI7pd')) - rate_item._store_responses
    rate_item._real_ratings = round(total_score / rate_item._total_comments, 1)
    # 儲存至資料庫
    rate_item.insert(connection)

    # 等待網址列顯示座標位置後取得座標位置
    print('\r正在取得地點座標...', end='')
    while True:
        if '@' in driver.current_url:
            coordinate = driver.current_url.split('@')[1].split(',')[0:2]
            location_item._longitude = coordinate[0]
            location_item._latitude = coordinate[1]
            break
        time.sleep(1)
    # 儲存至資料庫
    location_item.insert(connection)

    if is_repairing:
        print(f'\r【🛠️已修復】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {names[i]} ({comment_count[i]})\n', end='')
    else:
        print(f'\r【✅已完成】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {names[i]} ({comment_count[i]})\n', end='')

    # 執行完第一個資料後暫時停止繼續爬蟲(開發用)
    # driver.close()
    # break

print('\r已儲存所有搜尋結果的資料！', end='')
driver.close()
connection.close()
