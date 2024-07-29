from typing import Optional
from 地圖資訊爬蟲.crawler.module.functions.database.core import *
import 地圖資訊爬蟲.crawler.module.functions.database.table as table

def create(connection, name, show_hint: Optional[bool] = True):
    cursor = connection.cursor()
    if not exists(connection, name):
        cursor.execute(f'CREATE DATABASE `{name}`')
        cursor.execute(f'USE `{name}`')
        table.create_administrators(cursor)
        table.create_tags(cursor)
        table.create_members(cursor)
        table.create_stores(cursor)
        table.create_openhours(cursor)
        table.create_rates(cursor)
        table.create_comments(cursor)
        table.create_locations(cursor)
        table.create_keywords(cursor)
        table.create_services(cursor)
        table.create_favorites(cursor)
        if show_hint: print(f"已建立名稱為'{name}'的資料庫。")
    else:
        if show_hint: print(f"已存在名稱為'{name}'的資料庫。")
    cursor.close()

def drop(connection, name, show_hint: Optional[bool] = True):
    cursor = connection.cursor()
    if exists(connection, name):
        cursor.execute(f'DROP DATABASE `{name}`')
        if show_hint: print(f"已刪除名稱為'{name}'的資料庫。")
    else:
        if show_hint: print(f"不存在名稱為'{name}'的資料庫。")
    cursor.close()

def truncate(connection, name, show_hint: Optional[bool] = True):
    if exists(connection, name):
        drop(connection, name, show_hint=False)
        create(connection, name, show_hint=False)
        if show_hint: print(f"已清空名稱為'{name}'的資料庫。")
    else:
        if show_hint: print(f"不存在名稱為'{name}'的資料庫。")
