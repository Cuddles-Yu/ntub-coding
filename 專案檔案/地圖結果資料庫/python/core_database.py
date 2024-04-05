############################################## 資料庫核心 ##############################################
# (pip install mysql-connector-python) 安裝第三方模組來連結
import mysql.connector

# 系統變數
NAME = 'mapdb'
USERNAME = 'root'
PASSWORD = '11236018'

def connect(use_database):  # 連接資料庫
    if use_database:
        return mysql.connector.connect(
            user=USERNAME,
            password=PASSWORD,
            host='localhost',
            database=NAME,
            auth_plugin='mysql_native_password'
        )
    else:
        return mysql.connector.connect(
            user=USERNAME,
            password=PASSWORD,
            host='localhost',
            auth_plugin='mysql_native_password'
        )

# 檢查資料庫是否存在
def exists(c, database_name: str) -> bool:
    c.execute("SHOW DATABASES")  # 查詢獲取所有資料庫的列表
    databases = c.fetchall()
    # 查看資料庫列表，檢查是否存在目標資料庫名稱
    for db in databases:
        if db[0] == database_name:
            return True
    return False

# 建立'商家'資料表
def _create_stores_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `stores` (
            name VARCHAR(255),
            category VARCHAR(20),
            tag VARCHAR(20),
            preview_image VARCHAR(255),
            link VARCHAR(255),
            website VARCHAR(255),
            phone_number VARCHAR(15),
            PRIMARY KEY(name)
        )
    ''')
# 建立'關鍵字'資料表
def _create_keywords_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `keywords` (
            store_name VARCHAR(255),
            word VARCHAR(20),
            count INT,
            PRIMARY KEY(store_name, word),
            FOREIGN KEY(store_name) REFERENCES `Stores`(name)
        )
    ''')
# 建立'評論者'資料表
def _create_users_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `users` (
            id VARCHAR(255),
            level INT,
            PRIMARY KEY(id)
        )
    ''')
# 建立'留言'資料表
def _create_comments_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `comments` (
            store_name VARCHAR(255),
            sort INT,
            contents TEXT,
            time VARCHAR(20),
            rating INT,
            user_id VARCHAR(255),
            PRIMARY KEY(store_name, sort),
            FOREIGN KEY(store_name) REFERENCES `stores`(name),
            FOREIGN KEY(user_id) REFERENCES `users`(id)
        )
    ''')
# 建立'地點'資料表
def _create_locations_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `locations` (
            store_name VARCHAR(255),
            longitude DECIMAL(10,7),
            latitude DECIMAL(10,7),
            postal_code VARCHAR(5),
            city VARCHAR(3),
            dist VARCHAR(3),
            vil VARCHAR(3),
            details VARCHAR(255),
            PRIMARY KEY(store_name),
            FOREIGN KEY(store_name) REFERENCES `stores`(name)
        )
    ''')
# 建立'評分'資料表
def _create_rates_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `rates` (
            store_name VARCHAR(255),
            avg_ratings DECIMAL(3,1),
            total_ratings INT,
            total_comments INT,
            real_rating DECIMAL(2,1),
            store_responses INT,
            PRIMARY KEY(store_name),
            FOREIGN KEY(store_name) REFERENCES `stores`(name)
        )
    ''')

# 建立資料庫
def create_database(c, database_name) -> bool:
    if not exists(c, database_name):
        cursor.execute(f"CREATE DATABASE {database_name}")
        c.execute(f"USE `{database_name}`")
        _create_stores_table(c)
        _create_users_table(c)
        _create_rates_table(c)
        _create_locations_table(c)
        _create_comments_table(c)
        return True
    else:
        print(f"已存在名稱為'{database_name}'的資料庫。")
        return False

def drop_database(c, database_name) -> bool:
    if exists(c, database_name):
        c.execute('''
            DROP DATABASE `mapdb`;
        ''')
        return True
    else:
        print(f"不存在名稱為'{database_name}'的資料庫。")
        return False

def truncate_database(c, database_name) -> bool:
    if exists(c, database_name):
        drop_database(c, database_name)
        create_database(c, database_name)
        return True
    else:
        print(f"不存在名稱為'{database_name}'的資料庫。")
        return False


### 主程式 ###
connection = connect(use_database=False)
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
        print("清除資料表中的所有資料？[Y/N] ", end='')
        if input().lower() == 'y':
            truncate_database(cursor, NAME)
            print("清空資料庫成功！")
else:
    create_database(cursor, NAME)
# 關閉資料庫連線階段
cursor.close()
connection.close()
