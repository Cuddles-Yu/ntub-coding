import mysql.connector
from itertools import chain

def execute(connection, sql):
    return fetch(connection, 'none', sql)

def fetch(connection, mode: str, sql):
    cursor = connection.cursor()
    try:
        cursor.execute(sql)
        match mode.lower():
            case 'all':
                return cursor.fetchall()
            case 'one':
                return cursor.fetchone()
            case _:
                return None
    except mysql.connector.Error as error:
        print(f'''\n執行時發生錯誤 = {{\n"錯誤指令": "{sql.strip()}",\n"錯誤資訊": "{error}"\n}}''')
    finally:
        # 提交更改
        connection.commit()
        cursor.close()


# 手動新增欄位資料
def add(connection, table_name, values):
    execute(connection, f'''
        INSERT INTO {table_name}
        VALUES {values}
    ''')

# 手動修改欄位資料
def update(connection, table_name, setter, condition):
    execute(connection, f'''
        UPDATE {table_name}
        SET {setter}
        WHERE {condition}
    ''')

# 設定自動遞增欄位值
def set_increment(connection, table_name, value):
    execute(connection, f'''
        ALTER TABLE {table_name} AUTO_INCREMENT = {value};
    ''')

#### 新增+修改
# 新增欄位
def add_column(connection, table_name, column_name, column_type):
    execute(connection, f'''
        ALTER TABLE {table_name} 
        ADD COLUMN {column_name} {column_type}
    ''')

# 改變欄位的位置
def change_column(connection, table_name, column_name, column_type):
    execute(connection, f'''
        ALTER TABLE {table_name} 
        MODIFY COLUMN {column_name} {column_type} 
        AFTER {column_name}
    ''')

def update_column(connection, table_name, target_column, target_value, condition_column, condition_value):
    execute(connection, f'''
        UPDATE {table_name}
        SET {target_column} = {target_value}
        WHERE {condition_column} = {condition_value}
    ''')

# 改變欄位類型
def change_type(connection, table_name, column_name, column_type):
    execute(connection, f'''
        ALTER TABLE {table_name} MODIFY COLUMN {column_name} {column_type}
    ''')

#### 刪除
# 刪除整個表格(如果存在)
def drop_table(connection, table_name):
    execute(connection, f'''
        DROP TABLE IF EXISTS {table_name}
    ''')

# 刪除欄位
def drop_column(connection, table_name, column_name):
    execute(connection, f'''
        ALTER TABLE {table_name} DROP COLUMN {column_name}
    ''')

# 刪除整筆資料
def delete_value(connection, table_name, column_name, values):
    execute(connection, f'''
        DELETE FROM {table_name} WHERE {column_name} = {values}
    ''')

#### 查詢
# 取得所有資料庫名稱(檢查是否成功建立資料庫)
def show_databases(connection):
    records = fetch(connection, 'all', f'''
        SHOW DATABASES
    ''')
    for r in records:
        print(str(r))

# 取得表格中所有資料
def show_table_value(connection, table_name):
    records = fetch(connection, 'all', f'''
        SELECT * FROM {table_name}
    ''')
    for r in records:
        print(str(r))

# 取得表格中的所有資料
def select_table_value_by_column(connection, columns, table_name) -> set:
    return fetch(connection, 'all', f'''
        SELECT {columns} FROM {table_name}
    ''')

# 取得不完整資料的商家連結
def get_urls_from_incomplete_store(connection) -> set:
    return set(chain.from_iterable(fetch(connection, 'all', f'''
        SELECT DISTINCT link FROM stores
        WHERE crawler_state IN ('建立')
    ''')))

# 取得表格中符合特定條件的資料
def select_table_value_by_where(connection, target_column, table_name, column_name, value) -> set:
    return set(chain.from_iterable(fetch(connection, 'all', f'''
        SELECT `{target_column}` FROM {table_name}
        WHERE `{column_name}` = "{value}"
    ''')))

# 取得表格中指定欄位中的指定值
def get_value(connection, target_column, table_name, column_name, value) -> str:
    result = fetch(connection, 'one', f'''
        SELECT {target_column} FROM {table_name}
        WHERE {column_name} = {value}
    ''')
    return str(result[0]) if result else None

# 取得表格中指定欄位中存在指定值的總數
def get_value_count(connection, table_name, column_name, value) -> int:
    result = fetch(connection, 'one', f'''
        SELECT COUNT(*) FROM {table_name}
        WHERE {column_name} = {value}
    ''')
    return int(result[0]) if result else 0

# 取得表格中指定欄位中是否存在指定的值
def is_value_exist(connection, table_name, column_name, value) -> bool:
    result = fetch(connection, 'one', f'''
        SELECT COUNT(*) FROM {table_name}
        WHERE {column_name} = {value}
    ''')
    return int(result[0]) > 0 if result else False

# 取得表格中特定欄位資料
def select_columns(connection, column_name, table_name):
    return set(chain.from_iterable(fetch(connection, 'all', f'''
        SELECT {column_name} FROM {table_name}    
    ''')))

# 取得所有欄位(檢查是否成功建立所需欄位)
def show_table(connection, table_name):
    records = fetch(connection, 'all', f'''
        DESCRIBE {table_name}
    ''')
    for r in records:
        print(str(r))
    
# 根據欄位結果進行排序
def sort_values(connection, table_name, column_name):
    records = fetch(connection, 'all', f'''
        SELECT * FROM {table_name}
        ORDER BY {column_name}
    ''')
    for r in records:
        print(str(r))
