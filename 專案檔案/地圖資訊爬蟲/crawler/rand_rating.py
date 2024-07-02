import random

from 地圖資訊爬蟲.crawler.module.core_database import *
from 地圖資訊爬蟲.crawler.tables._base import escape_quotes

### 主程式 ###
connection = connect(use_database=True)
if connection is None: exit()

cursor = connection.cursor()
cursor.execute('''
    SELECT DISTINCT id FROM stores
''')
tags = cursor.fetchall()
cursor.close()
for index in tags:
    i = index[0]
    print(f'{i}/{len(tags)} {i}')
    cursor = connection.cursor()
    cursor.execute(f'''
        UPDATE rates
        SET 
            environment_rating = {random.randint(0, 100)}, 
            price_rating = {random.randint(0, 100)}, 
            product_rating = {random.randint(0, 100)}, 
            service_rating = {random.randint(0, 100)}
        WHERE store_id = {i};
    ''')
    connection.commit()
    cursor.close()

# 關閉資料庫連線階段
connection.close()
