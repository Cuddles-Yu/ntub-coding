import mysql.connector
from itertools import chain

#### 新增+修改
# 新增欄位
def add_column(connection, table_name, column_name, column_type):
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            ALTER TABLE {table_name} 
            ADD COLUMN {column_name} {column_type}
        ''')
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
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close() 

# 手動修改欄位資料
def update_data(connection, table_name, target_column, target_value, condition_column, condition_value):
    cursor = connection.cursor()
    try:
        cursor.execute(f"""
            UPDATE {table_name}
            SET {target_column} = {target_value}
            WHERE {condition_column} = {condition_value}
        """)
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        # 提交更改
        connection.commit() 
        cursor.close()
def update(connection, table_name, setter, condition):
    cursor = connection.cursor()
    try:
        cursor.execute(f"""
            UPDATE {table_name}
            SET {setter}
            WHERE {condition}
        """)
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
        cursor.execute(f"""
            DROP TABLE IF EXISTS {table_name}
        """)
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
        cursor.execute(f"""
            ALTER TABLE {table_name} DROP COLUMN {column_name}
        """)
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
        cursor.execute(f"""
            DELETE FROM {table_name} WHERE {column_name} = {values}
        """)
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
        cursor.execute(f"""
            SHOW DATABASES;
        """)
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
        cursor.execute(f"""
            SELECT * FROM {table_name}
        """)
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
        return cursor.fetchall()
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close()

# 取得未正確爬取留言與評分的商家地圖連結
def get_urls_from_no_ratings_store(connection) -> set:
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            SELECT s.link FROM stores AS s
            INNER JOIN rates AS r ON s.name = r.store_name
            WHERE r.avg_ratings = 0.0
        ''')
        return set(chain.from_iterable(cursor.fetchall()))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close()

def get_urls_from_incomplete_store(connection) -> set:
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            SELECT DISTINCT link FROM rates AS r
            RIGHT JOIN stores AS s ON s.id = r.store_id
            WHERE r.store_id IS NULL
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

# 取得表格中指定欄位中的指定值
def get_value(connection, target_column, table_name, column_name, value) -> str:
    cursor = connection.cursor()
    try:
        cursor.execute(f'''
            SELECT {target_column} FROM {table_name}
            WHERE {column_name} = {value}
        ''')
        result = cursor.fetchone()
        if result is None:
            return None
        else:
            return str(result[0])
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
        cursor.execute(f"""
            SELECT {column_name} FROM {table_name};
        """)
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
        cursor.execute(f"""
            SELECT * FROM {table_name};
        """)
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
        cursor.execute(f"""
            DESCRIBE {table_name};
        """)
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
        cursor.execute(f"""
            SELECT * FROM {table_name} ORDER BY {column_name};
        """)
        sorted_records = cursor.fetchall() 
        for r in sorted_records: 
            print("資料庫結構查詢成功！" + str(r))
    except mysql.connector.Error as error:
        # 處理錯誤
        print("Error:", error)
    finally:
        cursor.close() 

