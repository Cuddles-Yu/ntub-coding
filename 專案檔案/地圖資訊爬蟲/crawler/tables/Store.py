from typing import Optional

from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.database.core import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase


def newObject(title, url, branch_title: Optional[str] = None, branch_name: Optional[str] = None):
    return Store(
        name=title,
        branch_title=branch_title,
        branch_name=branch_name,
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


def reset_by_id(database: SqlDatabase, store_id: int) -> str:
    store_name = fetch_column(database.connection, 'one', 0, f'''
        SELECT name FROM stores
        WHERE id = {store_id}
    ''')
    if store_name is not None:
        reset_by_name(database, store_name, show_hint=False)
        print(f"已成功重設商家名稱為 '{store_name}' 的所有資料。")
    return store_name


def reset_by_name(database: SqlDatabase, store_name: str, show_hint: Optional[bool] = True) -> int:
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
        cursor.execute(f'''
            UPDATE `stores`
            SET `description` = NULL, `tag` = NULL, `preview_image` = NULL, `website` = NULL, `phone_number` = NULL, `last_update` = NULL
            WHERE `id` = '{sid}';
        ''')
        database.connection.commit()  # 提交修改
        store_item.change_crawler_state(database, '重設', None)
        if show_hint: print(f"已成功重設商家id為 '{sid}' 的所有資料。")
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
    _branch_title = ''
    _branch_name = ''
    _description = ''
    _tag = ''
    _preview_image = ''
    _link = ''
    _website = ''
    _phone_number = ''
    _last_update = ''
    _crawler_state = ''
    _crawler_description = ''

    def __init__(self, name, branch_title, branch_name, description, tag, preview_image, link, website, phone_number, last_update, crawler_state,
                 crawler_description):
        self._name = name
        self._branch_title = branch_title
        self._branch_name = branch_name
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
    def branch_title(self):
        return transform(escape_quotes(self._branch_title))

    @branch_title.setter
    def branch_title(self, value):
        self._branch_title = value

    @property
    def branch_name(self):
        return transform(escape_quotes(self._branch_name))

    @branch_name.setter
    def branch_name(self, value):
        self._branch_name = value

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

    @crawler_state.setter
    def crawler_state(self, value):
        self._crawler_state = value

    @property
    def crawler_description(self):
        return transform(self._crawler_description)

    @crawler_description.setter
    def crawler_description(self, value):
        self._crawler_description = value

    @property
    def crawler_time(self):
        return 'DEFAULT'

    def to_string(self):
        return (f"({self.id}, {self.name}, {self.branch_title}, {self.branch_name}, {self.description}, {self.tag}, {self.preview_image}, {self.link}, {self.website}, " +
                f"{self.phone_number}, {self.last_update}, {self.crawler_state}, {self.crawler_description}, {self.crawler_time})")

    def to_dict(self) -> dict:
        return {
            "description": self.description,
            "tag": self.tag,
            "preview_image": self.preview_image,
            "link": self.link,
            "website": self.website,
            "phone_number": self.phone_number,
            "last_update": self.last_update,
            "crawler_state": self.crawler_state,
            "crawler_description": self.crawler_description
        }

    def get_id(self, database: SqlDatabase):
        return database.get_value('id', 'stores', name=self.name)

    def get_tag(self):
        return self._tag

    def get_name(self):
        return self._name

    def get_code(self, database: SqlDatabase):
        return f'id:{self.get_id(database)}, {self.get_name()}'

    def change_crawler_state(self, database: SqlDatabase, state, description) -> bool:
        if self._link and not self.exists(database, check_name=True): return False
        self.crawler_state = state
        self.crawler_description = description
        database.update(
            'stores',
            {"crawler_state": transform(state), "crawler_description": transform(description)},
            {"name": self.name}
        )
        return True

    def change_branch(self, database: SqlDatabase, title, name) -> bool:
        self.branch_title = title
        self.branch_name = name
        database.update(
            'stores',
            {"branch_title": self.branch_title, "branch_name": self.branch_name},
            {"name": self.name}
        )

    def name_exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exists('stores', name=self.name)

    def exists(self, database: SqlDatabase, check_name: Optional[bool] = False) -> bool:
        if not database.is_value_exists('stores', link=self.link) and not database.is_value_exists('stores', name=self.name): return False
        if check_name:
            # 若link存在，且不存在名稱與連結相同的連結 -> 商家名字需變更
            if not database.is_value_exists('stores', name=self.name, link=self.link):
                old_name = transform(database.get_value('name', 'stores', link=self.link))
                database.update(
                    'stores',
                    {"name": self.name},
                    {"name": old_name, "link": self.link}
                )
                print(f'\r✏️已成功更新商家名稱 {old_name} -> {self.name}\n', end='')
        return True

    def get_state(self, database: SqlDatabase) -> (str, str):
        return (
            database.get_value('crawler_state', 'stores', name=self.name),
            database.get_value('crawler_description', 'stores', name=self.name)
        )

    def insert_if_not_exists(self, database: SqlDatabase):
        if not self.exists(database, check_name=True): database.add('stores', self.to_string())

    def update_if_exists(self, database: SqlDatabase):
        if self.exists(database, check_name=True):
            database.update('stores', self.to_dict(), {"name": self.name})
        else:
            database.add('stores', self.to_string())

    def reset(self, database: SqlDatabase):
        return reset_by_name(database, self._name, show_hint=False)

    def delete(self, database: SqlDatabase):
        return delete(database, self._name)


class Reference(Store):

    def __init__(self, name):
        super().__init__(name, None, None, None, None, None, None, None, None, None, None, None)
