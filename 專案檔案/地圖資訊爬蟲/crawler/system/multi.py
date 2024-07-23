### 匯入模組 ###
import time
from datetime import datetime
from multiprocessing import Process

from module.functions import *

# 資料表
from tables import Administrator, Comment, Favorite, Keyword, Location, Member, Rate, Service, Store, Tag, OpenHours
from tables.base import *

# 資料庫操作
# from module.delete_database import *
# import module.create_database as db
# import module.modify_database as mdb

# 網頁爬蟲
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.edge.service import Service
from selenium.webdriver.edge.options import Options
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.support.ui import WebDriverWait

ORDER_TYPE = {
    '最相關': 0,
    '最新': 1,
    '評分最高': 2,
    '評分最低': 3
}
def switch_to_order(driver, order_type: str) -> bool:
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

def scrape(url):
    options = webdriver.EdgeOptions()
    options.add_experimental_option("detach", True)
    options.add_argument('--window-size=950,1020')
    # options.add_argument("--headless")  # 不顯示視窗
    driver = webdriver.Edge(options=options)
    driver.get(url)
    driver.set_window_position(x=970, y=10)

    # 等待頁面加載完成後，進行所需的操作
    # 直到商家名稱顯示(無最大等候時間)
    while True:
        WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
            ec.presence_of_element_located((By.CLASS_NAME, 'DUwDvf'))
        )
        # 地點名稱
        title = driver.find_element(By.CLASS_NAME, 'DUwDvf').text
        if title.strip() != '': break
        time.sleep(0.1)

    print(title)

    store_item = Store.newObject(title, url)

    ### 營業資訊標籤 ###
    print('\r正在取得營業資訊...', end='')
    # 檢查標籤狀態
    filtered_tags = [
        t for t in driver.find_elements(By.CLASS_NAME, 'RcCsl')
        if t.find_elements(By.CLASS_NAME, 'HlvSq')  # 新版標籤
    ]
    if filtered_tags: filtered_tags[0].click()
    # 取得營業資訊
    open_hours_tag = wait_for_element(By.CLASS_NAME, driver, 'OqCZI')
    open_hours_dict = {}
    if open_hours_tag:
        # 更新時間
        if to_bool(open_hours_tag.find_element(By.CLASS_NAME, 'OMl5r').get_attribute(
            'aria-expanded')): open_hours_tag.click()  # (確保標籤關閉以取得更新時間)
        update_info = driver.find_elements(By.CLASS_NAME, 'zaf2le')
        if update_info: store_item._last_update = re.findall(r'\d+ \D+前', update_info[0].text.strip())[0]
        # 營業時間
        if not to_bool(open_hours_tag.find_element(By.CLASS_NAME, 'OMl5r').get_attribute(
            'aria-expanded')): open_hours_tag.click()  # (沒打開標籤會抓不到元素文字)
        days_of_week = driver.find_element(By.CLASS_NAME, 't39EBf').find_elements(By.CLASS_NAME, 'y0skZc')
        for day in days_of_week:
            # 星期
            day_of_week = day.find_element(By.CLASS_NAME, 'ylH6lf').find_element(By.TAG_NAME, 'div').text
            # 時間
            open_hours_list = [
                {'open': t.text.split('–')[0], 'close': t.text.split('–')[1]}
                for t in day.find_elements(By.CLASS_NAME, 'G8aQO') if ':' in t.text
            ]
            open_hours_dict[day_of_week] = open_hours_list if open_hours_list else None
    if filtered_tags: wait_for_click(By.CLASS_NAME, driver, 'hYBOP')  # 返回
    print(open_hours_dict)

    # 讀取標籤按鈕
    if wait_for_element(By.CLASS_NAME, driver, 'RWPxGd') is None:
        return
    tabs = driver.find_element(By.CLASS_NAME, 'RWPxGd').find_elements(By.CLASS_NAME, 'hh2c6')
    tabs_name = [tab.find_element(By.CLASS_NAME, 'Gpq6kf').text for tab in tabs]

    rate_item = Rate.newObject()
    location_item = Location.newObject()

    # 標籤按鈕 - 總覽/[評論]/簡介
    print('\r正在取得商家評論...', end='')
    if '評論' in tabs_name:
        tabs[tabs_name.index('評論')].click()
        # 取得評論星級
        rating = wait_for_element(By.CLASS_NAME, driver, 'jANrlb')
        if rating:
            rate_item._avg_rating = float(
                ''.join(re.findall(r'[0-9]+[.][0-9]+', rating.find_element(By.CLASS_NAME, 'fontDisplayLarge').text)))
            rate_item._total_reviews = int(
                ''.join(re.findall(r'[0-9]+', rating.find_element(By.CLASS_NAME, 'fontBodySmall').text)))

    comment_items = []
    keywords_dict = {}
    # 評論面板
    WebDriverWait(driver, MAXIMUM_TIMEOUT).until(
        ec.presence_of_element_located((By.CLASS_NAME, 'dS8AEf'))
    )
    commentContainer = driver.find_element(By.CLASS_NAME, 'dS8AEf')
    # 取得關鍵字
    keywords_elements = wait_for_element(By.CLASS_NAME, driver, 'e2moi')
    if keywords_elements:
        for keyword in commentContainer.find_elements(By.CLASS_NAME, 'e2moi'):
            count = keyword.find_elements(By.CLASS_NAME, 'bC3Nkc')
            if len(count) == 0: continue
            kw = keyword.find_element(By.CLASS_NAME, 'uEubGf').text
            if len(kw) > 20 or kw.isnumeric(): continue
            keywords_dict[kw] = (int(count[0].text), 'DEFAULT')

    if not switch_to_order(driver, order_type='最相關'):
        print(f'\r【❌已失敗】 | 最相關留言切換失敗 - {title}\n', end='')
        return

    # 紀錄爬取評論的等待時間
    start_time = time.time()
    # 滾動評論面板取得所有評論
    get_comments_type = ''
    current_total_reviews_count = 0
    current_filtered_reviews_count = 0
    while True:
        ActionChains(driver).move_to_element(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')[-1]).perform()
        commentContainer.send_keys(Keys.PAGE_DOWN)
        total_reviews = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
        filtered_reviews = [
            c for c in total_reviews
            if not ('年' in c.find_element(By.CLASS_NAME, 'rsqaWe').text and int(
                re.findall(r'\d+', c.find_element(By.CLASS_NAME, 'rsqaWe').text)[0]) > MAX_COMMENT_YEARS)
            # 有文字內容(包含分享按鈕)
        ]
        total_withcomments = limit_list([
            c for c in filtered_reviews
            if c.find_elements(By.CLASS_NAME, 'Upo0Ec')  # 有文字內容(包含分享按鈕)
        ], MAXIMUM_SAMPLES)
        time.sleep(0.2)
        # 檢查是否持續一段時間皆未出現新的評論(卡住)
        if current_total_reviews_count != len(total_reviews): start_time = time.time()
        current_total_reviews_count = len(total_reviews)
        current_filtered_reviews_count = len(filtered_reviews)
        current_total_withcomments = len(total_withcomments)
        if time.time() - start_time > (MAXIMUM_TIMEOUT + (current_total_reviews_count ** 0.5) / 2):
            get_comments_type = 'timeout'
            break
        # 按下「全文」以展開過長的評論內容
        for comment in filtered_reviews:
            expand_comment = comment.find_elements(By.CLASS_NAME, 'w8nwRe')
            if expand_comment: expand_comment[0].send_keys(Keys.ENTER)
        print(
            f'\r正在取得所有評論(留言:{current_total_withcomments}/過濾:{current_filtered_reviews_count}/瀏覽:{current_total_reviews_count}/總共:{rate_item.total_reviews}) | {store_item.name}...',
            end='')
        if len(total_reviews) >= rate_item.total_reviews:
            get_comments_type = 'all'
            break
        if MAXIMUM_SAMPLES > 0:
            if len(total_withcomments) >= MAXIMUM_SAMPLES:
                get_comments_type = 'sample'
                break

    # 關閉瀏覽器
    driver.quit()

if __name__ == '__main__':
    urls = [
        'https://www.google.com.tw/maps/place/+/data=!4m7!3m6!1s0x3442abc628acc309:0xdaeadc07c3e73dc3!8m2!3d25.0392345!4d121.5562983!16s%2Fg%2F1v6wm0c5!19sChIJCcOsKMarQjQRwz3nwwfc6to',
        'https://www.google.com.tw/maps/place/+/data=!4m7!3m6!1s0x3442abbce4992007:0x7cf3c37582840510!8m2!3d25.0434248!4d121.5692041!16s%2Fg%2F11b6ryqbpg!19sChIJByCZ5LyrQjQREAWEgnXD83w',
        'https://www.google.com.tw/maps/place/+/data=!4m7!3m6!1s0x3442aba67032c2a1:0xd5619b8bfd94c886!8m2!3d25.035678!4d121.577618!16s%2Fg%2F1tfwmnlw!19sChIJocIycKarQjQRhsiU_YubYdU'
    ]

    processes = []
    for url in urls:
        p = Process(target=scrape, args=(url,))
        p.start()
        processes.append(p)

    for p in processes:
        p.join()
