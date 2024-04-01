import mysql.connector 

# 連接資料庫
def connect_to_db():
    connection = mysql.connector.connect(
        user='root2',
        password='Mi700329',
        host='localhost',
        database='mapdb',
        auth_plugin='mysql_native_password'
    ) 
    return connection 

# 修改資料庫結構 # 設定參數 operation 可以指定要執行的操作類型
def modify_db(operation, table_name=None, column_name=None, column_type=None, new_column_name=None, new_column_value=None, old_column_name=None, old_column_value=None):
    connection = connect_to_db()
    cursor = connection.cursor()

    try:
        if operation == "add_column":
        # 新增欄位
            cursor.execute(f"ALTER TABLE {table_name} ADD COLUMN {column_name} {column_type} ")

        elif operation == "modify_column":
        # 改變欄位的位置
            cursor.execute(f"ALTER TABLE {table_name} MODIFY COLUMN {column_name} {column_type} AFTER {column_name}")

        elif operation == "update_data":
        # 手動修改欄位資料
            cursor.execute(f"UPDATE {table_name} SET {new_column_name}={new_column_value} WHERE {old_column_name}={old_column_value};")

        elif operation == "change_type":
        #改變欄位類型
            cursor.execute(f"ALTER TABLE {table_name} MODIFY COLUMN {column_name} {column_type}")

        elif operation == "add_data":
            #手動新增欄位資料
            values = input("請以逗號分隔輸入要插入的值: ")
            cursor.execute(f"INSERT INTO {table_name} VALUES({values})")        

        # 提交更改
        connection.commit() #以上指令都會a改變資料，需要這樣指令才會被提交上去(生效)
        print("資料庫結構修改成功！")
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close() 
        connection.close()

if __name__ == "__main__":
    operation = input("請輸入要執行的操作 (add_column, modify_column, update_data, change_type, add_data): ")

    if operation == "add_column":
        table_name = input("請輸入要新增欄位的表格名稱: ")
        column_name = input("請輸入要新增的欄位名稱: ")
        column_type = input("請輸入新欄位的資料類型: ")
        modify_db(operation, table_name, column_name, column_type)
    elif operation == "modify_column":
        table_name = input("請輸入要修改欄位的表格名稱: ")
        column_name = input("請輸入要修改的欄位名稱: ")
        column_type = input("請輸入新欄位的資料類型: ")
        modify_db(operation, table_name, column_name, column_type)
    elif operation == "update_data":
        table_name = input("請輸入要更新資料的表格名稱: ")
        new_column_name = input("請輸入新的欄位名稱: ")
        new_column_value = input("請輸入新的欄位值: ")
        old_column_name = input("請輸入舊的欄位名稱: ")
        old_column_value = input("請輸入舊的欄位值: ")
        modify_db(operation, table_name, None, None, new_column_name, new_column_value, old_column_name, old_column_value)
    elif operation == "change_type":
        table_name = input("請輸入要修改欄位類型的表格名稱: ")
        column_name = input("請輸入要修改的欄位名稱: ")
        column_type = input("請輸入新的資料類型: ")
        modify_db(operation, table_name, column_name, column_type)
    elif operation == "add_data":
        table_name = input("請輸入要插入資料的表格名稱: ")
        modify_db(operation, table_name)
    else:
        print("無效的操作選項")