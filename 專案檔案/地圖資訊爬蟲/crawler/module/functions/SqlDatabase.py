from typing import Optional

from 地圖資訊爬蟲.crawler.module.const import *
from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.database import schema
from 地圖資訊爬蟲.crawler.module.functions.database.core import *

### 常數參數 ###
RESET_ASKING = False

def connect(name, username, password):  # 連接資料庫
    try:
        connection = mysql.connector.connect(
            user=username,
            password=password,
            host='localhost',
            auth_plugin='mysql_native_password'
        )
        if not exists(connection, name): schema.create(connection, name)
        connection.database = name
        return connection
    except Exception:
        print('資料庫連線失敗，請確認服務是否啟用後再嘗試一次')
        exit()

def dict_to_clause(conditions: dict, connector: str) -> str:
    return connector.join([f'{k} = {transform(escape_quotes(v))}' for k, v in conditions.items()])

class SqlDatabase:
    ### 連線參數 ###
    name = ''
    username = ''
    password = ''

    ### 變數 ###
    connection: Optional[mysql.connector.MySQLConnection] = None

    ### 基礎 ###
    def __init__(self, name: str, username: str, password: str):
        self.name = name
        self.username = username
        self.password = password
        self.connection = connect(self.name, self.username, self.password)
        if RESET_ASKING: self.ask_to_reset_database()

    def close(self):
        self.connection.close()

    def execute(self, sql, *param):
        execute(self.connection, sql, *param)

    def fetch(self, mode, sql):
        return fetch(self.connection, mode, sql)

    def fetch_column(self, mode: str, index, sql) -> list:
        return fetch_column(self.connection, mode, index, sql)

    ### 涵式 ###
    def get_urls_from_incomplete_store(self) -> list:
        # 建立、基本、重設
        if BRANCH_STORE_FIRST:
            return fetch_column(self.connection, 'all', 0, f'''
                SELECT link FROM stores
                WHERE crawler_description IS NULL and branch_title IS NOT NULL
                ORDER BY id
            ''')
        else:
            return fetch_column(self.connection, 'all', 0, f'''
                SELECT link FROM stores
                WHERE crawler_description IS NULL
                ORDER BY id
            ''')

    def compare_to_stores(self, name):
        return fetch_column(self.connection, 'all', 0, f'''
            SELECT link FROM stores
            WHERE name = {name} AND crawler_description IS NULL
            ORDER BY id
        ''')

    def ask_to_reset_database(self):
        cursor = self.connection.cursor()
        print(f"清除'{self.name}'資料庫中的所有資料？[YES] ", end='')
        if input() == 'YES':
            schema.truncate(cursor, self.name, show_hint=False)
            print("清空資料庫成功！")
        cursor.close()

    # 手動新增欄位資料
    def add(self, table_name, values):
        execute(self.connection, f'''
            INSERT INTO {table_name}
            VALUES {values}
        ''')

    # 刪除整筆資料
    def delete(self, table_name, **where):
        execute(self.connection, f'''
            DELETE FROM {table_name}
            WHERE {dict_to_clause(where, ' AND ')}
        ''')

    # 手動修改欄位資料
    def update(self, table_name: str, setter: dict, where: dict):
        execute(self.connection, f'''
            UPDATE {table_name}
            SET {dict_to_clause(setter, ', ')}
            WHERE {dict_to_clause(where, ' AND ')}
        ''')

    # 設定自動遞增欄位值
    def set_increment(self, table_name, value):
        execute(self.connection, f'''
            ALTER TABLE {table_name}
            AUTO_INCREMENT = {value}
        ''')

    # 新增欄位
    def add_column(self, table_name, column_name, column_type):
        execute(self.connection, f'''
            ALTER TABLE {table_name} 
            ADD COLUMN {column_name} {column_type}
        ''')

    # 改變欄位的位置
    def change_column(self, table_name, column_name, column_type):
        execute(self.connection, f'''
            ALTER TABLE {table_name} 
            MODIFY COLUMN {column_name} {column_type} 
            AFTER {column_name}
        ''')

    # 改變欄位類型
    def change_type(self, table_name, column_name, column_type):
        execute(self.connection, f'''
            ALTER TABLE {table_name} MODIFY COLUMN {column_name} {column_type}
        ''')

    # 清空整個資料表
    def truncate_table(self, table_name):
        execute(self.connection, f'''
            TRUNCATE TABLE {table_name}
        ''')

    # 刪除整個資料表(如果存在)
    def drop_table(self, table_name):
        execute(self.connection, f'''
            DROP TABLE IF EXISTS {table_name}
        ''')

    # 刪除欄位
    def drop_column(self, table_name, column_name):
        execute(self.connection, f'''
            ALTER TABLE {table_name} 
            DROP COLUMN {column_name}
        ''')

    # 取得表格中的所有資料
    def select_table_value_by_column(self, columns, table_name) -> set:
        return fetch(self.connection, 'all', f'''
            SELECT {columns} FROM {table_name}
        ''')

    # 取得表格中符合特定條件的資料
    def select_table_value_by_where(self, target_column, table_name, **where) -> list:
        return from_iterable(0, fetch(self.connection, 'all', f'''
            SELECT `{target_column}` FROM {table_name}
            WHERE `{dict_to_clause(where, ' AND ')}"
        '''))

    # 取得表格中指定欄位中的指定值
    def get_value(self, target_column, table_name, **where) -> str:
        result = fetch(self.connection, 'one', f'''
            SELECT {target_column} FROM {table_name}
            WHERE {dict_to_clause(where, ' AND ')}
        ''')
        return result[0] if result else None

    # 取得表格中指定欄位中存在指定值的總數
    def get_value_count(self, table_name, **where) -> int:
        result = fetch(self.connection, 'one', f'''
            SELECT COUNT(*) FROM {table_name}
            WHERE {dict_to_clause(where, ' AND ')}
        ''')
        return int(result[0]) if result else 0

    def is_value_exists(self, table_name, **where) -> bool:
        result = fetch(self.connection, 'one', f'''
            SELECT COUNT(*) FROM {table_name}
            WHERE {dict_to_clause(where, ' AND ')}
        ''')
        return int(result[0]) > 0 if result else False

    # 取得表格中特定欄位資料
    def select_columns(self, column_name, table_name):
        return from_iterable(0, fetch(self.connection, 'all', f'''
            SELECT {column_name} FROM {table_name}    
        '''))
