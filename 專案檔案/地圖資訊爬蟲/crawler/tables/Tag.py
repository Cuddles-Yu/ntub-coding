from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

def refresh_all_tags(database: SqlDatabase):
    tags = database.fetch_column('all', 0, f'''
        SELECT DISTINCT tag FROM tags
    ''')
    store_tags = database.fetch_column('all', 0, f'''
        SELECT DISTINCT tag FROM stores
        WHERE tag IS NOT NULL
    ''')
    # 移除不存在於stores的標籤
    for tag in tags:
        if tag not in store_tags: database.delete_value('tags', tag=transform(tag))
    # 建立不存在於tags的標籤
    for store_tag in store_tags:
        if not database.is_value_exists('tags', tag=transform(store_tag)): database.add('tags', f"({transform(store_tag)}, NULL)")
    print(f"已成功更新tags資料表的所有資料。")

class Tag:
    _tag = ''
    _category = ''

    def __init__(self, tag, category):
        self._tag = tag
        self._category = category

    @property
    def tag(self):
        return transform(self._tag)

    @property
    def category(self):
        return transform(self._category)

    def to_string(self):
        return f"({self.tag}, {self.category})"

    def exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exists('tags', tag=self.tag)

    def insert_if_not_exists(self, database: SqlDatabase):
        if not self.exists(database): database.add('tags', self.to_string())
