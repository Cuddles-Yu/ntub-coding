from 地圖資訊爬蟲.crawler.module.const import *

import re
import sys
from 地圖資訊爬蟲.crawler.module.return_code import *
from numpy import sin, cos, arccos, pi, round

from selenium import webdriver
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as ec

def init_driver(url: str):
    options = webdriver.EdgeOptions()
    options.add_experimental_option("detach", True)
    options.add_argument('--window-size=950,1020')
    # options.add_argument("--headless")  # 不顯示視窗
    driver = webdriver.Edge(options=options)
    if url: driver.get(url)
    driver.set_window_position(x=970, y=10)
    return driver

### 函式 ###
def crawler_exit(driver, connection):
    driver.close()
    connection.close()
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

def getDistanceBetweenPointsNew(coordinate1: list, coordinate2: list, unit='kilometers'):
    theta = coordinate1[1] - coordinate2[1]
    distance = 60 * 1.1515 * rad2deg(
        arccos(
            (sin(deg2rad(coordinate1[0])) * sin(deg2rad(coordinate2[0]))) +
            (cos(deg2rad(coordinate1[0])) * cos(deg2rad(coordinate2[0])) * cos(deg2rad(theta)))
        )
    )
    match unit:
        case 'miles':
            return round(distance, 2)
        case 'kilometers':
            return round(distance * 1.609344, 2)

def keyword_filter(keyword: str):
    return re.findall(r"[^,-_'&a-zA-Z\s\d]+", keyword)

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

def wait_for_click(by, driver, value):
    try:
        element = WebDriverWait(driver, MAXIMUM_WAITING).until(
            ec.presence_of_element_located((by, value))
        )
        element.click()
        return element
    except TimeoutException:
        return None

def wait_for_element(by, driver, value):
    try:
        element = WebDriverWait(driver, MAXIMUM_WAITING).until(
            ec.presence_of_element_located((by, value))
        )
        return element
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
