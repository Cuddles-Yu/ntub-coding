import mysql.connector 

connection = mysql.connector.connect(
    user='root2',
    password='Mi700329',
    host='localhost',
    database='mapdb',
    auth_plugin='mysql_native_password'
) 

cursor = connection.cursor()

#刪除整個表格(如果存在)
#cursor.execute("DROP TABLE IF EXISTS【表格名字】")

#刪除欄位
#cursor.execute("ALTER TABLE mapg DROP COLUMN tags ")

#刪除整列
#cursor.execute("DELETE FROM `mapg` WHERE phone_number='0226191234'")


cursor.close()
connection.commit() #以上指令都會改變資料，需要這樣指令才會被提交上去(生效)
connection.close()