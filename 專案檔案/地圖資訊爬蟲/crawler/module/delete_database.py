import 地圖資訊爬蟲.crawler.module.core_database as db
from 地圖資訊爬蟲.crawler.tables import Store

def delete_all_records(connect, store_name: str):
    c = connect.cursor()
    store_id = Store.Reference(name=store_name).get_id(connect)
    c.execute(f'''
        DELETE FROM `comments` WHERE `store_id` = '{store_id}';
    ''')
    c.execute(f'''
        DELETE FROM `services` WHERE `store_id` = '{store_id}';
    ''')
    c.execute(f'''
        DELETE FROM `keywords` WHERE `store_id` = '{store_id}';
    ''')
    c.execute(f'''
        DELETE FROM `locations` WHERE `store_id` = '{store_id}';
    ''')
    c.execute(f'''
        DELETE FROM `rates` WHERE `store_id` = '{store_id}';
    ''')
    connect.commit()  # 提交修改
    c.close()

if __name__ == "__main__":
    TARGET_STORE = input('刪除哪個商家的所有資料？')
    # 連線資料庫
    connection = db.connect(use_database=True)
    # 執行操作
    delete_all_records(connection, TARGET_STORE)
    print(f"已成功移除商家名稱為 '{TARGET_STORE}' 的所有資料。")
    # 關閉資料庫
    connection.close()
