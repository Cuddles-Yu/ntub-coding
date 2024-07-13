### 匯入模組 ###
import re
import sys
import time

from selenium.common.exceptions import TimeoutException

from tables import Administrator, Comment, Contributor, Favorite, Keyword, Location, Member, Rate, Service, Store, Tag, OpenHours
from tables._base import *
from module.delete_database import *
from module.const import *
from module.return_code import *

import module.create_database as db
import module.modify_database as mdb

from numpy import sin, cos, arccos, pi, round

from selenium import webdriver
from selenium.webdriver import Keys
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.support.ui import WebDriverWait

### 結構 ###
ORDER_TYPE = {
    '最相關': 0,
    '最新': 1,
    '評分最高': 2,
    '評分最低': 3
}


### 函式 ###
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


def limit_list(array, c) -> list:
    return array[:c]
def combine(str_array: list, separator: str) -> str:
    return separator.join(str_array)

def has_service(k: str, yes: list, no: list):
    if k in no:
        return 0
    elif k not in no and k in yes:
        return 1
    else:
        return None

def wait_for_element(by, value):
    try:
        element = WebDriverWait(driver, MAXIMUM_WAITING).until(
            ec.presence_of_element_located((by, value))
        )
        return element
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
                return matches.group('postal'), matches.group('city'), matches.group('district'), matches.group(
                    'detail')
    else:
        # 中文地址
        matches = re.match(r'(?P<postal>\d+)(?P<city>\D{2}[縣市])(?P<district>\D{2}[鄉鎮市區])(?P<detail>.+)', address)
        if matches:
            return matches.group('postal'), matches.group('city'), matches.group('district'), matches.group('detail')
    # 皆無匹配
    return None, None, None, None


def switch_to_order(order_type: str) -> bool:
    print(f'\r正在切換至{order_type}評論...', end='')
    # 功能按鈕 - 撰寫/查詢/[排序評論]
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'S9kvJb'))
    )
    function_buttons = driver.find_elements(By.CLASS_NAME, 'S9kvJb')
    function_names = [button.get_attribute('data-value') for button in driver.find_elements(By.CLASS_NAME, 'S9kvJb')]
    if '排序' not in function_names: return False
    function_buttons[function_names.index('排序')].click()

    # 排序選單 - 最相關/最新/評分最高/評分最低
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'kA9KIf'))
    )
    order_list = driver.find_elements(By.CLASS_NAME, 'fxNQSd')
    order_list[ORDER_TYPE[order_type]].click()
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'jftiEf'))
    )
    return True


### 主程式 ###
# 連線資料庫
connection = db.connect(use_database=True)

# 初始化 Driver
# print('\r正在連線到GoogleMap...', end='\n')
options = webdriver.ChromeOptions()
options.add_experimental_option("detach", True)
options.add_argument('--window-size=950,1020')
# options.add_argument("--headless")  # 不顯示視窗
driver = webdriver.Chrome(options=options)
# driver.minimize_window()  # 最小化視窗
driver.get('https://www.google.com.tw/maps/preview')
driver.set_window_position(x=970, y=10)

if REPAIR_DATA:
    urls = mdb.get_urls_from_incomplete_store(connection)
else:
    urls = [
        
    ]

need_to_save_urls = len(urls) == 0
print(f'資料將儲存至資料庫 -> {NAME}')

if len(urls) == 0:
    while True:
        # 等待 Driver 瀏覽到指定頁面後，對搜尋框輸入關鍵字搜尋
        print(f'正在搜尋關鍵字 -> {SEARCH_KEYWORD}\n')
        WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
            ec.presence_of_element_located((By.CLASS_NAME, 'searchboxinput'))
        )
        search_box = driver.find_element(By.CLASS_NAME, 'searchboxinput')
        search_box.send_keys(SEARCH_KEYWORD)
        search_box.send_keys(Keys.ENTER)

        # 取得所有搜尋結果所在的'容器'物件
        print('\r正在取得搜尋結果...(可能會花費較多時間)', end='')
        WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
            ec.presence_of_element_located((By.CLASS_NAME, 'Nv2PK'))
        )
        container_search_result = driver.find_element(By.XPATH, '/html/body/div[1]/div[3]/div[8]/div[9]/div/div/div[1]/div[2]/div/div[1]/div/div/div[1]/div[1]')

        # 紀錄爬取評論的等待時間
        start_time = time.time()
        current_results = 0
        finish_scroll = True

        # 向下捲動瀏覽所有搜尋結果
        if ENABLE_SCROLL_DOWN:
            while True:
                if len(driver.find_elements(By.CLASS_NAME, 'HlvSq')) > 0:
                    break
                ActionChains(driver).move_to_element(
                    container_search_result.find_elements(By.CLASS_NAME, 'Nv2PK')[-1]).perform()
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
    # 地圖連結
    urls = [title.get_attribute('href') for title in element_search_title]

