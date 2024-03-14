
import time
import re
import pandas as pd
import pyautogui
from selenium import webdriver
from selenium.common import NoSuchElementException
from selenium.webdriver import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# 初始化 Driver
options = webdriver.EdgeOptions()
options.add_experimental_option("detach", True)
driver = webdriver.Edge(options=options)
driver.get('https://www.google.com.tw/maps/preview')


# 等待 Driver 瀏覽到指定頁面後，對搜尋框輸入關鍵字搜尋
WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, 'searchboxinput'))
)
element = driver.find_element(By.CLASS_NAME, 'searchboxinput')
keywords = '國立臺北商業大學 附近的蛋塔'
element.send_keys(keywords)
element.send_keys(Keys.ENTER)

WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, 'Nv2PK'))
)
container = driver.find_element(By.XPATH, '/html/body/div[1]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]')

while True:
    try:
        driver.find_element(By.CLASS_NAME, 'HlvSq')
        break
    except NoSuchElementException:
        container.send_keys(Keys.PAGE_DOWN)

titles = driver.find_elements(By.CLASS_NAME, 'hfpxzc')
values = driver.find_elements(By.CLASS_NAME, 'MW4etd')
comments = driver.find_elements(By.CLASS_NAME, 'UY7F9')
url = [title.get_attribute('href') for title in titles]
data = {
    '名稱': [title.get_attribute('aria-label') for title in titles],
    '連結': url,
    '評分': [str(value.text) for value in values],
    '評論數': [re.sub(r'\D', '', comment.text) for comment in comments]
}

# pyautogui.hotkey('ctrl', 't', interval=0.1)

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
    for dic in data:
        if len(data[dic.title()]) < i+1:
            data[dic.title()].append("None")
    commentTab = driver.find_element(By.CLASS_NAME, 'RWPxGd').find_elements(By.CLASS_NAME, 'hh2c6')[1]
    commentTab.click()
    print(f'\r正在取得搜尋結果... |{"█" * (i+1)}{" " * (maxCount - (i+1))}| {str(i+1).zfill(len(str(maxCount)))}/{maxCount}, {round((i+1) * 100 / maxCount, 2)}%', end='')

# pyautogui.hotkey('ctrl', 'w', interval=0.1)

frame = pd.DataFrame(data)
frame.to_csv(format('%s的搜尋結果.csv' % keywords), encoding='utf-8-sig')

print(format('\ncsv輸出完成，總共有 %d 筆資料' %len(titles)))

driver.close()