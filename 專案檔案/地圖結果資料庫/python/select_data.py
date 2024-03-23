############################################## 各種取得與查詢##############################################


#引入模組
import mysql.connector 

#連結MySQL
connection = mysql.connector.connect(user='root2', password='Mi700329',
                              host='localhost', database='mapg',
                              auth_plugin='mysql_native_password') #指定 auth_plugin 告訴 MySQL 連接器使用舊的驗證外掛程式才能相容於舊版的 MySQL 和 MySQL 連接器。

#把connection存到cursor變數中(cursor 允許執行 SQL 查詢並處理查詢結果。)
cursor = connection.cursor()

#取得所有資料庫名稱(檢查是否成功建立資料庫)
'''cursor.execute("SHOW DATABASES;")
records = cursor.fetchall() #將所有回傳的資料取出(是列表)
for r in records: #用迴圈將所有資料取出
    print(r)'''

#取得表格中所有資料
cursor.execute('SELECT * FROM mapg')
records = cursor.fetchall()
for r in records:
    print(r)

#取得表格中特定欄位資料
'''cursor.execute('SELECT name, address FROM mapg')
values = cursor.fetchall()
for v in values:
    print(v)'''

#取得表格中第一筆欄位資料
'''cursor.execute('SELECT * FROM mapg')
row = cursor.fetchone()
for r in row:
    print(r)'''

#取得所有欄位(檢查是否成功建立所需欄位)
'''cursor.execute('DESCRIBE mapg')
columns = cursor.fetchall()
for c in columns:
    print(c)'''

#根據欄位結果進行排序
'''cursor.execute("SELECT * FROM mapg ORDER BY ratings") #要查降序直接在後面加上DESC就好
sorted_records = cursor.fetchall()
for record in sorted_records:
    print(record)'''


cursor.close()
connection.close()