# 主爬蟲
urls = to_map_url(urls)
max_count = len(urls)

# 儲存本次查詢瀏覽連結(DEBUG)
if need_to_save_urls:
    with open('urls.txt', 'w+', encoding='utf-8') as f:
        contents = ''
        for i in range(len(urls)):
            contents += f'{str(i + 1).zfill(len(str(len(urls))))} | {urls[i]}\n'
        f.write(contents)

for i in range(max_count):
    # 瀏覽器開啟並切換至新視窗
    # driver.switch_to.new_window('tab')

    # 瀏覽器載入指定的商家地圖連結
    driver.get(urls[i])

    # 直到商家名稱顯示(無最大等候時間)
    while True:
        WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
            ec.presence_of_element_located((By.CLASS_NAME, 'DUwDvf'))
        )
        # 地點名稱
        title = driver.find_element(By.CLASS_NAME, 'DUwDvf').text
        if REPAIR_DATA:
            delete_all_records(connection, title)
        if title.strip() != '': break
        time.sleep(0.1)

    # 確認是否為特殊商家
    # if wait_for_element(By.CLASS_NAME, 'J8zHNe') is not None:
    #     print(f'\r【🌟特殊性】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
    #     continue

    # 讀取標籤按鈕
    if wait_for_element(By.CLASS_NAME, 'RWPxGd') is None:
        print(f'\r【🆖無標籤】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
        continue
    tabs = driver.find_element(By.CLASS_NAME, 'RWPxGd').find_elements(By.CLASS_NAME, 'hh2c6')
    tabs_name = [tab.find_element(By.CLASS_NAME, 'Gpq6kf').text for tab in tabs]

    # 標籤按鈕 - [總覽]/評論/簡介
    if '總覽' in tabs_name:
        tabs[tabs_name.index('總覽')].click()

    store_item = Store.Store(
        name=title,
        description=None,
        tag=None,
        preview_image=None,
        link=urls[i],
        website=None,
        phone_number=None,
        last_update=None
    )
    rate_item = Rate.Rate(
        store_id=None,
        avg_ratings=0.0,
        total_ratings=0,
        sample_ratings=0,
        total_comments=0,
        real_ratings=0.0,
        environment_rating=0.0,
        price_rating=0.0,
        product_rating=0.0,
        service_rating=0.0,
        store_responses=0
    )
    location_item = Location.Location(
        store_id=None,
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
    match (store_item.check_if_sample(connection, MAXIMUM_SAMPLES)):
        case 'is_exists':
            print(f'\r【💡已存在】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
            continue
        # case 'not_exists':
        case 'need_repair', 'ref_location':
            is_repairing = True
            print(f'\r正在移除不完整的資料...', end='')
            delete_all_records(connection, escape_quotes(title))
        # case 'ref_location':
        #     print(f'\r【🌐參照點】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')

    ### 取得標籤資訊 ###
    print('\r正在取得地點資訊...', end='')
    labels = {
        '地址': None,
        '網站': None,
        '電話號碼': None,
        'Plus Code': None
    }
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'lfPIob'))
    )
    tags = driver.find_elements(By.CLASS_NAME, 'RcCsl')
    for tag in tags:
        items = tag.find_elements(By.CLASS_NAME, 'CsEnBe')
        if not items: continue
        label = items[0].get_attribute('aria-label')
        href = items[0].get_attribute('href')
        if label and ': ' in label:
            name, value = label.strip().split(': ', 1)
            labels[name] = href if href else value

    ### 營業資訊標籤 ###
    print('\r正在取得營業資訊...', end='')
    open_hours_tag = driver.find_elements(By.CLASS_NAME, 'OqCZI')
    open_hours_dict = {}
    if open_hours_tag:
        # 更新時間
        update_info = driver.find_elements(By.CLASS_NAME, 'zaf2le')
        if update_info: store_item._last_update = re.findall(r'\d+ \D+前', update_info[0].text.strip())[0]
        # 營業時間
        open_hours_tag[0].click()  # (沒打開標籤會抓不到元素文字)
        days_of_week = driver.find_element(By.CLASS_NAME, 't39EBf').find_elements(By.CLASS_NAME, 'y0skZc')
        for day in days_of_week:
            # 星期
            day_of_week = day.find_element(By.CLASS_NAME, 'ylH6lf').find_element(By.TAG_NAME, 'div').text
            # 時間
            open_hours_list = [
                {'open': time.text.split('–')[0], 'close': time.text.split('–')[1]}
                for time in day.find_elements(By.CLASS_NAME, 'G8aQO') if ':' in time.text
            ]
            open_hours_dict[day_of_week] = open_hours_list if open_hours_list else None

    # 商家相片
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'ZKCDEc'))
    )
    store_img1 = driver.find_elements(By.CLASS_NAME, 'ZKCDEc')
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'aoRNLd'))
    )
    store_img2 = store_img1[0].find_elements(By.CLASS_NAME, 'aoRNLd')
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.TAG_NAME, 'img'))
    )
    store_img3 = store_img2[0].find_element(By.TAG_NAME, 'img')
    store_item._preview_image = store_img3.get_attribute('src').split('/')[-1] if len(store_img1) > 0 else None

    # 商家欄位資料(可能為永久歇業/暫時關閉)
    store_state = driver.find_elements(By.CLASS_NAME, 'fCEvvc')
    if len(store_state) > 0:
        store_item._tag = store_state[0].text
    else:
        store_tag = wait_for_element(By.CLASS_NAME, 'DkEaL')
        store_item._tag = store_tag.text if store_tag else None

    store_item._website = labels['網站']
    if labels['電話號碼']: store_item._phone_number = labels['電話號碼'].replace(' ', '-')

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

    if TARGET_CITY != '' and location_item._city == TARGET_CITY:
        print(f'\r【🌍範圍外】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
        continue

    get_comments_type = ''
    current_comments = 0

    service_item = Service.Service(
        store_id=None,
        dine_in=None,
        take_away=None,
        delivery=None
    )

    # 標籤按鈕 - 總覽/評論/[簡介]
    if '簡介' in tabs_name:
        tabs[tabs_name.index('簡介')].click()

        # 讀取商家簡介 (選擇性)
        description = wait_for_element(By.CLASS_NAME, 'PbZDve')
        if description: store_item._description = description.find_element(By.CLASS_NAME, 'ZqFyf').find_element(
            By.TAG_NAME, 'span').text

        # 讀取商家細節
        ns = driver.find_elements(By.CLASS_NAME, 'WeoVJe')
        ys = driver.find_elements(By.CLASS_NAME, 'hpLkke')
        services_no = [service.text for service in ns] if ns else []
        services_yes = [service.text for service in ys] if ys else []

        service_item._dine_in = has_service('內用', services_yes, services_no)
        service_item._take_away = has_service('外帶', services_yes, services_no)
        service_item._delivery = has_service('外送', services_yes, services_no)

    # 儲存商家資料，並取得其 store_id
    if store_item._tag:
        tag_item = Tag.Tag(
            tag=store_item._tag,
            category=None
        ).insert_if_not_exists(connection)
    try:
        store_item.insert_if_not_exists(connection)
    except Exception as e:
        print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {e} - {title}\n', end='')
        continue

    store_id = store_item.get_id(connection)
    rate_item._store_id = store_id
    location_item._store_id = store_id
    service_item._store_id = store_id

    # 標籤按鈕 - 總覽/[評論]/簡介
    if '評論' in tabs_name:
        tabs[tabs_name.index('評論')].click()
        # 取得評論星級
        rating = wait_for_element(By.CLASS_NAME, 'jANrlb')
        if rating:
            rate_item._avg_ratings = float(
                ''.join(re.findall(r'[0-9]+[.][0-9]+', rating.find_element(By.CLASS_NAME, 'fontDisplayLarge').text)))
            rate_item._total_ratings = int(
                ''.join(re.findall(r'[0-9]+', rating.find_element(By.CLASS_NAME, 'fontBodySmall').text)))

    if rate_item.total_ratings < MINIMUM_SAMPLES:
        print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | 評論總數低於最低樣本數 - {title}\n',
              end='')
        continue

    keyword_items = []
    contrib_items = []
    comment_items = []
    if rate_item.total_ratings > 0:
        # 評論面板
        WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
            ec.presence_of_element_located((By.CLASS_NAME, 'dS8AEf'))
        )
        commentContainer = driver.find_element(By.CLASS_NAME, 'dS8AEf')
        # 取得關鍵字
        keywords_elements = wait_for_element(By.CLASS_NAME, 'e2moi')
        if keywords_elements:
            for keyword in commentContainer.find_elements(By.CLASS_NAME, 'e2moi'):
                count = keyword.find_elements(By.CLASS_NAME, 'bC3Nkc')
                if len(count) == 0: continue
                kw = keyword.find_element(By.CLASS_NAME, 'uEubGf').text
                if len(kw) > 20 or kw.isnumeric(): continue
                keyword_items.append(Keyword.Keyword(
                    store_id=store_id,
                    word=kw,
                    count=int(count[0].text),
                    source='DEFAULT'
                ))

        if not switch_to_order(order_type='最相關'):
            print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | 最相關留言切換失敗 - {title}\n', end='')
            continue

        # 紀錄爬取評論的等待時間
        start_time = time.time()
        # 滾動評論面板取得所有評論
        while True:
            ActionChains(driver).move_to_element(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')[-1]).perform()
            commentContainer.send_keys(Keys.PAGE_DOWN)
            comments = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
            comments = comments[:MAXIMUM_SAMPLES]
            time.sleep(0.2)
            # 檢查是否持續一段時間皆未出現新的評論(卡住)
            if current_comments != len(comments): start_time = time.time()
            current_comments = len(comments)
            if time.time() - start_time > (MAXIMUM_TIMEOUT + (current_comments ** 0.5) / 2):
                get_comments_type = 'timeout'
                break
            # 按下「全文」以展開過長的評論內容
            for comment in comments:
                expand_comment = comment.find_elements(By.CLASS_NAME, 'w8nwRe')
                if len(expand_comment) > 0: expand_comment[0].send_keys(Keys.ENTER)
            print(f'\r正在取得所有評論(%d/%d) | {store_item.name}...' % (len(comments), rate_item.total_ratings), end='')
            if len(comments) >= rate_item.total_ratings:
                get_comments_type = 'all'
                break
            elif len(comments) >= MAXIMUM_SAMPLES:
                get_comments_type = 'sample'
                break

        # 提取評論內容
        sum_score = 0
        sum_responses = 0
        filtered_comments = [
            c for c in limit_list(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf'), MAXIMUM_SAMPLES)
            if len(c.find_elements(By.CLASS_NAME, 'MyEned')) > 0
        ]  # 沒留言的不會爬
        for index in range(len(filtered_comments)):
            try:
                score = 0
                comment_time = ''
                if len(filtered_comments[index].find_elements(By.CLASS_NAME, 'kvMYJc')) > 0:
                    score = int(filtered_comments[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
                    comment_time = filtered_comments[index].find_element(By.CLASS_NAME, 'rsqaWe').text
                else:
                    score = int(filtered_comments[index].find_element(By.CLASS_NAME, 'fzvQIb').text.split('/')[0])
                    comment_time = filtered_comments[index].find_element(By.CLASS_NAME, 'xRkPPb').text.split('(')[0].strip()
                sum_score += score
                sum_responses += 1 if len(filtered_comments[index].find_elements(By.CLASS_NAME, 'CDe7pd')) > 0 else 0
                contrib_id = filtered_comments[index].find_element(By.CLASS_NAME, 'al6Kxe').get_attribute('data-href').split('/')[-2]
                # 取得評論者結構
                level = re.search(r'ba(?P<level>\d+)', filtered_comments[index].find_element(By.CLASS_NAME, 'NBa7we').get_attribute('src'))
                contrib_items.append(Contributor.Contributor(
                    uid=contrib_id,
                    level=int(level.group('level')) + 2 if level else 0
                ))
                # 取得留言結構
                comment_items.append(Comment.Comment(
                    store_id=store_id,
                    sort=index + 1,
                    contents=filtered_comments[index].find_element(By.CLASS_NAME, 'MyEned').find_element(By.CLASS_NAME, 'wiI7pd').text,
                    time=comment_time,
                    rating=score,
                    contributor_id=contrib_id
                ))
            finally:
                pass

        rate_item._store_responses = sum_responses
        rate_item._total_comments = len(filtered_comments)
        rate_item._sample_ratings = current_comments
        if len(filtered_comments): rate_item._real_ratings = round(sum_score / len(filtered_comments), 1)

        if rate_item.sample_ratings < MINIMUM_SAMPLES:
            print(
                f'\r【❌已失敗】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | 樣本總數低於最低樣本數 - {title}\n',
                end='')
            continue

    # 等待網址列顯示座標位置後取得座標位置
    print('\r正在取得地點座標...', end='')
    while True:
        if '@' in driver.current_url:
            coordinate = driver.current_url.split('@')[1].split(',')[0:2]
            location_item._longitude = coordinate[0]
            location_item._latitude = coordinate[1]
            break
        time.sleep(1)

    ### 儲存至資料庫 ###
    # 營業時間
    for day_of_week, open_list in open_hours_dict.items():
        if open_list:
            for open_time in open_list:
                openhours_item = OpenHours.OpenHours(
                    store_id=store_id,
                    day_of_week=day_of_week,
                    open_time=open_time['open'],
                    close_time=open_time['close']
                ).insert(connection)
        else:
            openhours_item = OpenHours.OpenHours(
                store_id=store_id,
                day_of_week=day_of_week,
                open_time=None,
                close_time=None
            ).insert(connection)
    # 服務
    service_item.insert_if_not_exists(connection)
    # 關鍵字
    for index in range(len(keyword_items)):
        print(f'\r正在儲存關鍵字結構(%d/%d)...' % (index + 1, len(keyword_items)), end='')
        keyword_items[index].insert_if_not_exists(connection)
    # 貢獻者
    for index in range(len(contrib_items)):
        print(f'\r正在儲存貢獻者結構(%d/%d)...' % (index + 1, len(contrib_items)), end='')
        contrib_items[index].insert_if_not_exists(connection)
    # 評論
    for index in range(len(comment_items)):
        print(f'\r正在儲存評論結構(%d/%d)...' % (index + 1, len(comment_items)), end='')
        comment_items[index].insert(connection)
    # 評分
    rate_item.insert_if_not_exists(connection)
    # 地點
    location_item.insert_if_not_exists(connection)

    ### 評估完成狀態 ###
    if rate_item.total_ratings == 0:
        print(f'\r【📝無評論】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
    else:
        match get_comments_type:
            case 'all':
                if is_repairing:
                    print(f'\r【🛠️已修復】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({rate_item.total_ratings})\n', end='')
                else:
                    print(f'\r【✅已完成】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({rate_item.total_ratings})\n', end='')
            case 'sample':
                if is_repairing:
                    print(f'\r【🛠️已修復】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({rate_item.total_ratings})\n', end='')
                else:
                    print(f'\r【📝已抽樣】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({current_comments}/{rate_item.total_ratings})\n', end='')
            case 'timeout':
                print(f'\r【⏱️已超時】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({current_comments}/{rate_item.total_ratings})\n', end='')

    # driver.close()
    # driver.switch_to.window(driver.window_handles[0])
    driver.refresh()

print('\r已儲存所有搜尋結果的資料！', end='')
driver.close()
connection.close()
sys.exit(ReturnCode.Success)
