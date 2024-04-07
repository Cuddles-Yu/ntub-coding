import 地圖結果資料庫.python.module.core_database as db

def delete_all_records(connect, store_name: str):
    c = connect.cursor()
    c.execute(f'''
        DELETE FROM `comments` WHERE `store_name` = '{store_name}';
    ''')
    c.execute(f'''
        DELETE FROM `rates` WHERE `store_name` = '{store_name}';
    ''')
    c.execute(f'''
        DELETE FROM `keywords` WHERE `store_name` = '{store_name}';
    ''')
    c.execute(f'''
        DELETE FROM `locations` WHERE `store_name` = '{store_name}';
    ''')
    c.execute(f'''
        DELETE FROM `stores` WHERE `name` = '{store_name}';
    ''')
    connect.commit()  # 提交修改
    c.close()

if __name__ == "__main__":
    TARGET_STORE = 'Grandpa老爺爺葡式蛋塔'
    # 連線資料庫
    connection = db.connect(use_database=True)
    # 執行操作
    delete_all_records(connection, TARGET_STORE)
    print(f"已成功移除商家名稱為 '{TARGET_STORE}' 的所有資料。")
    # 關閉資料庫
    connection.close()
