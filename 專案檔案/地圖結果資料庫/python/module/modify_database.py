import mysql.connector
from itertools import chain

#### 新增+修改
# 新增欄位
def add_column(connection, table_name, column_name, column_type):
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            ALTER TABLE {table_name} ADD COLUMN {column_name} {column_type}
        ''')
        print("資料庫結構修改成功！")
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit()  # 以上指令都會a改變資料，需要這樣指令才會被提交上去(生效)
        cursor.close() 

# 改變欄位的位置
def change_column(connection, table_name, column_name, column_type):
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            ALTER TABLE {table_name} 
            MODIFY COLUMN {column_name} {column_type} 
            AFTER {column_name}
        ''')
        print("資料庫結構修改成功！")
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close() 

# 手動修改欄位資料
def update_data(connection, new_column_name, new_column_value, old_column_name, old_column_value):
    cursor = connection.cursor()
    try:
        sql = "UPDATE %s SET  %s = %s WHERE %s = %s"
        cursor.execute(sql, (new_column_name, new_column_value, old_column_name, old_column_value))
        print("資料庫結構修改成功！")
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close() 

# 改變欄位類型
def change_type(connection, table_name, column_name, column_type):
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            ALTER TABLE {table_name} MODIFY COLUMN {column_name} {column_type}
        ''')
        print("資料庫結構修改成功！")
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close() 

# 手動新增欄位資料
def add_data(connection, table_name, values):
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            INSERT INTO {table_name} VALUES {values}
        ''')
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close() 


#### 刪除
# 刪除整個表格(如果存在)
def drop_table(connection, table_name):
    cursor = connection.cursor()
    try:
        sql = "DROP TABLE IF EXISTS %s" 
        cursor.execute(sql, (table_name,))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close() 

# 刪除欄位
def drop_column(connection, table_name, column_name):
    cursor = connection.cursor()
    try:
        sql = "ALTER TABLE %s DROP COLUMN %s" 
        cursor.execute(sql, (table_name, column_name,))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close() 

# 刪除整筆資料
def delete_value(connection, table_name, column_name, values):
    cursor = connection.cursor()
    try:
        sql = "DELETE FROM %s WHERE %s = %s" 
        cursor.execute(sql, (table_name, column_name, values,))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close() 

#### 查詢
# 取得所有資料庫名稱(檢查是否成功建立資料庫)
def show_databases(connection):
    cursor = connection.cursor()
    try:
        cursor.execute("SHOW DATABASES;")
        records = cursor.fetchall()  # 將所有回傳的資料取出(是列表)
        for r in records:  # 用迴圈將所有資料取出
            print("資料庫結構查詢成功！" + str(r))  # 將 tuple 轉換為字串
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close() 

# 取得表格中所有資料
def select_table_value(connection, table_name):
    cursor = connection.cursor()
    try:
        sql = "SELECT * FROM %s;"
        cursor.execute(sql, (table_name,))
        records = cursor.fetchall() 
        for r in records: 
            print("資料庫結構查詢成功！" + str(r))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close()

# 取得表格中的所有資料
def select_table_value_by_column(connection, columns, table_name) -> set:
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            SELECT {columns} FROM {table_name}
        ''')
        return set(chain.from_iterable(cursor.fetchall()))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close()

# 取得表格中符合特定條件的資料
def select_table_value_by_where(connection, target_column, table_name, column_name, value) -> set:
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            SELECT `{target_column}` FROM {table_name}
            WHERE `{column_name}` = "{value}"
        ''')
        return set(chain.from_iterable(cursor.fetchall()))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close()

# 取得表格中指定欄位中存在指定值的總數
def get_value_count(connection, table_name, column_name, value) -> int:
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            SELECT COUNT(*) FROM {table_name}
            WHERE {column_name} = {value}
        ''')
        return int(cursor.fetchone()[0])
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close()

# 取得表格中指定欄位中是否存在指定的值
def is_value_exist(connection, table_name, column_name, value) -> bool:
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            SELECT COUNT(*) FROM {table_name}
            WHERE {column_name} = {value}
        ''')
        return int(cursor.fetchone()[0]) > 0
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close()

# 取得表格中特定欄位資料
def select_columns(connection, column_name, table_name):
    cursor = connection.cursor()
    try:
        sql = "SELECT (%s) FROM %s;"
        cursor.execute(sql, (column_name, table_name,))
        values = cursor.fetchall() 
        for v in values: 
            print("資料庫結構查詢成功！" + str(v))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close() 

# 取得表格中第一筆欄位資料
def select_first_row(connection, table_name):
    cursor = connection.cursor()
    try:
        sql = "SELECT * FROM %s;"
        cursor.execute(sql, (table_name,))
        records = cursor.fetchone() 
        for r in records: 
            print("資料庫結構查詢成功！" + str(r))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close() 

# 取得所有欄位(檢查是否成功建立所需欄位)
def describe_table(connection, table_name):
    cursor = connection.cursor()
    try:
        sql = "DESCRIBE" + table_name 
        cursor.execute(sql)
        columns = cursor.fetchall() 
        for c in columns: 
            print("資料庫結構查詢成功！" + str(c))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close() 
    
# 根據欄位結果進行排序
def sort_values(connection, table_name, column_name):
    cursor = connection.cursor()
    try:
        sql = "SELECT * FROM %s ORDER BY %s;"
        cursor.execute(sql, (table_name, column_name))
        sorted_records = cursor.fetchall() 
        for r in sorted_records: 
            print("資料庫結構查詢成功！" + str(r))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close() 
