import mysql.connector
import core_database as db

def countdb(table_name):
    connection = db.connect(use_database=True)
    cursor = connection.cursor()

    try:
        # 查詢並計算百分比
        cursor.execute(f"SELECT (total_comments / total_ratings* 100) AS percentage FROM {table_name} WHERE total_ratings != 0")

        # 取得結果
        percentage = cursor.fetchall()[0][0]  # 提取百分比值

        print("留言比:", percentage)

    except mysql.connector.Error as error:
        print("Error:", error)
    finally:
        cursor.close()
        connection.close()

if __name__ == "__main__":
    table_name = input("請輸入要查詢的表格名稱: ")
    countdb(table_name)
