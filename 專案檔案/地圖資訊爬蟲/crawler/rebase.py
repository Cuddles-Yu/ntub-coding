from 地圖資訊爬蟲.crawler.module.core_database import *
from 地圖資訊爬蟲.crawler.tables._base import escape_quotes

### 主程式 ###
connection = connect(use_database=True)
if connection is None: exit()

cursor = connection.cursor()
cursor.execute('''
    SELECT DISTINCT id, name FROM stores AS s
    INNER JOIN services AS c ON s.name = c.store_name
    WHERE c.store_id IS NULL
''')
tags = cursor.fetchall()
cursor.close()
for index, tag in tags:
    print(f'{index}/{len(tags)} {tag}')
    cursor = connection.cursor()
    cursor.execute(f'''
        UPDATE services
        SET store_id = {index}
        WHERE store_name = '{escape_quotes(tag)}' and store_id IS NULL;
    ''')
    connection.commit()
    cursor.close()

# 關閉資料庫連線階段
connection.close()
