
import time
import re
import pandas as pd
from selenium import webdriver
from selenium.common import NoSuchElementException
from selenium.webdriver import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.remote.webelement import WebElement
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains

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
driver.get('https://www.google.com.tw/maps/preview')

# 等待 Driver 瀏覽到指定頁面後，對搜尋框輸入關鍵字搜尋
print('\r正在搜尋關鍵字...', end='')
WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, 'searchboxinput'))
)
element = driver.find_element(By.CLASS_NAME, 'searchboxinput')
keywords = '臺北商業大學 附近的蛋塔'
element.send_keys(keywords)
element.send_keys(Keys.ENTER)

# 取得所有搜尋結果所在的'容器'物件
print('\r正在取得搜尋結果...(可能會花費較多時間)', end='')
WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, 'Nv2PK'))
)
resultContainer = driver.find_element(By.XPATH, '/html/body/div[1]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]')
while True:
    try:
        driver.find_element(By.CLASS_NAME, 'HlvSq')
        break
    except NoSuchElementException:
        ActionChains(driver).move_to_element(resultContainer.find_elements(By.CLASS_NAME, 'Nv2PK')[-1]).perform()
        resultContainer.send_keys(Keys.PAGE_DOWN)
        time.sleep(0.1)

print('\r正在建立資料...', end='')
titles = driver.find_elements(By.CLASS_NAME, 'hfpxzc')  # 地點名稱
values = driver.find_elements(By.CLASS_NAME, 'MW4etd')  # 平均評分
comments = driver.find_elements(By.CLASS_NAME, 'UY7F9')  # 評分總數
url = [title.get_attribute('href') for title in titles]
data = {
    '地點名稱': [title.get_attribute('aria-label') for title in titles],
    '地圖標籤': [],
    '地圖連結': url,
    '經度座標': [],
    '緯度座標': [],
    '平均評分': [str(value.text) for value in values],
    '評分總數': [re.sub(r'\D', '', comment.text) for comment in comments],
    '留言總數': [],
    '相關留言': [],
    '最新留言': []
}

maxCount = len(url)
for i in range(maxCount):
    # driver.switch_to.window(driver.window_handles[1])
    driver.get(url[i])
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'lfPIob'))
    )
    tags = driver.find_elements(By.CLASS_NAME, 'RcCsl')
    print('\r正在取得地點資訊...(可能會花費較多時間)', end='')
    if tags:
        for tag in tags:
            name = ''
            names = tag.find_elements(By.CLASS_NAME, 'CsEnBe')
            if names:
                name = names[0].get_attribute('aria-label')
                href = names[0].get_attribute('href')
                if ': ' in name:
                    name = name.split(': ')[0]
                    if name not in data:
                        data[name] = []
                        for j in range(i-1):
                            data[name].append("None")
                    if href:
                        data[name].append(href)
                    else:
                        data[name].append(tag.find_element(By.CLASS_NAME, 'Io6YTe').text)
    data['地圖標籤'].append(driver.find_element(By.CLASS_NAME, 'DkEaL').text)

    # 變數宣告'評分總數'
    totalCommentCount = int(data['評分總數'][i])
    # 標籤按鈕 - 總覽/[評論]/簡介
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'RWPxGd'))
    )
    tabs = driver.find_element(By.CLASS_NAME, 'RWPxGd').find_elements(By.CLASS_NAME, 'hh2c6')
    tabs[TabType['評論']].click()
    # 評論面板
    commentContainer = driver.find_element(By.CLASS_NAME, 'dS8AEf')

    # --- 【取得最相關與最新留言前10筆】 ---
    storeCount = 10
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
    orderList[OrderType['最新']].click()
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'jftiEf'))
    )
    comments = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
    latestComments = []
    for comment in comments[0:storeCount] if len(comments) >= storeCount else comments:
        try:
            latestComments.append(comment.find_element(By.CLASS_NAME, 'wiI7pd').text)
        except:
            pass
    data['最新留言'].append(', '.join(latestComments))
    orderButton[ButtonType['排序評論']].click()
    # 排序選單 - [最相關]/最新/評分最高/評分最低
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'fxNQSd'))
    )
    orderList = driver.find_elements(By.CLASS_NAME, 'fxNQSd')
    orderList[OrderType['最相關']].click()
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'jftiEf'))
    )
    comments = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
    relatedComments = []
    for comment in comments[0:storeCount] if len(comments) >= storeCount else comments:
        try:
            relatedComments.append(comment.find_element(By.CLASS_NAME, 'wiI7pd').text)
        except:
            pass
    data['相關留言'].append(', '.join(relatedComments))
    # 滾動評論面板取得所有評論
    while True:
        ActionChains(driver).move_to_element(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')[-1]).perform()
        commentContainer.send_keys(Keys.PAGE_DOWN)
        currentCount = len(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf'))
        print(f'\r正在取得所有評論(%d/%d)...' % (currentCount, totalCommentCount), end='')
        if currentCount >= totalCommentCount:
            break
        time.sleep(0.1)
    data['留言總數'].append(str(len(commentContainer.find_elements(By.CLASS_NAME, 'Upo0Ec'))))

    # 取得所有留言並重新計算評分
    # allComments = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
    # for c in range(len(allComments)):
    #     try:
    #         # 新增留言分析以及重新計算平均評分
    #     except:
    #         pass

    # 等待網址列顯示座標位置後取得座標位置
    print('\r正在取得地點座標...', end='')
    if len(data['地址']) == i+1:
        while True:
            if '@' in driver.current_url:
                coordinate = driver.current_url.split('@')[1].split(',')[0:2]
                data['經度座標'].append(coordinate[0])
                data['緯度座標'].append(coordinate[1])
                break
            time.sleep(1)

    for dic in data:
        if len(data[dic.title()]) < i+1:
            data[dic.title()].append("None")

    progressPercentage = round((i + 1) * 100 / maxCount, 2)
    print(f'\r進度 {str(i + 1).zfill(len(str(maxCount)))}/{maxCount}, {progressPercentage}% [{"▮" * int(progressPercentage // 10 + 1)}{"▯" * int(10 - (progressPercentage // 10 + 1))}]', end='')

print('\r正在輸出csv搜尋結果...', end='')
# print(data)
frame = pd.DataFrame(data)
frame.to_csv(format('%s的搜尋結果.csv' % keywords.strip().replace(' ','')), encoding='utf-8-sig')
print('\rcsv搜尋結果已完成輸出！', end='')

# driver.close()
