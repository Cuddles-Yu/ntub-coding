from 地圖資訊爬蟲.crawler.module.core_database import *

### 主程式 ###
connection = connect(use_database=False)
if connection is None: exit()

cursor = connection.cursor()
if exists(cursor, NAME):
    if __name__ == "__main__":
        print("是否要刪除並重新建立資料庫？[Y/N] ", end='')
        if input().lower() == 'y':
            drop_database(cursor, NAME)
            print("重新建立資料庫成功！")
        else:
            pass
        create_database(cursor, NAME)

    else:
        print("清除資料表中的所有資料？[YES] ", end='')
        if input() == 'YES':
            truncate_database(cursor, NAME)
            print("清空資料庫成功！")
else:
    create_database(cursor, NAME)
    print("建立資料庫成功！")

# 關閉資料庫連線階段
cursor.close()
connection.close()
