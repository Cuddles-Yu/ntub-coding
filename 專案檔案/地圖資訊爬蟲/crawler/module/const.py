
### 檔案儲存設定 ###
URLS_FILE_NAME = 'urls.txt'

### 系統常數設定 ###
SEARCH_CITY = '台北市'
SEARCH_TYPE = '拉麵'
SEARCH_KEYWORD = SEARCH_CITY + ' ' + SEARCH_TYPE

REF_LOCATION = False

SHUFFLE_URLS = False

CONTINUE_CRAWLER = True  # 重新執行爬蟲(修復不完整資料)
FORCE_CRAWLER = True
BRANCH_STORE_FIRST = False

CONTINUE_COUNT = 0
ENABLE_SCROLL_DOWN = True
HAVE_TO_GET_ALL_RESULTS = False

AUTO_SEARCH_IMAGE = False

STORES_URLS = [
    # 'https://www.google.com.tw/maps/place/+/data=!3m2!4b1!5s0x3442a9660fccec7d:0x8d84c86f1a82e687!4m6!3m5!1s0x3442a966031f5799:0x35822d693796d1a1!8m2!3d25.0519063!4d121.5260228!16s%2Fg%2F1tc_nq74'
    # 'https://www.google.com.tw/maps/place/+/data=!4m7!3m6!1s0x3442a968f6a334b7:0x183b14ba067d9c8b!8m2!3d25.0149261!4d121.5327481!16s%2Fg%2F11s93qc_ll!19sChIJtzSj9mipQjQRi5x9BroUOxg'
    # 'https://www.google.com.tw/maps/place/+/data=!4m7!3m6!1s0x3442abc92e8b4a7d:0x9794f69368c4033e!8m2!3d25.0361074!4d121.5546074!16s%2Fg%2F1tfkt06b!19sChIJfUqLLsmrQjQRPgPEaJP2lJc'
    # 'https://www.google.com.tw/maps/place/+/data=!4m7!3m6!1s0x3442abd4d0f5fe57:0xfbe900217bf3a103!8m2!3d25.0424021!4d121.5637437!16s%2Fg%2F11qb493ljf!19sChIJV_710NSrQjQRA6HzeyEA6fs'
    # 'https://www.google.com.tw/maps/place/+/data=!4m7!3m6!1s0x3442abc69d3f2ecb:0x6e9156b622a855e!8m2!3d25.0427409!4d121.5542369!16s%2Fg%2F11g88js2y1!19sChIJyy4_ncarQjQRXoUqYmsV6QY'
]

PASS_TAGS = ['暫時關閉', '永久歇業']
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
