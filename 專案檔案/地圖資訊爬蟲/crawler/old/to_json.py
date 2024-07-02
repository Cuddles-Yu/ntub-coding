import json
import re
import time
from selenium import webdriver
from selenium.webdriver import Keys
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.support.ui import WebDriverWait

# 系統常數設定
SEARCH_KEYWORD = '臺北商業大學 蛋塔'

IMG_URL = 'https://lh5.googleusercontent.com/p/'
MAP_URL = 'https://www.google.com.tw/maps/place/'

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
        if len(comment.find_elements(By.CLASS_NAME, 'wiI7pd')) > 0
    ]
    for index in range(len(filtered_comments)):
        try:
            sum_score += int(filtered_comments[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
            user_id = filtered_comments[index].find_element(By.CLASS_NAME, 'al6Kxe').get_attribute('data-href').split('/')[-2]
            # 取得留言結構
            comment_item = {
                '商家名稱': store_name,
                '評論者ID': user_id,
                '內容': filtered_comments[index].find_element(By.CLASS_NAME, 'wiI7pd').text,
                '次序': (index + 1),  # 最新留言序號由1起始
                '時間': filtered_comments[index].find_element(By.CLASS_NAME, 'rsqaWe').text,
                '評分': int(filtered_comments[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
            }
            # 取得在地嚮導等級
            level = re.search(r'ba(?P<level>\d+)', filtered_comments[index].find_element(By.CLASS_NAME, 'NBa7we').get_attribute('src'))
            # 取得評論者結構
            user_item = {
                'ID': user_id,
                # '名稱': filtered_comments[index].find_element(By.CLASS_NAME, 'd4r55').text,
                '等級': int(level.group('level'))+2 if level else 0
            }
            db['留言'].append(comment_item)
            if not is_user_exists(user_item['ID']): db['評論者'].append(user_item)
            print(f'\r正在儲存評論結構(%d/%d)...' % (index+1, len(filtered_comments)), end='')
        finally:
            pass
    return sum_score

def is_user_exists(user_id: str) -> bool:
    return user_id in set(user['ID'] for user in db['評論者'])

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
# 預覽圖片
images = [img.find_element(By.CLASS_NAME, 'p0Hhde').find_element(By.TAG_NAME, 'img').get_attribute('src').split('/')[-1] for img in element_search_img]
# 地圖連結
url = [title.get_attribute('href').split('/')[-1] for title in element_search_title]
# 平均評分
values = [str(value.text) for value in driver.find_elements(By.CLASS_NAME, 'MW4etd')]
# 評分總數
comment_count = [int(re.sub(r'\D', '', comment.text)) for comment in driver.find_elements(By.CLASS_NAME, 'UY7F9')]

db = {
    '商家': [],
    '地點': [],
    '回饋': [],
    '留言': [],
    '評論者': []
}

max_count = len(element_search_title)
for i in range(max_count):
    db_store = {
        '名稱': names[i],
        # '類別': 'None',
        '標籤': 'None',
        '預覽圖片': IMG_URL + images[i],
        '地圖連結': MAP_URL + url[i],
        '網站': 'None',
        '電話號碼': 'None',
        '關鍵字': []
    }
    db_response = {
        '商家名稱': names[i],
        '平均評分': float(values[i]),
        '評分總數': comment_count[i],
        '真實評分': 0.0,
        '留言總數': 0,
        '回應次數': 0
    }
    db_location = {
        '商家名稱': names[i],
        '經度座標': 'None',
        '緯度座標': 'None',
        '郵遞區號': 'None',
        '縣市別': 'None',
        '區域別': 'None',
        '鄰里別': 'None',
        '詳細地址': 'None'
    }

    driver.get(MAP_URL + db_store['地圖連結'])
    WebDriverWait(driver, 10).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'lfPIob'))
    )
    tags = driver.find_elements(By.CLASS_NAME, 'RcCsl')
    print('\r正在取得地點資訊...(可能會花費較多時間)', end='')
    labels = {
        '地址': 'None',
        '網站': 'None',
        '電話號碼': 'None',
        'Plus Code': 'None'
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
    db_store['標籤'] = driver.find_element(By.CLASS_NAME, 'DkEaL').text
    db_store['網站'] = labels['網站']
    db_store['電話號碼'] = labels['電話號碼'].replace(' ', '-')

    # 地點欄位資料
    if labels['地址']:
        postal, city, district, detail = get_split_from_address(labels['地址'])
        db_location['郵遞區號'] = postal
        db_location['縣市別'] = city
        db_location['區域別'] = district
        db_location['詳細地址'] = detail
    if labels['Plus Code']:
        village = re.search(r'(?P<village>\S+里)', labels['Plus Code'])
        db_location['鄰里別'] = village.group('village') if village else 'None'

    # 變數宣告'評分總數'
    totalCommentCount = int(db_response['評分總數'])
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
        db_store['關鍵字'].append(keywordItem)

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
        print(f'\r正在取得所有評論(%d/%d)...' % (len(comments), totalCommentCount), end='')
        if len(comments) >= totalCommentCount:
            break
    total_score = get_comments(store_name=names[i])
    db_response['留言總數'] = len(commentContainer.find_elements(By.CLASS_NAME, 'Upo0Ec'))
    db_response['回應次數'] = len(commentContainer.find_elements(By.CLASS_NAME, 'CDe7pd'))
    db_response['真實評分'] = round(total_score / db_response['留言總數'], 1)

    # 等待網址列顯示座標位置後取得座標位置
    print('\r正在取得地點座標...', end='')
    while True:
        if '@' in driver.current_url:
            coordinate = driver.current_url.split('@')[1].split(',')[0:2]
            db_location['經度座標'] = coordinate[0]
            db_location['緯度座標'] = coordinate[1]
            break
        time.sleep(1)

    db['商家'].append(db_store)
    db['地點'].append(db_location)
    db['回饋'].append(db_response)
    with open('../../爬蟲結構.json', "w", encoding="utf-8") as file:
        file.write(json.dumps(db, indent=4, ensure_ascii=False))

    print(f'\r【已完成{str(i + 1).zfill(len(str(max_count)))}/{max_count}】{db['商家'][i]['名稱']} ({db['回饋'][i]['評分總數']})\n', end='')

    # 執行完第一個資料後暫時停止繼續爬蟲(開發用)
    # driver.close()
    # break

print('\r已輸出所有搜尋結果的資料！', end='')
driver.close()
