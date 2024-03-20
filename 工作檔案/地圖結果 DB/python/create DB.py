#(pip install mysql-connector-python) 安裝第三方模組來連結

#引入模組
import mysql.connector 

#連結MySQL
connection = mysql.connector.connect(user='root2', password='Mi700329',
                              host='localhost',
                              auth_plugin='mysql_native_password') #指定 auth_plugin 告訴 MySQL 連接器使用舊的驗證外掛程式才能相容於舊版的 MySQL 和 MySQL 連接器。

#把connection存到cursor變數中(cursor 允許執行 SQL 查詢並處理查詢結果。)
cursor = connection.cursor()

#創建資料庫(用完要註解掉''' ''' 免得多執行它)
'''cursor.execute("CREATE DATABASE `mapg`;")  #在括號中輸入 SQL 語法'''

#取得所有資料庫名稱(檢查是否成功建立資料庫)
cursor.execute("SHOW DATABASES;")
records = cursor.fetchall() #將所有回傳的資料取出(是列表)
for r in records: #用迴圈將所有資料取出
    print(r)

#創建表格
#cursor.execute('CREATE TABLE ``')

cursor.close() #使用完這個變數後關閉他(以釋放資源、提高程式性能)
connection.close() #關閉連線