### 匯入模組 ###
from datetime import datetime

from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions.EdgeDriver import EdgeDriver
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.tables import Store, Comment, Keyword, Location, Rate, Service, Tag, OpenHour

from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By

### 初始化 ###
database = SqlDatabase('mapdb', 'root', '11236018')
driver = EdgeDriver(database, url='https://www.google.com.tw/maps/preview')
urls = []

### 主程式 ###
if OPEN_DATA:
    match OPEN_DATA:
        case '環保':
            NAME_KEY = '餐廳名稱'
            ADDRESS_KEY = '餐廳地址'
            API_PATH = 'https://data.taipei/api/v1/dataset/d706f428-b2c7-4591-9ebf-9f5cd7408f47?scope=resourceAquire&limit=1000'
        case '客家':
            NAME_KEY = '客家美食餐廳店名'
            ADDRESS_KEY = '地址'
            API_PATH = 'https://data.taipei/api/v1/dataset/2f2b039d-6bff-4663-a4e5-3bd6cc98ee48?scope=resourceAquire&limit=1000'
    api_data = get_json_from_api(API_PATH)
    if api_data:
        names = [item.get(NAME_KEY) for item in api_data.get('result').get('results')]
        addresses = [item.get(ADDRESS_KEY) for item in api_data.get('result').get('results')]
        count = len(names)
        print(f'\r正在爬取API所有商家資料(共{count}個)...\n')
        for i, (name, address) in enumerate(zip(names, addresses)):
            links, store_names = driver.search_and_scroll(name, return_one=True, show_hint=False)
            if links:
                urls.extend(links)
                print(f'\r【💛已取得】{str(i + 1).zfill(len(str(count)))}/{count} | {name} | {address}\n', end='')
            else:
                print(f'\r【🤍未取得】{str(i + 1).zfill(len(str(count)))}/{count} | {name} | {address}\n', end='')
else:
    if STORES_URLS:
        urls = STORES_URLS
    else:
        if CONTINUE_CRAWLER:
            urls = database.get_urls_from_incomplete_store()
            if CONTINUE_COUNT > 0: urls = limit_list(urls, CONTINUE_COUNT)
            if not urls:
                print(f'查無需資料修復之商家，程式將自動結束...')
                driver.exit()
            print(f'資料完整性修復模式 -> 已開啟')

print(f'資料將儲存至資料庫 -> {database.name}')
if FORCE_CRAWLER: print(f'強制爬蟲模式(自動重設已存在商家) -> 已開啟')

### 查詢關鍵字後儲存查詢結果 ###
if not urls:
    print(f'正在搜尋關鍵字 -> {SEARCH_KEYWORD}\n')
    urls, store_names = driver.search_and_save_results(SEARCH_KEYWORD)

### 主爬蟲 ###
url_count = len(urls)
if urls: urls = to_map_url(urls)
if SHUFFLE_URLS: shuffle(urls)

print(urls)

