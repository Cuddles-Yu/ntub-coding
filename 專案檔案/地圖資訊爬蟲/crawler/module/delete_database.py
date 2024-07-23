import 地圖資訊爬蟲.crawler.module.core_database as db
from 地圖資訊爬蟲.crawler.tables import Store

def reset_store(connect, store_name: str) -> str:
    c = connect.cursor()
    store_item = Store.Reference(name=store_name)
    sid = store_item.get_id(connect)
    if sid is not None:
        c.execute(f'''
            DELETE FROM `comments` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `services` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `keywords` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `locations` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `rates` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `openhours` WHERE `store_id` = '{sid}';
        ''')
        connect.commit()  # 提交修改
        store_item.change_state(connect, '建立', None)
    c.close()
    return sid

def delete_store(connect, store_name: str) -> str:
    c = connect.cursor()
    sid = Store.Reference(name=store_name).get_id(connect)
    if sid is not None:
        c.execute(f'''
            DELETE FROM `comments` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `services` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `keywords` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `locations` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `rates` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `openhours` WHERE `store_id` = '{sid}';
        ''')
        c.execute(f'''
            DELETE FROM `stores` WHERE `id` = '{sid}';
        ''')
        connect.commit()  # 提交修改
    c.close()
    return sid

if __name__ == "__main__":
    ### 刪除商家 ###
    # TARGET_STORE = input('刪除哪個商家的所有資料？')
    # # 連線資料庫
    # connection = db.connect(use_database=True)
    # # 執行操作
    # store_id = delete_store(connection, TARGET_STORE)
    # if store_id is not None:
    #     print(f"已成功移除商家id為 '{store_id}' 的所有資料。")
    # else:
    #     print(f"查無名稱為 '{TARGET_STORE}' 的商家。")

    ### 重設商家 ###
    TARGET_STORE = input('重設哪個商家的資料？')
    # 連線資料庫
    connection = db.connect(use_database=True)
    # 執行操作
    store_id = reset_store(connection, TARGET_STORE)
    if store_id is not None:
        print(f"已成功重設商家id為 '{store_id}' 的資料。")
    else:
        print(f"查無名稱為 '{TARGET_STORE}' 的商家。")

    ### 關閉資料庫 ##
    connection.close()
