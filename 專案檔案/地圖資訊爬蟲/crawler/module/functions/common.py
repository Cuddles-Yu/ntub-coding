import random
import re
import time
from numpy import sin, cos, arccos, pi, round
from 地圖資訊爬蟲.crawler.module.const import *

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
