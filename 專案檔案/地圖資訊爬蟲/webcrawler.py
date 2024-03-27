import os
import time
import re
import json
from selenium import webdriver
from selenium.webdriver import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains

def getSplitFromAddress(address):
    matches = re.match(r'(?P<postal>\d+)(?P<city>\D+[縣市])(?P<district>\D+[鄉鎮市區])(?P<detail>.+)', address)
    return matches.group('postal'), matches.group('city'), matches.group('district'), matches.group('detail')

def getComments(type, num):
    print(f'\r正在取得前%d筆{type}評論...' % storeCount, end='')
    # 功能按鈕 - 撰寫/查詢/[排序評論]
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'S9kvJb'))
    )
    orderButton = driver.find_elements(By.CLASS_NAME, 'S9kvJb')
    orderButton[ButtonType['排序評論']].click()
    # 排序選單 - 最相關/[最新]/評分最高/評分最低
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'fxNQSd'))
    )
    orderList = driver.find_elements(By.CLASS_NAME, 'fxNQSd')
    orderList[OrderType[type]].click()
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'jftiEf'))
    )
    # 可能不到10筆資料(沒留言的不會爬)
    filteredComments = [
        comment for comment in commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
        if len(comment.find_elements(By.CLASS_NAME, 'wiI7pd')) > 0
    ]
    for index in range(storeCount) if len(filteredComments) >= storeCount else range(len(filteredComments)):
        try:
            userID = filteredComments[index].find_element(By.CLASS_NAME, 'al6Kxe').get_attribute('data-href').split('/')[-2]
            # 按下「全文」以展開過長的評論內容
            expandComment = filteredComments[index].find_elements(By.CLASS_NAME, 'w8nwRe')
            if len(expandComment) > 0: expandComment[0].click()
            # 取得留言結構
            commentItem = {
                '評論者ID': userID,
                '內容': filteredComments[index].find_element(By.CLASS_NAME, 'wiI7pd').text,
                '次序': (index+1) + num,  # 最新留言序號由1起始
                '時間': filteredComments[index].find_element(By.CLASS_NAME, 'rsqaWe').text,
                '評分': int(filteredComments[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
            }
            # 取得在地嚮導等級
            level = re.search(r'ba(?P<level>\d+)', filteredComments[index].find_element(By.CLASS_NAME, 'NBa7we').get_attribute('src'))
            # 取得評論者結構
            userItem = {
                'ID': userID,
                '名稱': filteredComments[index].find_element(By.CLASS_NAME, 'd4r55').text,
                '等級': int(level.group('level'))+2 if level else 0
            }
            data['留言'].append(commentItem)
            if not isUserExists(userItem['ID']): data['評論者'].append(userItem)
        except:
            pass

def isUserExists(id):
    return id in set(user['ID'] for user in data['評論者'])

ButtonType = {
    '撰寫評論': 0,
    '查詢評論': 1,
    '排序評論': 2
}
OrderType = {
    '最相關': 0,
    '最新': 1,
    '評分最高': 2,
    '評分最低': 3
}
TabType = {
    '總覽': 0,
    '評論': 1,
    '簡介': 2
}

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
    EC.presence_of_element_located((By.CLASS_NAME, 'searchboxinput'))
)
element = driver.find_element(By.CLASS_NAME, 'searchboxinput')
searchKeywords = '臺北商業大學 蛋塔'
element.send_keys(searchKeywords)
element.send_keys(Keys.ENTER)

# 檔案儲存路徑
filePath = f'{searchKeywords.strip().replace(' ', '')}的搜尋結果.csv'

# 取得所有搜尋結果所在的'容器'物件
print('\r正在取得搜尋結果...(可能會花費較多時間)', end='')
WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, 'Nv2PK'))
)
resultContainer = driver.find_element(By.XPATH, '/html/body/div[1]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]')
# 向下捲動瀏覽所有搜尋結果
# while True:
#     try:
#         driver.find_element(By.CLASS_NAME, 'HlvSq')
#         break
#     except NoSuchElementException:
#         ActionChains(driver).move_to_element(resultContainer.find_elements(By.CLASS_NAME, 'Nv2PK')[-1]).perform()
#         resultContainer.send_keys(Keys.PAGE_DOWN)
#         time.sleep(0.1)

titles = driver.find_elements(By.CLASS_NAME, 'hfpxzc')
# 地點名稱
names = [title.get_attribute('aria-label') for title in titles]
# 地圖連結
url = [title.get_attribute('href') for title in titles]
# 平均評分
values = [str(value.text) for value in driver.find_elements(By.CLASS_NAME, 'MW4etd')]
# 評分總數
comments = [int(re.sub(r'\D', '', comment.text)) for comment in driver.find_elements(By.CLASS_NAME, 'UY7F9') ]

