import mysql.connector

def from_iterable(index, sql_return) -> list:
    return [result[index] for result in sql_return]

def exists(connection, name) -> bool:
    databases = from_iterable(0, fetch(connection, 'all', f'''
        SHOW DATABASES
    '''))
    return name in databases

def execute(connection, sql, *param):
    return fetch(connection, 'none', sql, *param)

def fetch_column(connection, mode: str, index, sql) -> list:
    sql_return = fetch(connection, mode, sql)
    if sql_return:
        if mode.lower() == 'all':
            return from_iterable(index, fetch(connection, mode, sql))
        elif mode.lower() == 'one':
            return fetch(connection, mode, sql)[index]
    return None

def fetch(connection, mode: str, sql, *param):
    cursor = connection.cursor()
    try:
        cursor.execute(sql, param)
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
