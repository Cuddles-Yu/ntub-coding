import mysql.connector 

connection = mysql.connector.connect(
    user='root2', 
    password='Mi700329',
    host='localhost',
    database='mapdb',
    auth_plugin='mysql_native_password') 

cursor = connection.cursor()

#查詢並計算百分比
cursor.execute("SELECT (total_comments / total_ratings * 100) AS percentage FROM mapg WHERE total_ratings != 0")

#取得结果
percentage = cursor.fetchall()[0]

print("留言比:", percentage)

cursor.close()
connection.close()