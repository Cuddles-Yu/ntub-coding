
### 檔案儲存設定 ###
URLS_FILE_NAME = 'urls.txt'

### 系統常數設定 ###
SEARCH_CITY = '台北市'
SEARCH_TYPE = '炒飯'
SEARCH_KEYWORD = SEARCH_CITY + ' ' + SEARCH_TYPE

REF_LOCATION = False
SHUFFLE_URLS = False

OPEN_DATA = None


CONTINUE_CRAWLER = False  # 重新執行爬蟲(修復不完整資料)
FORCE_CRAWLER = True
BRANCH_STORE_FIRST = False

CONTINUE_COUNT = 0
ENABLE_SCROLL_DOWN = False
HAVE_TO_GET_ALL_RESULTS = False

AUTO_SEARCH_IMAGE = False

STORES_URLS = [
]

PASS_TAGS = ['暫時關閉', '永久歇業']
REMOVE_TAGS = [
    '網絡行銷服務', '公司註冊處', '公司辦公室', '美容院', '禮品店', '時裝及配飾店', '指甲腳甲修飾專門店', '美容護膚診所', '紡織品批發商', '舞蹈學院', '攝影工作室',
    '音樂教練', '指甲腳甲修飾專門店', '網球教練', '髮廊', '寵物店', '課後補習班', '現場音樂表演場地','婚禮場地'
]
SWITCH_TABS = ['總覽', '評論', '簡介']
EXPERIENCE_TARGET = ['餐點', '服務', '氣氛']
RECOMMEND_DISHES = '建議的餐點'

HAS_COMMENT_CLASS = 'MyEned'

MAXIMUM_KEYWORD_LENGTH = 50

MAXIMUM_TIMEOUT = 15
MAXIMUM_WAITING = 3

HIGHRATING_SCORE = 4
LOWRATING_SCORE = 3

MAXIMUM_SAMPLES = 800
REFERENCE_SAMPLES = 100
MINIMUM_SAMPLES = 30

MAX_COMMENT_YEARS = 3

TARGET_CITIES = ['新北市', '台北市']