maxCount = len(titles)
for i in range(maxCount):
    data = {
        '商家': {
            '名稱': names[i],
            # '類別': 'None',
            '標籤': 'None',
            '地圖連結': url[i],
            '網站': 'None',
            '電話號碼': 'None',
            '關鍵字': []
        },
        '評分': {
            '商家名稱': names[i],
            '平均評分': float(values[i]),
            '評分總數': comments[i],
            '真實評分': 'None',
            '留言總數': 0,
            '回應次數': 0
        },
        '地點': {
            '商家名稱': names[i],
            '經度座標': 'None',
            '緯度座標': 'None',
            '郵遞區號': 'None',
            '縣市別': 'None',
            '區域別': 'None',
            '鄰里別': 'None',
            '詳細地址': 'None'
        },
        '留言': [],
        '評論者': []
    }
    driver.get(data['商家']['地圖連結'])
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'lfPIob'))
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
    data['商家']['標籤'] = driver.find_element(By.CLASS_NAME, 'DkEaL').text
    data['商家']['網站'] = labels['網站']
    data['商家']['電話號碼'] = labels['電話號碼'].replace(' ', '-')

    # 地點欄位資料
    if labels['地址']:
        postal, city, district, detail = getSplitFromAddress(labels['地址'])
        data['地點']['郵遞區號'] = postal
        data['地點']['縣市別'] = city
        data['地點']['區域別'] = district
        data['地點']['詳細地址'] = detail
    if labels['Plus Code']:
        village = re.search(r'(?P<village>\S+里)', labels['Plus Code'])
        data['地點']['鄰里別'] = village.group('village') if village else 'None'

    # 變數宣告'評分總數'
    totalCommentCount = int(data['評分']['評分總數'])
    # 標籤按鈕 - 總覽/[評論]/簡介
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'RWPxGd'))
    )
    tabs = driver.find_element(By.CLASS_NAME, 'RWPxGd').find_elements(By.CLASS_NAME, 'hh2c6')
    tabs[TabType['評論']].click()
    # 評論面板
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'dS8AEf'))
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
        data['商家']['關鍵字'].append(keywordItem)

    # --- 【取得最相關與最新留言前10筆】 ---
    storeCount = 10
    getComments('最新', 0)
    getComments('最相關', 1000)

    # 滾動評論面板取得所有評論
    while True:
        ActionChains(driver).move_to_element(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')[-1]).perform()
        commentContainer.send_keys(Keys.PAGE_DOWN)
        currentCount = len(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf'))
        print(f'\r正在取得所有評論(%d/%d)...' % (currentCount, totalCommentCount), end='')
        if currentCount >= totalCommentCount:
            break
        time.sleep(0.1)
    data['評分']['留言總數'] = len(commentContainer.find_elements(By.CLASS_NAME, 'Upo0Ec'))
    data['評分']['回應次數'] = len(commentContainer.find_elements(By.CLASS_NAME, 'CDe7pd'))

    # 取得所有留言並重新計算評分
    # allComments = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
    # for c in range(len(allComments)):
    #     try:
    #         # 新增留言分析以及重新計算平均評分
    #     except:
    #         pass

    # 等待網址列顯示座標位置後取得座標位置
    print('\r正在取得地點座標...', end='')
    while True:
        if '@' in driver.current_url:
            coordinate = driver.current_url.split('@')[1].split(',')[0:2]
            data['地點']['經度座標'] = coordinate[0]
            data['地點']['緯度座標'] = coordinate[1]
            break
        time.sleep(1)

    # 準備寫入檔案前先清空資料表
    # if i == 0: pd.DataFrame(columns=data.keys()).to_csv(filePath, index=False, encoding='utf-8-sig')
    # 新增資料至資料表
    # pd.DataFrame(data, index=[1]).to_csv(filePath, mode='a', header=False, index=False, encoding='utf-8-sig')
    print(f'\r【已完成{str(i + 1).zfill(len(str(maxCount)))}/{maxCount}】{data['商家']['名稱']} ({data['評分']['評分總數']})\n', end='')
    with open('爬蟲結構.json', "w", encoding="utf-8") as file:
        # 將中文字串寫入檔案
        file.write(json.dumps(data, indent=4, ensure_ascii=False))

    # 執行完第一個資料後暫時停止繼續爬蟲
    driver.close()
    break

# print('\r已輸出所有搜尋結果的資料！', end='')
# driver.close()
