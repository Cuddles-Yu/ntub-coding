############################################## 各種創建相關 ##############################################
#(pip install mysql-connector-python) 安裝第三方模組來連結

#引入模組
import mysql.connector 

# 函式用於檢查資料庫是否存在
def database_exists(cursor, database_name):
    cursor.execute("SHOW DATABASES") #查詢獲取所有資料庫的列表
    databases = cursor.fetchall()

    #查看資料庫列表，檢查是否存在目標資料庫名稱
    for db in databases:
        if db[0] == database_name:
            return True  # 如果目標資料庫存在，返回 True

    return False  # 如果目標資料庫不存在，返回 False

# 函式用於創建資料庫
def create_database(cursor, database_name):
    cursor.execute("CREATE DATABASE {}".format(database_name))

def main():
    #連結MySQL
    connection = mysql.connector.connect(
    user='root2',
    password='Mi700329',
    host='localhost',
    auth_plugin='mysql_native_password' #指定 auth_plugin 告訴 MySQL 連接器使用舊的驗證外掛程式才能相容於舊版的 MySQL 和 MySQL 連接器。
) 

    #把connection存到cursor變數中(cursor 允許執行 SQL 查詢並處理查詢結果。)
    cursor = connection.cursor()

    # 要檢查的資料庫名稱
    database_name = "test"


    # 如果資料庫不存在，則創建資料庫，否則顯示資料庫已存在的訊息
    if not database_exists(cursor, database_name):
        create_database(cursor, database_name)
        print(f"Database '{database_name}' created.")
    else:
        print(f"Database '{database_name}' already exists.")

    # 選擇要操作的資料庫
    cursor.execute(f"USE `{database_name}`")
    
    # 創建表格和定義欄位
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS `stores` (
            name VARCHAR(255),
            tags VARCHAR(20),
            categories VARCHAR(20),
            link VARCHAR(255),
            webpage VARCHAR(255),
            phone_number VARCHAR(255),
            key_words TEXT,
            PRIMARY KEY(name)
        )'''
)

    cursor.execute('''
    CREATE TABLE IF NOT EXISTS `comments` (
            store_name VARCHAR(255) REFERENCES `stores`(name),
            sort INT,
            contents TEXT,
            time VARCHAR(20),
            stars INT,
            user_id VARCHAR(255) REFERENCES `users`(id),
            PRIMARY KEY(store_name, sort)
        )'''
)

    cursor.execute('''
    CREATE TABLE IF NOT EXISTS `locations` (
            store_name VARCHAR(255) REFERENCES `stores`(name),
            postal_code VARCHAR(5),
            city VARCHAR(3),
            dist VARCHAR(3),
            vil VARCHAR(3),
            details VARCHAR(255),
            PRIMARY KEY(store_name)
        )'''
)

    cursor.execute('''
    CREATE TABLE IF NOT EXISTS `responses` (
            store_name VARCHAR(255) REFERENCES `stores`(name),
            average_ratings DECIMAL(3,1),
            total_ratings INT,
            total_comments INT,
            real_rates DECIMAL(2,1),
            times INT,
            PRIMARY KEY(store_name)
        )'''
)

    cursor.execute('''
    CREATE TABLE IF NOT EXISTS `users` (
            id VARCHAR(255),
            level VARCHAR(255),
            PRIMARY KEY(id)
        )'''
)

    cursor.close() #使用完這個變數後關閉他(以釋放資源、提高程式性能)
    connection.close() #關閉連線

# 如果這個檔案是作為主程式被執行的，則執行主函式
if __name__ == "__main__":
    main()