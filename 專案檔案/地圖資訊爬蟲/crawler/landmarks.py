### 匯入模組 ###
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from 地圖資訊爬蟲.crawler.tables import Landmark

### 初始化 ###
database = SqlDatabase('mapdb', 'root', '11236018')

API_PATH = 'https://data.taipei/api/v1/dataset/307a7f61-e302-4108-a817-877ccbfca7c1?scope=resourceAquire&limit=1000'
api_data = get_json_from_api(API_PATH)
database.truncate_table('landmarks')
if api_data:
    NAME_KEY, ENTRANCE_KEY, LNG_KEY, LAT_KEY = '出入口名稱', '出入口編號', '經度', '緯度'
    results = api_data.get('result', {}).get('results', [])
    names = [item.get(NAME_KEY).split('站')[0]+'站' for item in results]
    entrances = [item.get(ENTRANCE_KEY) for item in results]
    lngs = [item.get(LNG_KEY) for item in results]
    lats = [item.get(LAT_KEY) for item in results]
    in_landmarks = []
    landmarks = []
    for i, (name, entrance, lng, lat) in enumerate(zip(names, entrances, lngs, lats), start=1):
        if name in in_landmarks: continue
        landmarks.append(Landmark.newObject(
            name=name,
            lng=lng,
            lat=lat
        ))
        in_landmarks.append(name)
    for landmark in landmarks:
        landmark.insert_if_not_exists(database)
