import sys
from typing import Optional

from selenium import webdriver
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.support.ui import WebDriverWait

from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.const import *
from 地圖資訊爬蟲.crawler.module.return_code import *
from 地圖資訊爬蟲.crawler.tables import Store
from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

### 結構 ###
HAS_COMMENT_CLASS = 'MyEned'
ORDER_TYPE = {
    '最相關': 0,
    '最新': 1,
    '評分最高': 2,
    '評分最低': 3
}

class EdgeDriver:

    ### 變數 ###
    driver: Optional[webdriver.Edge] = None
    database: Optional[SqlDatabase] = None

    ### 基礎 ###
    def __init__(self, database, url: Optional[str] = None):
        options = webdriver.EdgeOptions()
        options.add_argument('--disable-blink-features=AutomationControlled')
        options.add_experimental_option("detach", True)
        options.add_argument('--window-size=950,1020')
        # options.add_argument("--headless")  # 不顯示視窗
        self.driver = webdriver.Edge(options=options)
        self.driver.execute_script("Object.defineProperty(navigator, 'webdriver', {get: () => undefined})")
        if url: self.get(url)
        self.driver.set_window_position(x=970, y=10)
        self.database = database

    @property
    def current_url(self):
        return self.driver.current_url

    def exit(self):
        if self.driver: self.driver.close()
        if self.database: self.database.close()
        sys.exit(ReturnCode.Success)

    def get(self, url: str):
        return self.driver.get(url)

    def find_element(self, by, value):
        return self.driver.find_element(by, value)

    def find_elements(self, by, value):
        return self.driver.find_elements(by, value)

    def refresh(self):
        self.driver.refresh()

    @staticmethod
    def find_parent_element(element, levels):
        xpath = '../' * levels
        return element.find_element(By.XPATH, xpath.rstrip('/'))

    def move_to_element(self, element):
        if element is None: return
        ActionChains(self.driver).move_to_element(element).perform()

    def click_element(self, element):
        if element is None: return
        self.move_to_element(element)
        element.click()

    def wait_for_text(self, by, value):
        try:
            element = self.wait_for_element(by, value)
            return element.text
        except Exception:
            return ''

    def wait_for_click(self, by, value):
        try:
            element = self.wait_for_element(by, value)
            element.click()
            return element
        except Exception:
            return None

    def wait_for_click_index(self, by, value, index):
        try:
            element = self.wait_for_elements(by, value)[index]
            element.click()
            return element
        except Exception:
            return None

    def find_element_list(self, by, values):
        for value in values:
            elements = self.find_elements(by, value)
            if elements: return elements[0]
        return None

    def wait_for_element_list(self, by, values):
        for value in values:
            element = self.wait_for_element(by, value)
            if element: return element
        return None

    def wait_for_element(self, by, value):
        try:
            WebDriverWait(self.driver, MAXIMUM_WAITING).until(
                ec.presence_of_element_located((by, value))
            )
            return self.find_element(by, value)
        except TimeoutException:
            return None

    def wait_for_elements(self, by, value):
        try:
            WebDriverWait(self.driver, MAXIMUM_WAITING).until(
                ec.presence_of_element_located((by, value))
            )
            return self.find_elements(by, value)
        except TimeoutException:
            return None

    ### 涵式 ###
    def get_tabs(self):
        # 讀取標籤按鈕
        tabs = self.wait_for_element(By.CLASS_NAME, 'RWPxGd')
        if not tabs: return None, None
        tabs_elem = tabs.find_elements(By.CLASS_NAME, 'hh2c6')
        tabs_name = [tab.find_element(By.CLASS_NAME, 'Gpq6kf').text for tab in tabs_elem]
        return tabs_elem, tabs_name

    def get_keywords_dict(self) -> dict:
        keywords_dict = {}
        comment_container = self.wait_for_element(By.CLASS_NAME, 'dS8AEf')  # 評論面板
        # 取得留言關鍵字
        keywords_elements = self.wait_for_element(By.CLASS_NAME, 'e2moi')
        if keywords_elements:
            for keyword in comment_container.find_elements(By.CLASS_NAME, 'e2moi'):
                count = keyword.find_elements(By.CLASS_NAME, 'bC3Nkc')
                if len(count) == 0: continue
                kws = keyword_separator(keyword.find_element(By.CLASS_NAME, 'uEubGf').text)
                kw = ''.join(kws)
                if not kws or not keyword_filter(kw): continue
                keywords_dict[kw] = (int(count[0].text), 'DEFAULT')
        return keywords_dict

    def switch_to_order(self, order_type: str):
        print(f'\r正在切換至{order_type}評論...', end='')
        # 功能按鈕 - 撰寫/查詢/[排序評論]
        function_buttons = self.wait_for_elements(By.CLASS_NAME, 'S9kvJb')
        function_names = [button.get_attribute('data-value') for button in self.driver.find_elements(By.CLASS_NAME, 'S9kvJb')]
        if '排序' not in function_names: return
        function_buttons[function_names.index('排序')].click()
        time.sleep(0.5)
        # 排序選單 - 最相關/最新/評分最高/評分最低
        self.wait_for_click_index(By.CLASS_NAME, 'fxNQSd', index=ORDER_TYPE[order_type])
        time.sleep(1)
        # 等待載入新留言
        self.wait_for_element(By.CLASS_NAME, 'jftiEf')

    def search(self, keyword) -> bool:
        # 等待 Driver 瀏覽到指定頁面後，對搜尋框輸入關鍵字搜尋
        search_box = self.wait_for_element(By.CLASS_NAME, 'searchboxinput')
        search_box.clear()
        search_box.send_keys(keyword)
        search_box.send_keys(Keys.ENTER)
        if OPEN_DATA: time.sleep(1)
        has_result = self.wait_for_element(By.CLASS_NAME, 'THOPZb')
        get_all_results = self.wait_for_element(By.CLASS_NAME, 'eKbjU')
        return has_result or get_all_results

    def search_and_save_results(self, keyword) -> bool:
        urls, store_names = self.search_and_scroll(keyword)
        if not urls or not store_names: return [], []
        print(f'\r正在建立搜尋結果至資料庫...')
        for link, store_name in zip(urls, store_names):
            store = Store.newObject(store_name, link)
            if store.exists(self.database):
                print(f'✴️已存在搜尋結果【{store_name}】')
            else:
                # store.insert_if_not_exists(self.database)
                print(f'✳️已建立搜尋結果【{store_name}】')
        print()
        return urls, store_names

    def search_and_scroll(self, keyword, return_one: Optional[bool] = False, show_hint: Optional[bool] = True):
        if not self.search(keyword):
            if not return_one:
                if show_hint: print(f'\r⚠️該關鍵字沒有包含搜尋結果')
                return None, None
            else:
                if show_hint: print(f'\r👀該關鍵字僅包含一個結果')
                return [self.current_url], [keyword]
        else:
            if return_one:
                if show_hint: print(f'\r👁️該關鍵字包含多個結果')
                return None, None
            while True:
                # 取得所有搜尋結果所在的'容器'物件
                if show_hint: print('\r正在取得搜尋結果...(可能會花費較多時間)', end='')
                self.wait_for_element(By.CLASS_NAME, 'Nv2PK')
                container_search_result = self.find_element_list(By.XPATH, [
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
                        if self.find_elements(By.CLASS_NAME, 'HlvSq'): break
                        self.move_to_element(container_search_result.find_elements(By.CLASS_NAME, 'Nv2PK')[-1])
                        container_search_result.send_keys(Keys.PAGE_DOWN)
                        time.sleep(0.1)
                        # 檢查是否持續一段時間皆未出現新的結果(卡住)
                        results = self.find_elements(By.CLASS_NAME, 'hfpxzc')
                        if current_results != len(results): start_time = time.time()
                        current_results = len(results)
                        if time.time() - start_time > MAXIMUM_TIMEOUT:
                            finish_scroll = not HAVE_TO_GET_ALL_RESULTS
                            break
                if finish_scroll: break

            element_search_title = self.find_elements(By.CLASS_NAME, 'Nv2PK')
            # 商家連結與名稱
            urls = to_map_url([title.find_element(By.CLASS_NAME, 'hfpxzc').get_attribute('href') for title in element_search_title])
            store_names = [title.find_element(By.CLASS_NAME, 'qBF1Pd').text for title in element_search_title]

        return urls, store_names
