#(pip install mysql-connector-python) 安裝第三方模組來連結

############################################## 各種創建相關 ##############################################

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

#選擇資料庫
cursor.execute("USE `mapg`;")

#創建表格和定義欄位
#cursor.execute("CREATE TABLE mapg (name VARCHAR(255), x_coordinate DECIMAL(7,5),y_coordinate DECIMAL(7,5), link VARCHAR(255), ratings VARCHAR(255), total_ratings VARCHAR(255), total_comments VARCHAR(255), comments VARCHAR(255), address VARCHAR(255), webpage VARCHAR(255), phone_number INT(10), PRIMARY KEY(`name`))")


cursor.close() #使用完這個變數後關閉他(以釋放資源、提高程式性能)
connection.close() #關閉連線