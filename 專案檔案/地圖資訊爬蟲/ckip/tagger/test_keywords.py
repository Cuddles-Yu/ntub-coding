# -*- coding: utf-8 -*-
from collections import defaultdict
from datetime import datetime
from 地圖資訊爬蟲.ckip.module.functions import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

database = SqlDatabase('mapdb', 'root', '11236018')

START_TIME = datetime.now()

keywords = load_json(r'D:\ntub\project\repository\ntub-coding\專案檔案\地圖資訊爬蟲\ckip\tagger\results\all_pos.json')
na = keywords.get('普通名詞(Na)', {})
nc = keywords.get('地方詞(Nc)', {})

keywords_list = defaultdict(int)

for keyword, count in na.items():
    keywords_list[keyword] += count
for keyword, count in nc.items():
    keywords_list[keyword] += count
keywords_list = dict(sorted(keywords_list.items(), key=lambda item: item[1], reverse=True))

write_json(keywords_list, 'results/all_keywords.json')
print(f'\r已將分析結果輸出至 json 文件中。')

# 計算時間差
TIME_DIFFERENCE = datetime.now() - START_TIME
MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60

print(f'\n【✅已完成】餐廳關鍵字提取 | 耗時:{MINUTES_ELAPSE:.2f}分鐘\n')

database.close()
