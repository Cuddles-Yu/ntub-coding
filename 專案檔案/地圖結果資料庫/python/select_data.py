############################################## 查詢 ##############################################
import mysql.connector
import database as db

def select_db(operation, table_name=None, columns=None):
    connection = db.connect(use_database=True)
    cursor = connection.cursor()
    try:
        if operation == "show_databases":
            # 取得所有資料庫名稱(檢查是否成功建立資料庫)
            cursor.execute("SHOW DATABASES;")
            records = cursor.fetchall()  # 將所有回傳的資料取出(是列表)
            for r in records:  # 用迴圈將所有資料取出
                print(r)
        elif operation == "select_table_value":
            # 取得表格中所有資料
            cursor.execute(f"SELECT * FROM {table_name}")
            records = cursor.fetchall()
            for r in records:
                print(r)
        elif operation == "select_columns":
            # 取得表格中特定欄位資料
            columns_str = ", ".join(columns)
            cursor.execute(f"SELECT {columns_str} FROM {table_name}")
            values = cursor.fetchall()
            for v in values:
                print(v)
        elif operation == "select_first_row":
            # 取得表格中第一筆欄位資料
            cursor.execute(f"SELECT * FROM {table_name}")
            row = cursor.fetchone()
            for r in row:
                print(r)
        elif operation == "describe_table":
            # 取得所有欄位(檢查是否成功建立所需欄位)
            cursor.execute(f"DESCRIBE {table_name}")
            columns = cursor.fetchall()
            for c in columns:
                print(c)
        elif operation == "sort_by_ratings":
            # 根據欄位結果進行排序
            cursor.execute(f"SELECT * FROM {table_name} ORDER BY ratings")  # 要查降序直接在後面加上DESC就好
            sorted_records = cursor.fetchall()
            for record in sorted_records:
                print(record)
        else:
            print("無效的操作選項")
    except mysql.connector.Error as error:
        print("Error:", error)
    finally:
        cursor.close()
        connection.close()

if __name__ == "__main__":
    operation = input("請輸入要執行的操作 (show_databases, select_table_value, select_columns, select_first_row, describe_table, sort_by_ratings): ")
    if operation == "select_table_value":
        table_name = input("請輸入要查詢欄位的表格名稱: ")
        select_db(operation, table_name)
    elif operation == "select_columns":
        table_name = input("請輸入要查詢欄位的表格名稱: ")
        columns_str = input("請輸入要查詢的欄位名稱（以逗號分隔）: ")
        columns = [col.strip() for col in columns_str.split(",")]
        select_db(operation, table_name, columns)
    elif operation == "select_first_row":
        table_name = input("請輸入要查詢資料的表格名稱: ")
        select_db(operation, table_name )
    elif operation == "describe_table":
        table_name = input("請輸入要查詢資料的表格名稱: ")
        select_db(operation, table_name )   
    elif operation == "sort_by_ratings":
        table_name = input("請輸入要查詢資料的表格名稱: ")
        select_db(operation, table_name )
    else:
        print("無效的操作選項")
    