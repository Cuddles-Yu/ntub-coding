#引入模組
import mysql.connector 

#連結MySQL
connection = mysql.connector.connect(user='root2', password='Mi700329',
                              host='localhost', database='mapg',
                              auth_plugin='mysql_native_password') #指定 auth_plugin 告訴 MySQL 連接器使用舊的驗證外掛程式才能相容於舊版的 MySQL 和 MySQL 連接器。

#把connection存到cursor變數中(cursor 允許執行 SQL 查詢並處理查詢結果。)
cursor = connection.cursor()

#取得表格中所有資料
cursor.execute('SELECT * FROM `mapg`;')
records = cursor.fetchall()
for r in records:
    print(r)

cursor.close()
connection.close()