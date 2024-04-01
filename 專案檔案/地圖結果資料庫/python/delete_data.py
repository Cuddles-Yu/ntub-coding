import mysql.connector
from update_data import connect_to_db


def delete_db(operation, table_name=None, column_name=None, column_value=None):
    connection = connect_to_db()
    cursor = connection.cursor()

    try:
        if operation == "drop_table":
        #刪除整個表格(如果存在)
            cursor.execute(f"DROP TABLE IF EXISTS {table_name}")
        
        elif operation == "drop_column":
        #刪除欄位
            cursor.execute(f"ALTER TABLE {table_name} DROP COLUMN {column_name} ")

        elif operation == "delete_value":
        #刪除整列
            cursor.execute(f"DELETE FROM {table_name} WHERE {column_name} = '{column_value}'")

       # 提交更改
        connection.commit() 
        print("資料庫結構修改成功！")
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:       
        cursor.close()
        connection.close()

if __name__ == "__main__":
    operation = input("請輸入要執行的操作 (drop_table, drop_column, delete_value): ")

    if operation == "drop_table":
        table_name = input("請輸入要新增欄位的表格名稱: ")
        delete_db(operation, table_name)
    elif operation == "drop_column":
        table_name = input("請輸入要修改欄位的表格名稱: ")
        column_name = input("請輸入要修改的欄位名稱: ")
        delete_db(operation, table_name, column_name)
    elif operation == "delete_value":
        table_name = input("請輸入要更新資料的表格名稱: ")
        column_name = input("請輸入新的欄位名稱: ")
        column_value = input("請輸入新的欄位值: ")
        delete_db(operation, table_name, column_name, column_value)
    else:
        print("無效的操作選項")