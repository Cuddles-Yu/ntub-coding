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
    database_name = "mapg"


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
        CREATE TABLE IF NOT EXISTS mapg (
            name VARCHAR(255),
            x_coordinate DECIMAL(7,5),
            y_coordinate DECIMAL(7,5),
            link VARCHAR(255),
            ratings VARCHAR(255),
            total_ratings VARCHAR(255),
            total_comments VARCHAR(255),
            comments VARCHAR(255),
            address VARCHAR(255),
            webpage VARCHAR(255),
            phone_number INT(10),
            PRIMARY KEY(name)
        )
    ''')


    cursor.close() #使用完這個變數後關閉他(以釋放資源、提高程式性能)
    connection.close() #關閉連線

# 如果這個檔案是作為主程式被執行的，則執行主函式
if __name__ == "__main__":
    main()