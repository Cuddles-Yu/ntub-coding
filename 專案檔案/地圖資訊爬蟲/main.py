
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

# 初始化 Driver
print('\r[步驟1/7] 正在連線到GoogleMap...', end='')
options = webdriver.EdgeOptions()
options.add_experimental_option("detach", True)
driver = webdriver.Edge(options=options)
driver.get('https://www.google.com.tw/maps/preview')

# 等待 Driver 瀏覽到指定頁面後，對搜尋框輸入關鍵字搜尋
print('\r[步驟2/7] 正在搜尋關鍵字...', end='')
WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, 'searchboxinput'))
)
element = driver.find_element(By.CLASS_NAME, 'searchboxinput')
keywords = '國立臺北商業大學 附近的蛋塔'
element.send_keys(keywords)
element.send_keys(Keys.ENTER)

# 取得所有搜尋結果所在的'容器'物件
print('\r[步驟3/7] 正在取得搜尋結果...(可能會花費較多時間)', end='')
WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, 'Nv2PK'))
)
resultContainer = driver.find_element(By.XPATH, '/html/body/div[1]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]')
# while True:
#     try:
#         driver.find_element(By.CLASS_NAME, 'HlvSq')
#         break
#     except NoSuchElementException:
#         resultContainer.send_keys(Keys.PAGE_DOWN)

print('\r[步驟4/7] 建立基本資訊之JSON結構...', end='')
titles = driver.find_elements(By.CLASS_NAME, 'hfpxzc')
values = driver.find_elements(By.CLASS_NAME, 'MW4etd')
comments = driver.find_elements(By.CLASS_NAME, 'UY7F9')
url = [title.get_attribute('href') for title in titles]
data = {
    '名稱': [title.get_attribute('aria-label') for title in titles],
    'X座標': [],
    'Y座標': [],
    '連結': url,
    '評分': [str(value.text) for value in values],
    '評論數': [re.sub(r'\D', '', comment.text) for comment in comments],
    '留言數': [],
    '留言': []
}

print('\r[步驟5/7] 正在取得地點標籤資訊...(可能會花費較多時間)', end='')
maxCount = len(url)
for i in range(maxCount):
    # driver.switch_to.window(driver.window_handles[1])
    driver.get(url[i])
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CLASS_NAME, 'lfPIob'))
    )
    tags = driver.find_elements(By.CLASS_NAME, 'RcCsl')
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

    # 1.模擬滑鼠右鍵打開功能選單來取得座標位置
    # canvas = driver.find_element(By.XPATH, "//*[name()='canvas']")
    # ActionChains(driver).context_click(canvas).perform()
    # WebDriverWait(driver, 10).until(
    #     EC.presence_of_element_located((By.CLASS_NAME, 'mLuXec'))
    # )
    # data['座標'].append(driver.find_element(By.CLASS_NAME, 'mLuXec').text)

    # 2.等待網址列顯示座標位置後取得座標位置
    if len(data['地址']) == i+1:
        time.sleep(3)
        coordinate = driver.current_url.split('@')[1].split(',')[0:2]
        data['X座標'].append(coordinate[0])
        data['Y座標'].append(coordinate[1])

    commentTab = driver.find_element(By.CLASS_NAME, 'RWPxGd').find_elements(By.CLASS_NAME, 'hh2c6')[1]
    commentTab.click()

    print('\r[步驟6/7] 正在取得所有評論...(可能會花費較多時間)', end='')
    commentContainer = driver.find_element(By.CLASS_NAME, 'dS8AEf')
    while True:
        commentContainer.send_keys(Keys.PAGE_DOWN)
        if len(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')) >= int(data['評論數'][i]):
            break
    allComments = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
    comments = []
    data['留言數'].append(str(len(commentContainer.find_elements(By.CLASS_NAME, 'Upo0Ec'))))
    for comment in allComments:
        try:
            comments.append(comment.find_element(By.CLASS_NAME, 'wiI7pd').text)
        except:
            pass
    data['留言'].append(f'[{", ".join(comments)}]')

    for dic in data:
        if len(data[dic.title()]) < i+1:
            data[dic.title()].append("None")

    progressPercentage = round((i + 1) * 100 / maxCount, 2)
    print(f'\r進度 {str(i + 1).zfill(len(str(maxCount)))}/{maxCount}, {progressPercentage}% [{"▮" * int(progressPercentage // 10 + 1)}{"▯" * int(10 - (progressPercentage // 10 + 1))}]', end='')

print('\r[步驟7/7] 正在輸出csv搜尋結果...', end='')
frame = pd.DataFrame(data)
frame.to_csv(format('%s的搜尋結果.csv' % keywords), encoding='utf-8-sig')
print('\rcsv搜尋結果已完成輸出！', end='')

# driver.close()
