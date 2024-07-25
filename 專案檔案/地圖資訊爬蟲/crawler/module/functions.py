import re
import sys
import time
import random
from numpy import sin, cos, arccos, pi, round

from 地圖資訊爬蟲.crawler.module.const import *
from 地圖資訊爬蟲.crawler.tables import Store
from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.return_code import *

from selenium import webdriver
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as ec

def init_driver(url: str):
    options = webdriver.EdgeOptions()
    options.add_argument('--disable-blink-features=AutomationControlled')
    options.add_experimental_option("detach", True)
    options.add_argument('--window-size=950,1020')
    # options.add_argument("--headless")  # 不顯示視窗
    driver = webdriver.Edge(options=options)
    driver.execute_script("Object.defineProperty(navigator, 'webdriver', {get: () => undefined})")
    if url: driver.get(url)
    driver.set_window_position(x=970, y=10)
    return driver

def search(driver, keyword):
    # 等待 Driver 瀏覽到指定頁面後，對搜尋框輸入關鍵字搜尋
    search_box = wait_for_element(By.CLASS_NAME, driver, 'searchboxinput')
    search_box.clear()
    search_box.send_keys(keyword)
    search_box.send_keys(Keys.ENTER)

def search_and_scroll(connection, driver, keyword):
    while True:
        search(driver, keyword)

        # 取得所有搜尋結果所在的'容器'物件
        print('\r正在取得搜尋結果...(可能會花費較多時間)', end='')
        WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
            ec.presence_of_element_located((By.CLASS_NAME, 'Nv2PK'))
        )
        container_search_result = find_element_list(By.XPATH, driver, [
            '/html/body/div[1]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]',
            '/html/body/div[2]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]'
        ])

        # 紀錄爬取評論的等待時間
        start_time = time.time()
        current_results = 0
        finish_scroll = True

        # 向下捲動瀏覽所有搜尋結果
        if ENABLE_SCROLL_DOWN:
            while True:
                if len(driver.find_elements(By.CLASS_NAME, 'HlvSq')) > 0:
                    break
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

    element_search_title = driver.find_elements(By.CLASS_NAME, 'hfpxzc')
    # 商家連結與名稱
    urls = to_map_url([title.get_attribute('href') for title in element_search_title])
    store_names = [title.get_attribute('aria-label') for title in element_search_title]

    # 儲存本次查詢瀏覽連結
    print(f'\r正在建立搜尋結果至資料庫...')
    for url, store_name in zip(urls, store_names):
        store = Store.newObject(store_name, url)
        if store.exists(connection):
            print(f'✴️已存在搜尋結果【{store_name}】')
        else:
            store.insert_if_not_exists(connection)
            print(f'✳️已建立搜尋結果【{store_name}】')

    return urls

### 函式 ###
def crawler_exit(driver, connection):
    if driver: driver.close()
    if connection: connection.close()
    sys.exit(ReturnCode.Success)

def to_bool(s: str) -> bool:
    if s.lower() in ('yes', 'true', 't', 'y', '1'):
        return True
    else:
        return False

def rad2deg(radians):
    degrees = radians * 180 / pi
    return degrees
def deg2rad(degrees):
    radians = degrees * pi / 180
    return radians

def random_delay(_min: float, _max: float, _decimals: int):
    time.sleep(round(random.uniform(_min, _max), _decimals))

def getDistanceBetweenPointsNew(coordinate1: list, coordinate2: list, unit='kilometers'):
    theta = coordinate1[1] - coordinate2[1]
    distance = 60 * 1.1515 * rad2deg(
        arccos(
            (sin(deg2rad(coordinate1[0])) * sin(deg2rad(coordinate2[0]))) +
            (cos(deg2rad(coordinate1[0])) * cos(deg2rad(coordinate2[0])) * cos(deg2rad(theta)))
        )
    )
    if unit == 'miles':
        return round(distance, 2)
    elif 'kilometers':
        return round(distance * 1.609344, 2)

def keyword_filter(keyword: str):
    return 1 < len(keyword) < MAXIMUM_KEYWORD_LENGTH and not only_pattern(r"\d+\s*元", keyword)

def keyword_separator(keyword: str):
    return re.findall(r"[^,-_'&a-zA-Zâéûêîôäëïöüáíóúàèñìòù\s\d]+", keyword)

def only_pattern(pattern, keyword):
    return re.sub(pattern, '', keyword).strip() == ''

def limit_list(array, c) -> list:
    if c > 0:
        return array[:c]
    else:
        return []

def exclude_list(array, c) -> list:
    if c > 0:
        return array[c:]
    else:
        return []

def combine(str_array: list, separator: str) -> str:
    return separator.join(str_array)

def click_element(driver, element):
    if element is None: return
    ActionChains(driver).move_to_element(element).perform()
    element.click()

def wait_for_click(by, driver, value):
    try:
        WebDriverWait(driver, MAXIMUM_WAITING).until(
            ec.presence_of_element_located((by, value))
        )
        element = driver.find_element(by, value)
        element.click()
        return element
    except Exception:
        return None

def wait_for_click_index(by, driver, value, index):
    try:
        WebDriverWait(driver, MAXIMUM_WAITING).until(
            ec.presence_of_element_located((by, value))
        )
        element = driver.find_elements(by, value)[index-1]
        element.click()
        return element
    except Exception as e:
        return None

def find_element_list(by, driver, values):
    for value in values:
        elements = driver.find_elements(by, value)
        if elements: return elements[0]
    return None

def wait_for_element_list(by, driver, values):
    for value in values:
        element = wait_for_element(by, driver, value)
        if element: return element
    return None

def wait_for_element(by, driver, value):
    try:
        WebDriverWait(driver, MAXIMUM_WAITING).until(
            ec.presence_of_element_located((by, value))
        )
        return driver.find_element(by, value)
    except TimeoutException:
        return None

def wait_for_elements(by, driver, value):
    try:
        WebDriverWait(driver, MAXIMUM_WAITING).until(
            ec.presence_of_element_located((by, value))
        )
        return driver.find_elements(by, value)
    except TimeoutException:
        return None

def get_split_from_address(address):
    if ',' in address:
        # 英譯地址
        _split = address.split(', ')
        if len(_split) > 3:
            # 正常拆分
            details = combine(_split[0:len(_split)], ', ')
            matches = re.match(r'(?P<district>\D{2}[鄉鎮市區])(?P<city>\D{2}[縣市])(?P<postal>.+)', _split[-1])
            if matches and details:
                return matches.group('postal'), matches.group('city'), matches.group('district'), details
        else:
            # 換位拆分
            matches = re.match(r'(?P<detail>.+)(?P<district>\D{2}[鄉鎮市區])(?P<city>\D{2}[縣市])(?P<postal>\d+)', address)
            if matches:
                return matches.group('postal'), matches.group('city'), matches.group('district'), matches.group('detail')
    else:
        # 中文地址
        matches = re.match(r'(?P<postal>\d+)(?P<city>\D{2}[縣市])(?P<district>\D{2}[鄉鎮市區])(?P<detail>.+)', address)
        if matches:
            return matches.group('postal'), matches.group('city'), matches.group('district'), matches.group('detail')
    # 皆無匹配
    return None, None, None, None
