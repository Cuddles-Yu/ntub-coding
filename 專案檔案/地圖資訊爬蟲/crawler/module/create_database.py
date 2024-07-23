from 地圖資訊爬蟲.crawler.module.core_database import *

def ask_to_reset_database(_connection):
    _cursor = _connection.cursor()
    print(f"清除'{NAME}'資料庫中的所有資料？[YES] ", end='')
    if input() == 'YES':
        truncate_database(_cursor, NAME)
        print("清空資料庫成功！")
    _cursor.close()


if __name__ == "__main__":
    ### 主程式 ###
    connection = connect(use_database=False)
    if connection is None: exit()
    cursor = connection.cursor()
    if exists(cursor, NAME):
        print(f"刪除並重新建立'{NAME}'資料庫？[YES] ", end='')
        if input() == 'YES':
            drop_database(cursor, NAME)
            print("重新建立資料庫成功！")
        else:
            pass
        create_database(cursor, NAME)
    else:
        create_database(cursor, NAME)
        print("建立資料庫成功！")
    # 關閉資料庫連線階段
    cursor.close()
    connection.close()
