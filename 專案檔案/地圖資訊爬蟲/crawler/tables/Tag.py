from 地圖資訊爬蟲.crawler.tables.base import *

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

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'tags', 'tag', self.tag)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection): mdb.add(connection, 'tags', self.to_string())
