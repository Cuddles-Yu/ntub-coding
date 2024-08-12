from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

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
