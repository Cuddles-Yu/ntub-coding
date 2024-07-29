import mysql.connector
from mysql.connector import Error

def from_iterable(index, sql_return) -> list:
    return [result[index] for result in sql_return]

def exists(connection, name) -> bool:
    databases = from_iterable(0, fetch(connection, 'all', f'''
        SHOW DATABASES
    '''))
    return name in databases

def connect(name, username, password):  # 連接資料庫
    try:
        connection = mysql.connector.connect(
            user=username,
            password=password,
            host='localhost',
            auth_plugin='mysql_native_password'
        )
        if not exists(connection, name): create(connection, name)
        connection.database = name
        return connection
    except Error:
        print('資料庫連線失敗，請確認服務是否啟用後再嘗試一次')
        exit()

def execute(connection, sql):
    return fetch(connection, 'none', sql)

def fetch_column(connection, mode: str, index, sql) -> list:
    return from_iterable(index, fetch(connection, mode, sql))

def fetch(connection, mode: str, sql):
    cursor = connection.cursor()
    try:
        cursor.execute(sql)
        if mode.lower() == 'all':
            return cursor.fetchall()
        elif mode.lower() == 'one':
            return cursor.fetchone()
        else:
            return None
    except mysql.connector.Error as error:
        print(f'''\n執行時發生錯誤 = {{\n"錯誤指令": "{sql.strip()}",\n"錯誤資訊": "{error}"\n}}''')
    finally:
        # 提交更改
        connection.commit()
        cursor.close()
