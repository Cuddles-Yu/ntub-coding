import json
import random
import re
import sys
import time
import requests
import subprocess
from enum import Enum
from typing import Optional

from numpy import sin, cos, arccos, pi, round
from 地圖資訊爬蟲.crawler.module.const import *

def download_image_as_binary(url):
    try:
        response = requests.get(url)
        response.raise_for_status()
        return response.content
    except requests.exceptions.RequestException as e:
        print(f"Error downloading image: {e}")
        return None

def get_json_from_api(api_url):
    try:
        response = requests.get(api_url)
        if response.status_code == 200:
            return response.json()
        else:
            print(f"請求失敗，狀態碼: {response.status_code}")
    except requests.exceptions.RequestException as e:
        print(f"發生錯誤: {e}")

def to_bool(s: str) -> bool:
    if s.lower() in ('yes', 'true', 't', 'y', '1'):
        return True
    else:
        return False

def load_json(file_path):
    try:
        with open(file_path, 'r', encoding='utf-8') as file:
            data = json.load(file)
            return data
    except Exception as e:
        return {}

def write_json(data, file_path):
    try:
        with open(file_path, 'w', encoding='utf-8') as file:
            json.dump(data, file, ensure_ascii=False, indent=4)
    except Exception as e:
        pass

def json_to_str(data):
    return json.dumps(data, ensure_ascii=False)

def str_to_json(data):
    if not data: return {}
    return json.loads(data)

def shuffle(target_list: list):
    random.shuffle(target_list)

def is_sublist(subs, target):
    # 判斷前者是否為後者的子陣列
    for sub in subs:
        for i in range(len(target) - len(sub) + 1):
            if target[i:i + len(sub)] == sub: return True
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

SEPARATOR_PATTERN = r'\s*[\s\-—–_（）(){}]+\s*'
SYMBOL_PATTERN = r'[【】（）()｜{}]*'
IGNORE_PATTERN = r'(?<!專門|專賣|小吃|大飯|涼麵|點心|精肉|司總|料理|骨總|利麵|美食|1號|固定|餐館|星來|海產|拉麵|海鮮|快餐|披薩|分分)店'
def get_store_branch_title(store_name, force_return: Optional[bool] = False):
    _title, _name = None, None
    if not re.findall(r'[^酒的關飯]店', store_name):
        if force_return: return store_name, None
        return None, None
    if re.findall(SEPARATOR_PATTERN, store_name):
        pattern = re.compile(
            r'(?P<branch>[^\s\-—–_（）(){}]*' + IGNORE_PATTERN + '|[總分]店)'
        )
        match = pattern.search(store_name)
        if match:
            _name = re.sub(SYMBOL_PATTERN, '', match.group('branch')).strip()
            if len(_name) > 1:
                store_group = store_name[:match.start('branch')]
                if re.findall(SEPARATOR_PATTERN, store_group):
                    last_match = list(re.finditer(fr'(?P<separator>{SEPARATOR_PATTERN})', store_group))[-1]
                    _title = store_group[:last_match.start('separator')].strip()
                else:
                    _title = store_group.strip()
                _title = re.sub(SYMBOL_PATTERN, '', _title).strip()
    else:
        pattern = re.compile(
            fr'(?P<branch>..{IGNORE_PATTERN}|[總分]店)'
        )
        match = pattern.search(store_name)
        if match:
            _name = match.group('branch').strip()
            _title = re.sub(SYMBOL_PATTERN, '', store_name.split(_name)[0].strip()).strip()

    if not _title or not _name or len(_title) <= 1:
        if force_return: return store_name, None
        return None, None
    return _title, _name

def normalize_branch_title(branch_title):
    return re.sub(r'[\s\'’]', '', branch_title.lower())

def get_split_from_plus_code(plus_code):
    _vil = re.search(r'(?P<vil>\D{2}[里])\s*', plus_code)
    vil = _vil.group().strip() if _vil else None
    _search = re.search(r'(?P<city>\D{2}[縣市])\s*(?P<district>\D{1,3}[鄉鎮市區])\s*', plus_code)
    if not _search: _search = re.search(r'(?P<district>\D{1,3}[鄉鎮市區])\s*(?P<city>\D{2}[縣市])\s*', plus_code)
    city = _search.group('city').strip() if _search else None
    district = _search.group('district').strip() if _search else None
    return vil, city, district

def get_split_from_simple_address(simple_address):
    matches = re.match(r'(?P<postal>\d+)(?P<country>\D{1,3}省)*(?P<detail>.*)', simple_address)
    if matches and matches.group('detail'): return matches.group('postal'), matches.group('detail')
    return None, None

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
            if matches: return matches.group('postal'), matches.group('city'), matches.group('district'), matches.group('detail')
    else:
        # 中文地址
        matches = re.match(r'(?P<postal>\d+)(?P<country>\D{1,3}省)*(?P<city>\D{1,3}[縣市])*(?P<district>\D{1,3}[鄉鎮市區])*(?P<detail>.+)', address)
        if matches: return matches.group('postal'), matches.group('city'), matches.group('district'), matches.group('detail')
    # 皆無匹配
    return None, None, None, None

class Python(Enum):
    # 類別
    v37 = r'C:\Users\Cuddles Yu\AppData\Local\Programs\Python\Python37\python.exe'
    v312 = r'C:\Users\Cuddles Yu\AppData\Local\Programs\Python\Python312\python.exe'

def process(version: Python, file_path: str, args: list, output: Optional[bool] = True):
    try:
        result = subprocess.run([version.value, file_path] + args, capture_output=output, text=True, check=True, encoding='utf-8')
        print(result.stdout)
    except subprocess.CalledProcessError as e:
        print(f'執行程式時發生錯誤: {e.stderr}')

def get_args(index: Optional[int] = None):
    _arg = sys.argv
    if index:
        if len(_arg) > index:
            return _arg[index]
        else:
            return None
    else:
        return _arg[1:]

