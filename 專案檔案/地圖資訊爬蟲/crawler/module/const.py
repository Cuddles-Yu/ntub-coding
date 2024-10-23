
### 檔案儲存設定 ###
URLS_FILE_NAME = 'urls.txt'

### 系統常數設定 ###
SEARCH_CITY = '台北市'
SEARCH_TYPE = '拉麵'
SEARCH_KEYWORD = SEARCH_CITY + ' ' + SEARCH_TYPE

REF_LOCATION = False

SHUFFLE_URLS = False

OPEN_DATA = None
API_PATH = 'https://data.taipei/api/v1/dataset/d706f428-b2c7-4591-9ebf-9f5cd7408f47?scope=resourceAquire&limit=1000'  # 環保餐廳
API_KEY = 'https://data.taipei/api/v1/dataset/2f2b039d-6bff-4663-a4e5-3bd6cc98ee48?scope=resourceAquire&limit=1000'  # 客家餐廳

CONTINUE_CRAWLER = False  # 重新執行爬蟲(修復不完整資料)
FORCE_CRAWLER = True
BRANCH_STORE_FIRST = False

CONTINUE_COUNT = 0
ENABLE_SCROLL_DOWN = True
HAVE_TO_GET_ALL_RESULTS = False

AUTO_SEARCH_IMAGE = False

STORES_URLS = [
    'https://www.google.com.tw/maps/place/+/data=!4m7!3m6!1s0x3442a90ca507e3bb:0x803939ffa227dbed!8m2!3d25.0442063!4d121.5133685!16s%2Fg%2F12m9dpz5x!19sChIJu-MHpQypQjQR7dsnov85OYA'
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

MAXIMUM_SAMPLES = 200
REFERENCE_SAMPLES = 100
MINIMUM_SAMPLES = 30

MAX_COMMENT_YEARS = 3

TARGET_CITIES = ['新北市', '台北市']
