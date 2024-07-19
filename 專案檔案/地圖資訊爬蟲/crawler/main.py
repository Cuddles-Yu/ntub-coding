### 匯入模組 ###
import time
from datetime import datetime

from module.functions import *

# 資料表
from tables import Administrator, Comment, Favorite, Keyword, Location, Member, Rate, Service, Store, Tag, OpenHours
from tables._base import *

# 資料庫操作
from module.delete_database import *
import module.create_database as db
import module.modify_database as mdb

# 網頁爬蟲
from selenium.webdriver.common.keys import Keys
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
driver = init_driver()

if REPAIR_DATA:
    urls = mdb.get_urls_from_incomplete_store(connection)
    if not urls:
        print(f'查無需資料修復之商家，程式將自動結束...')
        crawler_exit(driver, connection)
    print(f'已開啟資料修復模式 -> 共{len(urls)}個')
else:
    urls = STORES_URLS if STORES_URLS else []

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
        if REPAIR_DATA: reset_store(connection, title)
        if title.strip() != '': break
        time.sleep(0.1)

    # 確認是否為特殊商家
    # if wait_for_element(By.CLASS_NAME, driver, 'J8zHNe') is not None:
    #     print(f'\r【🌟特殊性】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
    #     continue

    store_item = Store.newObject(title, urls[i])

    ### 檢查資料庫中是否已經存在指定的商家 ###
    is_repairing = False
    if store_item.exists(connection):
        crawler_state, crawler_description = store_item.get_state(connection)
        match crawler_state:
            case '成功' | '抽樣' | '超時':
                print(f'\r【⭐已存在】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({crawler_state})\n', end='')
                continue
            case '建立':
                print(f'\r正在準備重新爬取資料...', end='')
                reset_store(connection, store_item.name)
            case _:
                is_repairing = True
                print(f'\r正在移除不完整的資料...', end='')
                reset_store(connection, store_item.name)

            # print(f'\r【🌐參照點】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')

    START_TIME = datetime.now()

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
        if to_bool(open_hours_tag.find_element(By.CLASS_NAME, 'OMl5r').get_attribute('aria-expanded')): open_hours_tag.click() # (確保標籤關閉以取得更新時間)
        update_info = driver.find_elements(By.CLASS_NAME, 'zaf2le')
        if update_info: store_item._last_update = re.findall(r'\d+ \D+前', update_info[0].text.strip())[0]
        # 營業時間
        if not to_bool(open_hours_tag.find_element(By.CLASS_NAME, 'OMl5r').get_attribute('aria-expanded')): open_hours_tag.click()  # (沒打開標籤會抓不到元素文字)
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
    if filtered_tags: wait_for_click(By.CLASS_NAME, driver, 'hYBOP')  # 返回

    # 讀取標籤按鈕
    if wait_for_element(By.CLASS_NAME, driver, 'RWPxGd') is None:
        print(f'\r【🆖無標籤】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
        continue
    tabs = driver.find_element(By.CLASS_NAME, 'RWPxGd').find_elements(By.CLASS_NAME, 'hh2c6')
    tabs_name = [tab.find_element(By.CLASS_NAME, 'Gpq6kf').text for tab in tabs]

    # 標籤按鈕 - [總覽]/評論/簡介
    if '總覽' in tabs_name: tabs[tabs_name.index('總覽')].click()

    rate_item = Rate.newObject()
    location_item = Location.newObject()

    ### 商家欄位資料 ###
    store_state = driver.find_elements(By.CLASS_NAME, 'fCEvvc')
    if len(store_state) > 0:
        store_item._tag = store_state[0].text
    else:
        store_tag = wait_for_element(By.CLASS_NAME, driver, 'DkEaL')
        store_item._tag = store_tag.text if store_tag else None
    # 可能為永久歇業/暫時關閉
    if any(pass_tag in store_item.get_tag() for pass_tag in PASS_TAGS):
        print(f'\r【⛔休業中】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
        continue

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

    store_item._website = labels['網站']
    if labels['電話號碼']: store_item._phone_number = labels['電話號碼'].replace(' ', '-')

    # 地點欄位資料
    if labels['地址']:
        postal, city, district, detail = get_split_from_address(labels['地址'])
        location_item._postal_code = postal
        location_item._city = city
        location_item._dist = district
        location_item._details = detail
    else:
        print(f'\r【🗺️無地址】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
        continue

    if labels['Plus Code']:
        village = re.search(r'(?P<village>\S+里)', labels['Plus Code'])
        location_item._vil = village.group('village') if village else None

    if TARGET_CITY != '' and location_item.get_city() == TARGET_CITY:
        print(f'\r【🌍範圍外】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
        continue

    ### 商家相片 ###
    print('\r正在取得商家相片...', end='')
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
    store_item._preview_image = store_img3.get_attribute('src') if len(store_img1) > 0 else None

    ### 服務項目 ###
    print('\r正在取得服務項目...', end='')
    service_dict = {}
    if '簡介' in tabs_name:
        # 標籤按鈕 - 總覽/評論/[簡介]
        tabs[tabs_name.index('簡介')].click()
        # 商家簡介 (選擇性)
        description = wait_for_element(By.CLASS_NAME, driver, 'PbZDve')
        if description: store_item._description = description.find_element(By.CLASS_NAME, 'ZqFyf').find_element(By.TAG_NAME, 'span').text
        # 服務類別
        for category in driver.find_elements(By.CLASS_NAME, 'iP2t7d'):
            category_name = category.find_element(By.CLASS_NAME, 'iL3Qke').text
            # 服務項目
            for service in category.find_elements(By.CLASS_NAME, 'WeoVJe'):  # 沒有提供的服務
                service_dict[service.find_element(By.TAG_NAME, 'span').text] = (category_name, 0)
            for service in category.find_elements(By.CLASS_NAME, 'hpLkke'):  # 所有提供的服務
                if service_dict.get(service.find_element(By.TAG_NAME, 'span').text) is None: service_dict[service.find_element(By.TAG_NAME, 'span').text] = (category_name, 1)

    ### 儲存至'關鍵字'資料表
    if store_item.get_tag():
        tag_item = Tag.Tag(
            tag=store_item.get_tag(),
            category=None
        ).insert_if_not_exists(connection)

    ### 儲存商家資料，並取得其 store_id ###
    try:
        store_item.update_if_exists(connection)
    except Exception as e:
        print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {e} - {title}\n', end='')
        continue
    store_id = store_item.get_id(connection)
    rate_item._store_id = store_id
    location_item._store_id = store_id

    # 標籤按鈕 - 總覽/[評論]/簡介
    print('\r正在取得商家評論...', end='')
    if '評論' in tabs_name:
        tabs[tabs_name.index('評論')].click()
        # 取得評論星級
        rating = wait_for_element(By.CLASS_NAME, driver, 'jANrlb')
        if rating:
            rate_item._avg_rating = float(''.join(re.findall(r'[0-9]+[.][0-9]+', rating.find_element(By.CLASS_NAME, 'fontDisplayLarge').text)))
            rate_item._total_reviews = int(''.join(re.findall(r'[0-9]+', rating.find_element(By.CLASS_NAME, 'fontBodySmall').text)))

    if rate_item.total_reviews == 0:
        print(f'\r【📝無評論】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')
        store_item.change_state(connection, '失敗', '評論總數為零')

    if rate_item.total_reviews < MINIMUM_SAMPLES:
        print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | 評論總數低於最低樣本數 - {title}\n', end='')
        store_item.change_state(connection, '失敗', '評論總數不足')
        continue

    comment_items = []
    keywords_dict = {}
    if rate_item.total_reviews > 0:
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

        if not switch_to_order(order_type='最相關'):
            print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | 最相關留言切換失敗 - {title}\n', end='')
            store_item.change_state(connection, '失敗', '最相關留言切換失敗')
            continue

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
                if not ('年' in c.find_element(By.CLASS_NAME, 'rsqaWe').text and int(re.findall(r'\d+', c.find_element(By.CLASS_NAME, 'rsqaWe').text)[0]) > MAX_COMMENT_YEARS)  # 有文字內容(包含分享按鈕)
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
            print(f'\r正在取得所有評論(留言:{current_total_withcomments}/過濾:{current_filtered_reviews_count}/瀏覽:{current_total_reviews_count}/總共:{rate_item.total_reviews}) | {store_item.name}...', end='')
            if len(total_reviews) >= rate_item.total_reviews:
                get_comments_type = 'all'
                break
            if MAXIMUM_SAMPLES > 0:
                if len(total_withcomments) >= MAXIMUM_SAMPLES:
                    get_comments_type = 'sample'
                    break

        # 紀錄並拆分評論元素
        mixed_reviews = limit_list(filtered_reviews, MAXIMUM_SAMPLES)
        total_withoutcomments = [
            c for c in mixed_reviews
            if not c.find_elements(By.CLASS_NAME, 'Upo0Ec')  # 沒文字內容(不包含分享按鈕)
        ]
        # (在混合留言裡面包含文字內容的數量)
        mix_comments_count = len([
            c for c in mixed_reviews
            if c.find_elements(By.CLASS_NAME, 'Upo0Ec')  # 沒文字內容(不包含分享按鈕)
        ])
        additional_comments = limit_list([
            c for c in exclude_list(filtered_reviews, MAXIMUM_SAMPLES)
            if c.find_elements(By.CLASS_NAME, 'Upo0Ec')  # 有文字內容(包含分享按鈕)
        ], MAXIMUM_SAMPLES - mix_comments_count)
        total_samples = mixed_reviews + additional_comments
        rate_item._total_browses = len(total_reviews)
        rate_item._total_samples = len(total_samples)
        rate_item._total_withcomments = len(total_withcomments)
        rate_item._total_withoutcomments = len(total_withoutcomments)
        rate_item._mixreviews_count = len(mixed_reviews)
        rate_item._additionalcomments_count = len(additional_comments)

        if rate_item.total_samples < MINIMUM_SAMPLES:
            print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | 評論樣本少於最低需求{MINIMUM_SAMPLES}個 - {title}\n', end='')
            store_item.change_state(connection, '失敗', '評論樣本不足')
            continue

        # 提取評論內容
        sum_score = 0
        sum_responses = 0
        for index in range(len(total_samples)):
            try:
                print(f'\r正在提取所有評論內容({index}/{len(total_samples)})...', end='')
                score = 0
                comment_time = ''
                if total_samples[index].find_elements(By.CLASS_NAME, 'kvMYJc'):
                    score = int(total_samples[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
                    comment_time = total_samples[index].find_element(By.CLASS_NAME, 'rsqaWe').text
                else:
                    print('特殊狀況')
                    score = int(total_samples[index].find_element(By.CLASS_NAME, 'fzvQIb').text.split('/')[0])
                    comment_time = total_samples[index].find_element(By.CLASS_NAME, 'xRkPPb').text.split('(')[0].strip()
                level = re.search(r'ba(?P<level>\d+)', total_samples[index].find_element(By.CLASS_NAME, 'NBa7we').get_attribute('src'))
                # 取得留言結構
                user_experiences_dict = {}
                user_experiences = total_samples[index].find_elements(By.CLASS_NAME, 'PBK6be')
                for ue in user_experiences:
                    line = ue.find_elements(By.CLASS_NAME, 'RfDO5c')
                    match (len(line)):
                        case 1:
                            span = line[0].find_element(By.TAG_NAME, 'span').text.split('：')
                            if span[0] in EXPERIENCE_TARGET and span[1]:
                                numbers = re.findall(r'\d+', span[1])
                                if numbers: user_experiences_dict[span[0]] = int(numbers[0])
                        case _:
                            experience = []
                            for review_tag in line:
                                experience.append(review_tag.find_element(By.TAG_NAME, 'span').text)
                            if experience[0] in RECOMMEND_DISHES and experience[1]:
                                dishes = re.findall(r'[^,&\s]+', experience[1])
                                for dish in dishes:
                                    keywords_dict[dish] = (keywords_dict.get(dish)[0]+1, 'recommend') if keywords_dict.get(dish) else (1, 'recommend')
                contents_element = total_samples[index].find_elements(By.CLASS_NAME, 'MyEned')
                contents = contents_element[0].find_element(By.CLASS_NAME, 'wiI7pd').text if contents_element else None
                sum_score += score if contents_element else 0
                sum_responses += 1 if total_samples[index].find_elements(By.CLASS_NAME, 'CDe7pd') else 0
                # 儲存評論物件
                comment_items.append(Comment.Comment(
                    store_id=store_id,
                    index=index + 1,
                    contents=contents,
                    has_image=1 if total_samples[index].find_elements(By.CLASS_NAME, 'KtCyie') else 0,
                    time=comment_time,
                    rating=score,
                    food_rating=user_experiences_dict.get(EXPERIENCE_TARGET[0]) if user_experiences_dict else None,
                    service_rating=user_experiences_dict.get(EXPERIENCE_TARGET[1]) if user_experiences_dict else None,
                    atmosphere_rating=user_experiences_dict.get(EXPERIENCE_TARGET[2]) if user_experiences_dict else None,
                    contributor_level=int(level.group('level')) + 2 if level else 0,
                    environment_state=None,
                    price_state=None,
                    product_state=None,
                    service_state=None
                ))
            finally:
                pass

        rate_item._store_responses = sum_responses
        if len(total_withcomments): rate_item._real_ratings = round(sum_score / len(total_withcomments), 1)

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
    print('\r正在儲存營業時間結構...', end='')
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
    print('\r正在儲存服務項目結構...', end='')
    for properties, state in service_dict.items():
        service_item = Service.Service(
            store_id=store_id,
            properties=properties,
            category=state[0],
            state=state[1]
        ).insert(connection)
    # 關鍵字
    print(f'\r正在儲存關鍵字結構...', end='')
    for word, value in keywords_dict.items():
        Keyword.Keyword(
            store_id=store_id,
            word=word,
            count=value[0],
            source=value[1],
            image_url=None,
            source_url=None
        ).insert_if_not_exists(connection)
    # 評論
    for index in range(len(comment_items)):
        print(f'\r正在儲存評論結構(%d/%d)...' % (index + 1, len(comment_items)), end='')
        comment_items[index].insert(connection)
    # 評分
    print('\r正在儲存評分資料...', end='')
    rate_item.insert_if_not_exists(connection)
    # 地點
    print('\r正在儲存地點資料...', end='')
    location_item.insert_if_not_exists(connection)

    # 計算時間差
    TIME_DIFFERENCE = datetime.now() - START_TIME
    MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

    ### 評估完成狀態 ###
    match get_comments_type:
        case 'all':
            if is_repairing:
                print(f'\r【🛠️已修復】耗時:{MINUTES_ELAPSE:.2f}分鐘 | {str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({rate_item.total_reviews})\n', end='')
                store_item.change_state(connection, '成功', '取得完整資料(修復)')
            else:
                print(f'\r【✅已完成】耗時:{MINUTES_ELAPSE:.2f}分鐘 | {str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({rate_item.total_reviews})\n', end='')
                store_item.change_state(connection, '成功', '取得完整資料')
        case 'sample':
            if is_repairing:
                print(f'\r【🛠️已修復】耗時:{MINUTES_ELAPSE:.2f}分鐘 | {str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({rate_item.total_reviews})\n', end='')
                store_item.change_state(connection, '抽樣', '取得樣本資料(修復)')
            else:
                print(f'\r【📝已抽樣】耗時:{MINUTES_ELAPSE:.2f}分鐘 | {str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} ({rate_item.total_samples}/{rate_item.total_reviews})\n', end='')
                store_item.change_state(connection, '抽樣', '取得樣本資料')
        case 'timeout':
            print(f'\r【⏱️已超時】耗時:{MINUTES_ELAPSE:.2f}分鐘 | {str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title} (留言:{current_total_withcomments}/過濾:{current_filtered_reviews_count}/瀏覽:{current_total_reviews_count}/總共:{rate_item.total_reviews})\n', end='')
            store_item.change_state(connection, '超時', '超出爬蟲時間限制')

    # driver.close()
    # driver.switch_to.window(driver.window_handles[0])
    driver.refresh()

print('\r已儲存所有搜尋結果的資料！', end='')
crawler_exit(driver, connection)
