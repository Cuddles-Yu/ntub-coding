from typing import Optional

from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

def newObject(title, url):
    return Store(
        name=title,
        description=None,
        tag=None,
        preview_image=None,
        link=url,
        website=None,
        phone_number=None,
        last_update=None,
        crawler_state='DEFAULT',
        crawler_description=None
    )

def reset(database: SqlDatabase, store_name: str) -> str:
    cursor = database.connection.cursor()
    store_item = Reference(name=store_name)
    sid = store_item.get_id(database)
    if sid is not None:
        cursor.execute(f'''
            DELETE FROM `comments` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `services` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `keywords` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `locations` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `rates` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `openhours` WHERE `store_id` = '{sid}';
        ''')
        database.connection.commit()  # 提交修改
        store_item.change_state(database, '建立', None)
        print(f"已成功重設商家id為 '{sid}' 的所有資料。")
    cursor.close()
    return sid

def delete(database: SqlDatabase, store_name: str) -> str:
    cursor = database.connection.cursor()
    sid = Reference(name=store_name).get_id(database)
    if sid is not None:
        cursor.execute(f'''
            DELETE FROM `comments` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `services` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `keywords` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `locations` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `rates` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `openhours` WHERE `store_id` = '{sid}';
        ''')
        cursor.execute(f'''
            DELETE FROM `stores` WHERE `id` = '{sid}';
        ''')
        database.connection.commit()  # 提交修改
        print(f"已成功移除商家id為 '{sid}' 的所有資料。")
    cursor.close()
    return sid

class Store:
    _name = ''
    _description = ''
    _tag = ''
    _preview_image = ''
    _link = ''
    _website = ''
    _phone_number = ''
    _last_update = ''
    _crawler_state = ''
    _crawler_description = ''

    def __init__(self, name, description, tag, preview_image, link, website, phone_number, last_update, crawler_state, crawler_description):
        self._name = name
        self._description = description
        self._tag = tag
        self._preview_image = preview_image
        self._link = link
        self._website = website
        self._phone_number = phone_number
        self._last_update = last_update
        self._crawler_state = crawler_state
        self._crawler_description = crawler_description

    @property
    def id(self):
        return 'DEFAULT'

    @property
    def name(self):
        return transform(escape_quotes(self._name))

    @property
    def description(self):
        return transform(self._description)

    @property
    def tag(self):
        return transform(self._tag)

    @property
    def preview_image(self):
        return transform(self._preview_image)

    @property
    def link(self):
        return transform(self._link)

    @property
    def website(self):
        return transform(self._website)

    @property
    def phone_number(self):
        return transform(self._phone_number)

    @property
    def last_update(self):
        return transform(self._last_update)

    @property
    def crawler_state(self):
        return transform(self._crawler_state)

    @property
    def crawler_description(self):
        return transform(self._crawler_description)

    @property
    def crawler_time(self):
        return 'DEFAULT'

    def to_string(self):
        return f"({self.id}, {self.name}, {self.description}, {self.tag}, {self.preview_image}, {self.link}, {self.website}, {self.phone_number}, {self.last_update}, {self.crawler_state}, {self.crawler_description}, {self.crawler_time})"

    def to_value(self):
        return f"description={self.description}, tag={self.tag}, preview_image={self.preview_image}, link={self.link}, website={self.website}, phone_number={self.phone_number}, last_update={self.last_update}, crawler_state={self.crawler_state}, crawler_description={self.crawler_description}"

    def get_id(self, database: SqlDatabase):
        return database.get_value('id', 'stores', 'name', self.name)

    def get_tag(self):
        return self._tag

    def get_name(self):
        return self._name

    def change_state(self, database: SqlDatabase, state, description) -> bool:
        if not self.name_exists(database): return False
        database.update('stores', f'crawler_state={transform(state)}, crawler_description={transform(description)}', f'name={self.name}')
        return True

    def name_exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exist('stores', 'name', self.name)

    def exists(self, database: SqlDatabase, check_name: Optional[bool] = False) -> bool:
        if self.link is None: print("\n這不合理\n")
        link_exists = database.is_value_exist('stores', 'link', self.link)
        if not link_exists: return False
        if check_name:
            # 若link存在，且不存在名稱與連結相同的連結 -> 商家名字需變更
            if not database.is_condition_exist('stores', f'name = {self.name} and link = {self.link}'):
                old_name = transform(database.get_value('name', 'stores', 'link', self.link))
                database.update('stores', f'name = {self.name}', f'name = {old_name} and link = {self.link}')
                print(f'\r✏️已成功更新商家名稱 {old_name} -> {self.name}\n', end='')
        return True

    def get_state(self, database: SqlDatabase) -> (str, str):
        return (
            database.get_value('crawler_state', 'stores', 'name', self.name),
            database.get_value('crawler_description', 'stores', 'name', self.name)
        )

    def insert_if_not_exists(self, database: SqlDatabase):
        if not self.exists(database, check_name=True): database.add('stores', self.to_string())

    def update_if_exists(self, database: SqlDatabase):
        if self.exists(database, check_name=True):
            database.update('stores', self.to_value(), f'name={self.name}')
        else:
            database.add('stores', self.to_string())

    def reset(self, database: SqlDatabase):
        return reset(database, self.name)

    def delete(self, database: SqlDatabase):
        return delete(database, self.name)

class Reference(Store):
    def __init__(self, name):
        super().__init__(name, None, None, None, None, None, None, None, None, None)
