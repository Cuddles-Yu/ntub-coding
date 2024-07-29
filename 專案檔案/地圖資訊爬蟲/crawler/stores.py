### 匯入模組 ###
from datetime import datetime

from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions.EdgeDriver import EdgeDriver
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.tables import Store, Comment, Keyword, Location, Rate, Service, Tag, OpenHours

from bs4 import BeautifulSoup

from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By


### 初始化 ###
database = SqlDatabase('mapdb', 'root', '11236018')
driver = EdgeDriver(database, url='https://www.google.com.tw/maps/preview')

### 主程式 ###
if CONTINUE_CRAWLER:
    urls = database.get_urls_from_incomplete_store()
    if not urls:
        print(f'查無需資料修復之商家，程式將自動結束...')
        driver.exit()
    print(f'資料完整性修復模式 -> 已開啟')
else:
    urls = STORES_URLS if STORES_URLS else []
if urls: urls = to_map_url(urls)

print(f'資料將儲存至資料庫 -> {database.name}')

### 查詢關鍵字後儲存查詢結果 ###
if not urls:
    print(f'正在搜尋關鍵字 -> {SEARCH_KEYWORD}\n')
    driver.search_and_scroll(SEARCH_KEYWORD)

### 主爬蟲 ###
url_count = len(urls)
if SHUFFLE_URLS: shuffle(urls)

