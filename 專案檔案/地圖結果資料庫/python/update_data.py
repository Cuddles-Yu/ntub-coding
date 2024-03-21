import mysql.connector 

connection = mysql.connector.connect(user='root2', password='Mi700329',
                              host='localhost', database='mapg',
                              auth_plugin='mysql_native_password') #指定 auth_plugin 告訴 MySQL 連接器使用舊的驗證外掛程式才能相容於舊版的 MySQL 和 MySQL 連接器。

cursor = connection.cursor()

#新增欄位
cursor.execute("ALTER TABLE mapg ADD COLUMN tags VARCHAR(20) }")

#刪除欄位
#cursor.execute("ALTER TABLE mapg DROP COLUMN column_name")


#新增欄位資料
#cursor.execute("INSERT INTO `mapg` VALUES( 【】)")

#修改欄位資料
#cursor.execute("UPDATE `mapg` SET 【新欄位名稱】=【新的值】 WHERE 【舊欄位名稱】=【[舊的值】;")

#刪除欄位資料
#cursor.execute("DELETE FROM `mapg` WHERE 【欄位名稱】=【值】;")

#改變欄位類型
#cursor.execute("ALTER TABLE mapg MODIFY COLUMN ratings TEXT")

cursor.close()
connection.commit() #以上指令都會改變資料，需要這樣指令才會被提交上去(生效)
connection.close()