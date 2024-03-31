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
def exists(cursor, database_name: str) -> bool:
    cursor.execute("SHOW DATABASES")  # 查詢獲取所有資料庫的列表
    databases = cursor.fetchall()
    # 查看資料庫列表，檢查是否存在目標資料庫名稱
    for db in databases:
        if db[0] == database_name:
            return True
    return False