print(f'\r正在準備爬取所有商家連結資料(共{url_count}個)...\n')
for i in range(url_count):
    CRAWLER_START_TIME = datetime.now()
    # 瀏覽器載入指定的商家地圖連結
    driver.get(urls[i])
    # 直到商家名稱顯示(無最大等候時間)
    while True:
        # 地點名稱
        title = driver.wait_for_text(By.CLASS_NAME, 'DUwDvf')
        if title.strip() != '': break
        time.sleep(0.1)
    store_item = Store.newObject(title, urls[i], mark=OPEN_DATA)
    store_item._crawler_state = '基本'

    ### 檢查資料庫中是否已經存在指定的商家 ###
    if store_item.exists(database):
        crawler_state, crawler_description = store_item.get_state(database)
        if crawler_description is None or FORCE_CRAWLER:
            # '建立' | '基本'
            print(f'\r正在準備重新爬取資料...', end='')
            store_item.reset(database)
        else:
            # '成功' | '抽樣' | '超時' | '特殊'
            print(f'\r【⭐已存在】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title} ({crawler_state})\n', end='')
            if OPEN_DATA: store_item.change_mark(database, OPEN_DATA)
            continue
        # print(f'\r【🌐參照點】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')

    # 讀取標籤按鈕
    tabs_elem, tabs_name = driver.get_tabs()
    if tabs_elem is None:
        print(f'\r【🈚無頁籤】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        store_item.change_crawler_state(database, '頁籤', '不包含資訊頁籤')
        continue
    else:
        if not set(SWITCH_TABS).issubset(set(tabs_name)):
            print(f'\r【🆖缺頁籤】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
            store_item.change_crawler_state(database, '頁籤', f'缺少{''.join(set(SWITCH_TABS) - set(tabs_name))}頁籤')
            continue

    # 確認是否為特殊商家
    if '價格' in tabs_name:
        print(f'\r【💎特殊性】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        store_item.change_crawler_state(database, '特殊', '結合飯店住宿服務')
        continue
    else:
        store_item._crawler_state = '基本'

    ### 商家欄位資料 ###
    store_state = driver.find_elements(By.CLASS_NAME, 'fCEvvc')
    if len(store_state) > 0:
        store_item.change_tag(database, store_state[0].text)
    else:
        information_bar = driver.wait_for_element(By.CLASS_NAME, 'tAiQdd')
        if information_bar:
            store_tag = information_bar.find_elements(By.CLASS_NAME, 'DkEaL')
            if not store_tag: store_tag = information_bar.find_elements(By.CLASS_NAME, 'mgr77e')
            store_item.change_tag(database, store_tag[0].text if store_tag else None)

    # 可能為永久歇業/暫時關閉
    if store_item.tag:
        if any(pass_tag in store_item.tag for pass_tag in PASS_TAGS):
            print(f'\r【⛔休業中】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
            store_item.change_crawler_state(database, '休業', '商家已永久停業')
            continue
        elif any(remove_tag in store_item.tag for remove_tag in REMOVE_TAGS):
            print(f'\r【🤡類別外】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
            store_item.change_crawler_state(database, '無效', '不屬於餐廳類別')
            continue

    ### 營業資訊標籤 ###
    print('\r正在取得營業資訊...', end='')
    # 檢查標籤狀態
    filtered_tags = [
        t for t in driver.find_elements(By.CLASS_NAME, 'RcCsl')
        if t.find_elements(By.CLASS_NAME, 'HlvSq')  # 新版標籤
    ]
    if filtered_tags: filtered_tags[0].click()
    # 取得營業資訊
    open_hours_tag = driver.wait_for_element(By.CLASS_NAME, 'OqCZI')
    open_hours_dict = {}
    if open_hours_tag:
        time.sleep(0.2)
        # 更新時間
        if to_bool(open_hours_tag.find_element(By.CLASS_NAME, 'OMl5r').get_attribute('aria-expanded')): open_hours_tag.click()  # (確保標籤關閉以取得更新時間)
        update_info = driver.find_elements(By.CLASS_NAME, 'zaf2le')
        if update_info:
            last_update_info = re.search(r'(?P<time>\d+\s*\D+前)', update_info[0].text.strip())
            store_item._last_update = last_update_info.group() if last_update_info else None
        time.sleep(0.2)
        # 營業時間
        if not to_bool(open_hours_tag.find_element(By.CLASS_NAME, 'OMl5r').get_attribute('aria-expanded')): open_hours_tag.click()  # (沒打開標籤會抓不到元素文字)
        days_of_week = driver.find_element(By.CLASS_NAME, 't39EBf').find_elements(By.CLASS_NAME, 'y0skZc')
        for day in days_of_week:
            # 星期
            day_of_week = day.find_element(By.CLASS_NAME, 'ylH6lf').find_element(By.TAG_NAME, 'div').text
            open_hour_info = day.find_elements(By.CLASS_NAME, 'G8aQO')
            # 時間
            open_hours_list = [
                {'open': time.text.split('–')[0], 'close': time.text.split('–')[1]}
                for time in open_hour_info if ':' in time.text
            ]
            if not open_hours_list and open_hour_info:
                if '24 小時營業' in open_hour_info[0].text: open_hours_list.append({'open': '00:00', 'close': '24:00'})
            open_hours_dict[day_of_week] = open_hours_list if open_hours_list else None
    if filtered_tags: driver.wait_for_click(By.CLASS_NAME, 'hYBOP')  # 返回

    # 重新讀取標籤按鈕
    tabs_elem, tabs_name = driver.get_tabs()

    # 標籤按鈕 - [總覽]/評論/簡介
    if '總覽' in tabs_name: driver.click_element(tabs_elem[tabs_name.index('總覽')])

    rate_item = Rate.newObject()
    location_item = Location.newObject()

    ### 取得標籤資訊 ###
    print('\r正在取得地點資訊...', end='')
    labels = {
        '地址': None,
        '網站': None,
        '電話號碼': None,
        'Plus Code': None
    }
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
    if labels['Plus Code']:
        village, city, district = get_split_from_plus_code(labels['Plus Code'])
        location_item.vil = village if village else None
        location_item.city = city
        location_item.dist = district

    if labels['地址']:
        postal, city, district, detail = get_split_from_address(labels['地址'])
        if (location_item.city and not city) or (location_item.dist and not district):
            simple_address = labels['地址']
            if location_item.get_city(): simple_address = simple_address.replace(location_item.get_city(), '')
            if location_item.get_dist(): simple_address = simple_address.replace(location_item.get_dist(), '')
            postal, detail = get_split_from_simple_address(simple_address)

        location_item.postal_code = postal if postal else None
        if not location_item.city: location_item.city = city if city else None
        if not location_item.dist: location_item.dist = district if district else None
        location_item.details = detail if detail else None

    if not (location_item.get_city() and location_item.get_dist() and location_item.get_details()):
        print(f'\r【🗺️無定位】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        store_item.change_crawler_state(database, '定位', '無法解析地址結構')
        continue

    if TARGET_CITIES and location_item.get_city() not in TARGET_CITIES:
        print(f'\r【🌍範圍外】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        store_item.change_crawler_state(database, '越界', '不支援非雙北地區')
        continue

    ### 商家相片 ###
    print('\r正在取得商家相片...', end='')
    store_img = driver.wait_for_element(By.CLASS_NAME, 'aoRNLd')
    time.sleep(0.5)
    if store_img:
        image_url = store_img.find_element(By.TAG_NAME, 'img').get_attribute('src')
        store_item._preview_image = image_url
        store_item._image = download_image_as_binary(image_url)

    ### 服務項目 ###
    print('\r正在取得服務項目...', end='')
    service_dict = {}
    if '簡介' in tabs_name:
        # 標籤按鈕 - 總覽/評論/[簡介]
        tabs_elem[tabs_name.index('簡介')].click()
        time.sleep(0.2)
        # 商家簡介 (已停用)
        # description = driver.wait_for_element(By.CLASS_NAME, 'PbZDve')
        # if description: store_item._description = description.find_element(By.CLASS_NAME, 'ZqFyf').find_element(By.TAG_NAME, 'span').text
        # 服務類別
        for category in driver.find_elements(By.CLASS_NAME, 'iP2t7d'):
            category_name = category.find_element(By.CLASS_NAME, 'iL3Qke').text
            # 服務項目
            for service in category.find_elements(By.CLASS_NAME, 'WeoVJe'):  # 沒有提供的服務
                service_dict[service.find_element(By.TAG_NAME, 'span').text] = (category_name, 0)
            for service in category.find_elements(By.CLASS_NAME, 'hpLkke'):  # 所有提供的服務
                if service_dict.get(service.find_element(By.TAG_NAME, 'span').text) is None: service_dict[service.find_element(By.TAG_NAME, 'span').text] = (category_name, 1)

    ### 儲存至'關鍵字'資料表 [已停用]
    # if store_item.tag:
    #     tag_item = Tag.Tag(
    #         tag=store_item.tag,
    #         category=None
    #     ).insert_if_not_exists(database)

    ### 儲存商家資料，並取得其 store_id ###
    try:
        store_item.update_if_exists(database)
    except Exception as e:
        print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {e} - {title}\n', end='')
        continue
    store_id = store_item.get_id(database)
    rate_item.store_id = store_id
    location_item._store_id = store_id

    # 標籤按鈕 - 總覽/[評論]/簡介
    print('\r正在取得商家評論...', end='')
    if '評論' in tabs_name:
        tabs_elem[tabs_name.index('評論')].click()
        # 取得評論星級
        rating = driver.wait_for_element(By.CLASS_NAME, 'jANrlb')
        if rating:
            rate_item.avg_rating = float(rating.find_element(By.CLASS_NAME, 'fontDisplayLarge').text)
            rate_item.total_reviews = int(''.join(re.findall(r'\d+', rating.find_element(By.CLASS_NAME, 'fontBodySmall').text)))

    if rate_item.total_reviews == 0:
        print(f'\r【📝無評論】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        store_item.change_crawler_state(database, '失敗', '評論總數為零')
    elif rate_item.total_reviews < MINIMUM_SAMPLES:
        print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 評論總數低於最低樣本數 - {title}\n', end='')
        store_item.change_crawler_state(database, '失敗', '評論總數不足')
        continue

    comments_dict = {}
    keywords_dict = {}
    if rate_item.total_reviews > 0:
        commentContainer = driver.wait_for_element(By.CLASS_NAME, 'dS8AEf')  # 評論面板
        keywords_dict = driver.get_keywords_dict()  # 取得關鍵字
        orders = {
            '最相關': {
                '最大樣本': MAXIMUM_SAMPLES,
                '統計物件': rate_item,
                '類型陣列': [1, 0, 0],
                '樣本型態': None,
                '過濾評論': 0
            },
            '評分最高': {
                '最大樣本': REFERENCE_SAMPLES,
                '統計物件': rate_item.newObject(),
                '類型陣列': [0, 1, 0],
                '樣本型態': None
            },
            '評分最低': {
                '最大樣本': REFERENCE_SAMPLES,
                '統計物件': rate_item.newObject(),
                '類型陣列': [0, 0, 1],
                '樣本型態': None
            }
        }
        for order_type, settings in orders.items():
            if order_type != '最相關' and (orders['最相關']['樣本型態'] is None or orders['最相關']['統計物件'].total_browses >= settings['統計物件'].total_reviews): break
            ### 滾動評論面板取得所有評論 ###
            driver.switch_to_order(order_type)
            # 初始變數
            start_time = time.time()
            CHECK_INTERVAL = 5  # 每次檢查評論的間隔次數
            current_total_target = 0
            current_total_reviews_count = 0
            current_filtered_reviews_count = 0
            scroll_count = 0  # 記錄捲動次數
            while True:
                driver.move_to_element(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')[-1])
                commentContainer.send_keys(Keys.PAGE_DOWN)
                total_reviews = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
                scroll_count += 1
                time.sleep(0.5)
                # 檢查是否出現新的評論
                if scroll_count % CHECK_INTERVAL == 0:
                    scroll_count = 0
                    if current_total_reviews_count != len(total_reviews):
                        start_time = time.time()
                        filtered_reviews = [
                            c for c in total_reviews
                            if not ('年' in c.find_element(By.CLASS_NAME, 'rsqaWe').text and int(re.search(r'\d+', c.find_element(By.CLASS_NAME, 'rsqaWe').text).group()) > MAX_COMMENT_YEARS)
                        ]
                        current_total_reviews_count = len(total_reviews)
                        current_filtered_reviews_count = len(filtered_reviews)

                    match order_type:
                        case '最相關':
                            total_withcomments = [
                                c for c in filtered_reviews
                                if c.find_elements(By.CLASS_NAME, HAS_COMMENT_CLASS)  # 有文字內容
                            ]
                            current_total_target = len(total_withcomments)
                            if current_total_reviews_count >= settings['統計物件'].total_reviews:
                                settings['樣本型態'] = 'all'
                                break
                            if current_total_target >= settings['最大樣本']:
                                settings['樣本型態'] = 'sample'
                                break
                        case '評分最高' | '評分最低':
                            last_score = int(total_reviews[-1].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
                            if order_type == '評分最高':
                                total_highrating_withcomments = [
                                    c for c in filtered_reviews
                                    if c.find_elements(By.CLASS_NAME, HAS_COMMENT_CLASS) and int(c.find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0]) >= HIGHRATING_SCORE
                                ]
                                current_total_target = len(total_highrating_withcomments)
                                if current_total_target >= settings['最大樣本']:
                                    settings['樣本型態'] = 'complete'
                                    break
                                if last_score < HIGHRATING_SCORE:
                                    settings['樣本型態'] = 'all'
                                    break
                            elif order_type == '評分最低':
                                total_lowrating_withcomments = [
                                    c for c in filtered_reviews
                                    if c.find_elements(By.CLASS_NAME, HAS_COMMENT_CLASS) and int(c.find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0]) <= LOWRATING_SCORE
                                ]
                                current_total_target = len(total_lowrating_withcomments)
                                if current_total_target >= settings['最大樣本']:
                                    settings['樣本型態'] = 'complete'
                                    break
                                if last_score > LOWRATING_SCORE:
                                    settings['樣本型態'] = 'all'
                                    break

                    if time.time() - start_time > (MAXIMUM_TIMEOUT + (current_total_reviews_count ** 0.5) * 0.8):
                        if current_total_reviews_count >= settings['最大樣本']:
                            settings['樣本型態'] = 'limit'
                        else:
                            settings['樣本型態'] = 'timeout'
                        break

                print(f'\r正在取得所有{order_type}評論 | 進度:{current_total_target} | 過濾:{current_filtered_reviews_count}/瀏覽:{current_total_reviews_count}/總數:{settings['統計物件'].total_reviews} | ' +
                      f'{'▮'*(scroll_count+1)}{'▯'*(CHECK_INTERVAL-scroll_count-1)} | {title}...', end='')

            ### 拆分評論元素 ###
            match order_type:
                case '最相關':
                    settings['過濾評論'] = current_filtered_reviews_count
                    mixed_reviews = limit_list(filtered_reviews, settings['最大樣本'])
                    total_withcomments = limit_list(total_withcomments, settings['最大樣本'])
                    total_withoutcomments = [
                        c for c in mixed_reviews
                        if not c.find_elements(By.CLASS_NAME, HAS_COMMENT_CLASS)  # 沒文字內容
                    ]
                    # (在混合留言裡面包含文字內容的數量)
                    mix_comments_count = len([
                        c for c in mixed_reviews
                        if c.find_elements(By.CLASS_NAME, HAS_COMMENT_CLASS)  # 沒文字內容
                    ])
                    additional_comments = limit_list([
                        c for c in exclude_list(filtered_reviews, settings['最大樣本'])
                        if c.find_elements(By.CLASS_NAME, HAS_COMMENT_CLASS)  # 有文字內容
                    ], settings['最大樣本'] - mix_comments_count)
                    total_samples = mixed_reviews + additional_comments
                    settings['統計物件'].total_browses = len(total_reviews)
                    settings['統計物件'].total_withcomments = current_total_target
                    settings['統計物件'].total_withoutcomments = len(total_withoutcomments)
                    settings['統計物件'].mixreviews_count = len(mixed_reviews)
                    settings['統計物件'].additionalcomments_count = len(additional_comments)
                case '評分最高':
                    total_samples = limit_list(total_highrating_withcomments, settings['最大樣本'])
                case '評分最低':
                    total_samples = limit_list(total_lowrating_withcomments, settings['最大樣本'])

            ### 紀錄統計資料 ###
            settings['統計物件'].total_samples = len(total_samples)

            if order_type == '最相關' and settings['統計物件'].total_samples < MINIMUM_SAMPLES:
                print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 評論樣本少於最低需求{MINIMUM_SAMPLES}個 - {title}\n', end='')
                store_item.change_crawler_state(database, '失敗', f'最相關評論樣本不足')
                settings['樣本型態'] = None
                continue

            ### 提取評論內容 ###
            sum_score = 0
            sum_responses = 0
            for index in range(len(total_samples)):
                print(f'\r正在提取所有{order_type}評論內容({index + 1}/{len(total_samples)})...', end='')
                data_review_id = total_samples[index].get_attribute('data-review-id')
                contents_element = total_samples[index].find_elements(By.CLASS_NAME, 'MyEned')
                score = int(total_samples[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
                sum_score += score if contents_element else 0
                sum_responses += 1 if total_samples[index].find_elements(By.CLASS_NAME, 'CDe7pd') else 0
                if data_review_id not in comments_dict:
                    # 按下「全文」以展開過長的評論內容
                    expand_comment = total_samples[index].find_elements(By.CLASS_NAME, 'w8nwRe')
                    time.sleep(0.1)
                    if expand_comment: expand_comment[0].send_keys(Keys.ENTER)
                    # 取得留言內容
                    contents = contents_element[0].find_element(By.CLASS_NAME, 'wiI7pd').text if contents_element else None
                    comment_time = total_samples[index].find_element(By.CLASS_NAME, 'rsqaWe').text
                    level = re.search(r'ba(?P<level>\d+)', total_samples[index].find_element(By.CLASS_NAME, 'NBa7we').get_attribute('src'))
                    # 取得使用者經驗
                    user_experiences_dict = {}
                    user_experiences = total_samples[index].find_elements(By.CLASS_NAME, 'RfDO5c')
                    for ue in user_experiences:
                        parent_element = driver.find_parent_element(ue, 2)
                        line = parent_element.find_elements(By.CLASS_NAME, 'RfDO5c')
                        if len(line) == 1:
                            span = line[0].find_element(By.TAG_NAME, 'span').text.split('：')
                            if span[0] in EXPERIENCE_TARGET and span[1]:
                                numbers = re.search(r'\d+', span[1])
                                if numbers: user_experiences_dict[span[0]] = int(numbers.group())
                        else:
                            dishes = []
                            experience = []
                            for review_tag in line:
                                experience.append(review_tag.find_element(By.TAG_NAME, 'span').text)
                            if experience[0] in RECOMMEND_DISHES:
                                if experience[1]: dishes = keyword_separator(experience[1])
                                if dishes:
                                    for dish in dishes:
                                        if not keyword_filter(dish): continue
                                        keywords_dict[dish] = (keywords_dict.get(dish)[0] + 1, 'recommend') if keywords_dict.get(dish) else (1, 'recommend')

                # 儲存評論物件
                if comments_dict.get(data_review_id) is None:
                    comments_dict[data_review_id] = {
                        'id': len(comments_dict) + 1,
                        'contents': contents,
                        'time': comment_time,
                        'rating': score,
                        'has_image': 1 if total_samples[index].find_elements(By.CLASS_NAME, 'KtCyie') else 0,
                        'food_rating': user_experiences_dict.get(EXPERIENCE_TARGET[0]) if user_experiences_dict else None,
                        'service_rating': user_experiences_dict.get(EXPERIENCE_TARGET[1]) if user_experiences_dict else None,
                        'atmosphere_rating': user_experiences_dict.get(EXPERIENCE_TARGET[2]) if user_experiences_dict else None,
                        'contributor_level': int(level.group('level')) + 2 if level else 0,
                        'sample_of_most_relevant': settings['類型陣列'][0],
                        'sample_of_highest_rating': settings['類型陣列'][1],
                        'sample_of_lowest_rating': settings['類型陣列'][2]
                    }
                else:
                    if settings['類型陣列'][0] == 1:
                        comments_dict[data_review_id]['sample_of_most_relevant'] = 1
                    elif settings['類型陣列'][1] == 1:
                        comments_dict[data_review_id]['sample_of_highest_rating'] = 1
                    elif settings['類型陣列'][2] == 1:
                        comments_dict[data_review_id]['sample_of_lowest_rating'] = 1

            settings['統計物件'].store_responses = sum_responses
            if settings['統計物件'].total_withcomments > 0: settings['統計物件'].real_rating = round(sum_score / settings['統計物件'].total_withcomments, 1)

    # 等待網址列顯示座標位置後取得座標位置
    print('\r正在取得地點座標...', end='')
    while True:
        if '@' in driver.current_url:
            coordinate = driver.current_url.split('@')[1].split(',')[0:2]
            location_item._longitude = coordinate[1]
            location_item._latitude = coordinate[0]
            break
        time.sleep(1)

    ### 儲存至資料庫 ###
    # 營業時間
    print('\r正在儲存營業時間結構...', end='')
    openhour_counter = 0
    for day_of_week, open_list in open_hours_dict.items():
        if open_list:
            for open_time in open_list:
                openhour_counter += 1
                openhours_item = OpenHour.OpenHour(
                    store_id=store_id,
                    sid=openhour_counter,
                    day_of_week=day_of_week,
                    open_time=open_time['open'],
                    close_time=open_time['close']
                ).insert(database)
        else:
            openhour_counter += 1
            openhours_item = OpenHour.OpenHour(
                store_id=store_id,
                sid=openhour_counter,
                day_of_week=day_of_week,
                open_time=None,
                close_time=None
            ).insert(database)
    # 服務
    for index, (properties, state) in enumerate(service_dict.items()):
        print(f'\r正在儲存服務項目結構({index+1}/{len(service_dict.items())})...', end='')
        service_item = Service.Service(
            store_id=store_id,
            sid=index+1,
            properties=properties,
            category=state[0],
            state=state[1]
        ).insert(database)
    # 關鍵字
    for index, (word, value) in enumerate(keywords_dict.items()):
        print(f'\r正在儲存關鍵字結構({index+1}/{len(keywords_dict.items())})...', end='')
        keyword_item = Keyword.Keyword(
            store_id=store_id,
            word=word,
            count=value[0],
            source=value[1],
            image_url=None,
            source_url=None
        )
        if AUTO_SEARCH_IMAGE and keyword_item.is_recommend():
            keyword_item.insert_after_search(driver, database, store_item.get_branch_title())
        else:
            keyword_item.insert_if_not_exists(database)
    # 評論
    not_only_samples = orders['評分最高']['樣本型態'] is not None
    for index, (data_id, value) in enumerate(comments_dict.items()):
        print(f'\r正在儲存評論結構({index+1}/{len(comments_dict.items())})...', end='')
        Comment.Comment(
            store_id=store_id,
            sid=value['id'],
            data_id=data_id,
            contents=value['contents'],
            time=value['time'],
            rating=value['rating'],
            has_image=value['has_image'],
            food_rating=value['food_rating'],
            service_rating=value['service_rating'],
            atmosphere_rating=value['atmosphere_rating'],
            contributor_level=value['contributor_level'],
            environment_state=None,
            price_state=None,
            product_state=None,
            service_state=None,
            sample_of_most_relevant=value['sample_of_most_relevant'] if not_only_samples else 1,
            sample_of_highest_rating=value['sample_of_highest_rating'] if not_only_samples else 1,
            sample_of_lowest_rating=value['sample_of_lowest_rating'] if not_only_samples else 1
        ).update_if_exists(database)
    # 評分
    print('\r正在儲存評分資料...', end='')
    rate_item.insert_if_not_exists(database)
    # 地點
    print('\r正在儲存地點資料...', end='')
    location_item.insert_if_not_exists(database)

    # 計算時間差
    TIME_DIFFERENCE = datetime.now() - CRAWLER_START_TIME
    MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

    ### 評估完成狀態 ###
    match orders['最相關']['樣本型態']:
        case 'all':
            print(f'\r【✅已完成】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {store_item.get_code(database)} | 總數:{rate_item.total_reviews}\n', end='')
            store_item.change_crawler_state(database, '成功', '取得完整資料')
        case 'sample':
            print(f'\r【📝已抽樣】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {store_item.get_code(database)} | ' +
                  f'相關:{rate_item.total_samples}/最高:{orders['評分最高']['統計物件'].total_samples}/最低:{orders['評分最低']['統計物件'].total_samples} | 總數:{rate_item.total_reviews})\n', end='')
            store_item.change_crawler_state(database, '抽樣', '取得樣本資料')
        case 'limit':
            print(f'\r【📜已上限】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {store_item.get_code(database)} | ' +
                  f'相關:{rate_item.total_samples}/最高:{orders['評分最高']['統計物件'].total_samples}/最低:{orders['評分最低']['統計物件'].total_samples} | ' +
                  f'過濾:{orders['最相關']['過濾評論']}/瀏覽:{rate_item.total_browses}/總數:{rate_item.total_reviews}\n', end='')
            store_item.change_crawler_state(database, '完成', '取得最大上限')
        case 'timeout':
            print(f'\r【⏱️已超時】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {store_item.get_code(database)} | ' +
                  f'相關:{rate_item.total_samples}/最高:{orders['評分最高']['統計物件'].total_samples}/最低:{orders['評分最低']['統計物件'].total_samples} | ' +
                  f'過濾:{orders['最相關']['過濾評論']}/瀏覽:{rate_item.total_browses}/總數:{rate_item.total_reviews}\n', end='')
            store_item.change_crawler_state(database, '超時', '超出爬蟲時間限制')

    driver.refresh()

print('\r已儲存所有搜尋結果的資料！', end='')
driver.exit()
