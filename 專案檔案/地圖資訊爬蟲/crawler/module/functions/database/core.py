import mysql.connector
from mysql.connector import Error

def from_iterable(index, sql_return) -> list:
    return [result[index] for result in sql_return]

def exists(connection, name) -> bool:
    databases = from_iterable(0, fetch(connection, 'all', f'''
        SHOW DATABASES
    '''))
    return name in databases

def execute(connection, sql):
    return fetch(connection, 'none', sql)

def fetch_column(connection, mode: str, index, sql) -> list:
    return from_iterable(index, fetch(connection, mode, sql))

def fetch(connection, mode: str, sql):
    cursor = connection.cursor()
    try:
        cursor.execute(sql)
        if mode.lower() == 'all':
            result = cursor.fetchall()
            return result if result else None
        elif mode.lower() == 'one':
            result = cursor.fetchall()
            return result[0] if result else None
        else:
            return None
    except mysql.connector.Error as error:
        print(f'''\n執行時發生錯誤 = {{\n"錯誤指令": "{sql.strip()}",\n"錯誤資訊": "{error}"\n}}''')
    finally:
        # 提交更改
        connection.commit()
        cursor.close()