print(f'\r正在準備爬取所有商家連結資料(共{url_count}個)...\n')
for i in range(url_count):
    START_TIME = datetime.now()
    # 瀏覽器載入指定的商家地圖連結
    driver.get(urls[i])
    # 直到商家名稱顯示(無最大等候時間)
    while True:
        # 地點名稱
        title = driver.wait_for_text(By.CLASS_NAME, 'DUwDvf')
        if title.strip() != '': break
        time.sleep(0.1)

    store_item = Store.newObject(title, urls[i])
    store_item._crawler_state = '基本'

    # 讀取標籤按鈕
    tabs_elem, tabs_name = driver.get_tabs()
    if tabs_elem is None:
        print(f'\r【🆖無標籤】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        continue

    # 確認是否為特殊商家
    if '價格' in tabs_name:
        print(f'\r【💎特殊性】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        store_item._crawler_state = '特殊'
        store_item._crawler_description = '結合飯店住宿服務'
        store_item.update_if_exists(database)
        continue
    else:
        store_item._crawler_state = '基本'

    ### 檢查資料庫中是否已經存在指定的商家 ###
    is_repairing = False
    if store_item.exists(database):
        crawler_state, crawler_description = store_item.get_state(database)
        if crawler_description is None:
            # '建立' | '基本'
            print(f'\r正在準備重新爬取資料...', end='')
            store_item.reset(database)
        else:
            # '成功' | '抽樣' | '超時' | '特殊'
            print(f'\r【⭐已存在】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title} ({crawler_state})\n', end='')
            continue
        # print(f'\r【🌐參照點】{str(i + 1).zfill(len(str(max_count)))}/{max_count} | {title}\n', end='')

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
        # 更新時間
        if to_bool(open_hours_tag.find_element(By.CLASS_NAME, 'OMl5r').get_attribute('aria-expanded')): open_hours_tag.click()  # (確保標籤關閉以取得更新時間)
        update_info = driver.find_elements(By.CLASS_NAME, 'zaf2le')
        if update_info:
            last_update_info = re.search(r'(?P<time>\d+\s*\D+前)', update_info[0].text.strip())
            store_item._last_update = last_update_info.group() if last_update_info else None
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

    ### 商家欄位資料 ###
    store_state = driver.find_elements(By.CLASS_NAME, 'fCEvvc')
    if len(store_state) > 0:
        store_item._tag = store_state[0].text
    else:
        store_tag = driver.wait_for_element_list(By.CLASS_NAME, ['DkEaL', 'mgr77e'])
        store_item._tag = store_tag.text if store_tag else None
    # 可能為永久歇業/暫時關閉
    if any(pass_tag in store_item.get_tag() for pass_tag in PASS_TAGS):
        print(f'\r【⛔休業中】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        store_item.change_state(database, '休業', '')
        continue

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
    if labels['地址']:
        postal, city, district, detail = get_split_from_address(labels['地址'])
        location_item._postal_code = postal
        location_item._city = city
        location_item._dist = district
        location_item._details = detail
    else:
        print(f'\r【🗺️無地址】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        continue

    if labels['Plus Code']:
        village = re.search(r'(?P<vil>\S+里)', labels['Plus Code'])
        location_item._vil = village.group() if village else None

    if TARGET_CITY != '' and location_item.get_city() == TARGET_CITY:
        print(f'\r【🌍範圍外】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        continue

    ### 商家相片 ###
    print('\r正在取得商家相片...', end='')
    store_img = driver.wait_for_element(By.CLASS_NAME, 'aoRNLd')
    if store_img: store_item._preview_image = store_img.find_element(By.TAG_NAME, 'img').get_attribute('src')
    if not store_img:
        print('沒有商家相片(不可能)')
        exit()

    ### 服務項目 ###
    print('\r正在取得服務項目...', end='')
    service_dict = {}
    if '簡介' in tabs_name:
        # 標籤按鈕 - 總覽/評論/[簡介]
        tabs_elem[tabs_name.index('簡介')].click()
        # 商家簡介 (選擇性)
        description = driver.wait_for_element(By.CLASS_NAME, 'PbZDve')
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
        ).insert_if_not_exists(database)

    ### 儲存商家資料，並取得其 store_id ###
    try:
        store_item.update_if_exists(database)
    except Exception as e:
        print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {e} - {title}\n', end='')
        continue
    store_id = store_item.get_id(database)
    rate_item._store_id = store_id
    location_item._store_id = store_id

    # 標籤按鈕 - 總覽/[評論]/簡介
    print('\r正在取得商家評論...', end='')
    if '評論' in tabs_name:
        tabs_elem[tabs_name.index('評論')].click()
        # 取得評論星級
        rating = driver.wait_for_element(By.CLASS_NAME, 'jANrlb')
        if rating:
            rate_item._avg_rating = float(rating.find_element(By.CLASS_NAME, 'fontDisplayLarge').text)
            rate_item._total_reviews = int(''.join(re.findall(r'\d+', rating.find_element(By.CLASS_NAME, 'fontBodySmall').text)))

    if rate_item.total_reviews == 0:
        print(f'\r【📝無評論】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | {title}\n', end='')
        store_item.change_state(database, '失敗', '評論總數為零')
    elif rate_item.total_reviews < MINIMUM_SAMPLES:
        print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 評論總數低於最低樣本數 - {title}\n', end='')
        store_item.change_state(database, '失敗', '評論總數不足')
        continue

    comment_items = []
    keywords_dict = {}
    get_comments_type = ''
    if rate_item.total_reviews > 0:
        # 評論面板
        commentContainer = driver.wait_for_element(By.CLASS_NAME, 'dS8AEf')
        # 取得關鍵字
        keywords_elements = driver.wait_for_element(By.CLASS_NAME, 'e2moi')
        if keywords_elements:
            for keyword in commentContainer.find_elements(By.CLASS_NAME, 'e2moi'):
                count = keyword.find_elements(By.CLASS_NAME, 'bC3Nkc')
                if len(count) == 0: continue
                kws = keyword_separator(keyword.find_element(By.CLASS_NAME, 'uEubGf').text)
                kw = ''.join(kws)
                if not kws or not keyword_filter(kw): continue
                keywords_dict[kw] = (int(count[0].text), 'DEFAULT')

        if not driver.switch_to_order(order_type='最相關'):
            print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 最相關留言切換失敗 - {title}\n', end='')
            store_item.change_state(database, '失敗', '最相關留言切換失敗')
            continue

        # 紀錄爬取評論的等待時間
        start_time = time.time()
        # 滾動評論面板取得所有評論
        current_total_reviews_count = 0
        current_filtered_reviews_count = 0
        while True:
            driver.move_to_element(commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')[-1])
            commentContainer.send_keys(Keys.PAGE_DOWN)
            total_reviews = commentContainer.find_elements(By.CLASS_NAME, 'jftiEf')
            filtered_reviews = [
                c for c in total_reviews
                if not ('年' in c.find_element(By.CLASS_NAME, 'rsqaWe').text and int(re.search(r'\d+', c.find_element(By.CLASS_NAME, 'rsqaWe').text).group()) > MAX_COMMENT_YEARS)
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
            if time.time() - start_time > (MAXIMUM_TIMEOUT + (current_total_reviews_count ** 0.5) * 0.8):
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
            print(f'\r【❌已失敗】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 評論樣本少於最低需求{MINIMUM_SAMPLES}個 - {title}\n', end='')
            store_item.change_state(database, '失敗', '評論樣本不足')
            continue

        # 提取評論內容
        sum_score = 0
        sum_responses = 0
        for index in range(len(total_samples)):
            try:
                print(f'\r正在提取所有評論內容({index+1}/{len(total_samples)})...\n', end='')
                score = 0
                comment_time = ''
                score = int(total_samples[index].find_element(By.CLASS_NAME, 'kvMYJc').get_attribute('aria-label').split(' ')[0])
                comment_time = total_samples[index].find_element(By.CLASS_NAME, 'rsqaWe').text
                level = re.search(r'ba(?P<level>\d+)', total_samples[index].find_element(By.CLASS_NAME, 'NBa7we').get_attribute('src'))
                # 取得留言結構
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
                                    keywords_dict[dish] = (keywords_dict.get(dish)[0]+1, 'recommend') if keywords_dict.get(dish) else (1, 'recommend')

                contents_element = total_samples[index].find_elements(By.CLASS_NAME, 'MyEned')
                contents = contents_element[0].find_element(By.CLASS_NAME, 'wiI7pd').text if contents_element else None
                sum_score += score if contents_element else 0
                sum_responses += 1 if total_samples[index].find_elements(By.CLASS_NAME, 'CDe7pd') else 0
                # 儲存評論物件
                comment_items.append(Comment.Comment(
                    store_id=store_id,
                    index=index+1,
                    contents=contents,
                    has_image=1 if total_samples[index].find_elements(By.CLASS_NAME, 'KtCyie') else 0,
                    time=comment_time,
                    rating=score,
                    food_rating=user_experiences_dict.get(EXPERIENCE_TARGET[0]) if user_experiences_dict else None,
                    service_rating=user_experiences_dict.get(EXPERIENCE_TARGET[1]) if user_experiences_dict else None,
                    atmosphere_rating=user_experiences_dict.get(EXPERIENCE_TARGET[2]) if user_experiences_dict else None,
                    contributor_level=int(level.group('level'))+2 if level else 0,
                    environment_state=None,
                    price_state=None,
                    product_state=None,
                    service_state=None
                ))
            finally:
                pass

        rate_item._store_responses = sum_responses
        if len(total_withcomments): rate_item._real_rating = round(sum_score / len(total_withcomments), 1)

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
                ).insert(database)
        else:
            openhours_item = OpenHours.OpenHours(
                store_id=store_id,
                day_of_week=day_of_week,
                open_time=None,
                close_time=None
            ).insert(database)
    # 服務
    print('\r正在儲存服務項目結構...', end='')
    for properties, state in service_dict.items():
        service_item = Service.Service(
            store_id=store_id,
            properties=properties,
            category=state[0],
            state=state[1]
        ).insert(database)
    # 關鍵字
    keyword_counter = 0
    for word, value in keywords_dict.items():
        keyword_counter += 1
        print(f'\r正在儲存關鍵字結構({keyword_counter}/{len(keywords_dict.items())})...', end='')
        keyword_item = Keyword.Keyword(
            store_id=store_id,
            word=word,
            count=value[0],
            source=value[1],
            image_url=None,
            source_url=None
        )
        if AUTO_SEARCH_IMAGE and keyword_item.is_recommend():
            keyword_item.insert_after_search(driver, database, store_item.get_name())
        else:
            keyword_item.insert_if_not_exists(database)
    # 評論
    for index in range(len(comment_items)):
        print(f'\r正在儲存評論結構(%d/%d)...' % (index + 1, len(comment_items)), end='')
        comment_items[index].insert(database)
    # 評分
    print('\r正在儲存評分資料...', end='')
    rate_item.insert_if_not_exists(database)
    # 地點
    print('\r正在儲存地點資料...', end='')
    location_item.insert_if_not_exists(database)

    # 計算時間差
    TIME_DIFFERENCE = datetime.now() - START_TIME
    MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

    ### 評估完成狀態 ###
    match get_comments_type:
        case 'all':
            if is_repairing:
                print(f'\r【🛠️已修復】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {title} ({rate_item.total_reviews})\n', end='')
                store_item.change_state(database, '成功', '取得完整資料(修復)')
            else:
                print(f'\r【✅已完成】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {title} ({rate_item.total_reviews})\n', end='')
                store_item.change_state(database, '成功', '取得完整資料')
        case 'sample':
            if is_repairing:
                print(f'\r【🛠️已修復】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {title} ({rate_item.total_reviews})\n', end='')
                store_item.change_state(database, '抽樣', '取得樣本資料(修復)')
            else:
                print(f'\r【📝已抽樣】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {title} ({rate_item.total_samples}/{rate_item.total_reviews})\n', end='')
                store_item.change_state(database, '抽樣', '取得樣本資料')
        case 'timeout':
            print(f'\r【⏱️已超時】{str(i + 1).zfill(len(str(url_count)))}/{url_count} | 耗時:{MINUTES_ELAPSE:.2f}分鐘 | {title} (留言:{current_total_withcomments}/過濾:{current_filtered_reviews_count}/瀏覽:{current_total_reviews_count}/總共:{rate_item.total_reviews})\n', end='')
            store_item.change_state(database, '超時', '超出爬蟲時間限制')

    # driver.close()
    # driver.switch_to.window(driver.window_handles[0])
    driver.refresh()

print('\r已儲存所有搜尋結果的資料！', end='')
driver.exit()
