from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

# 需要被移除的服務項目 '停車位不好找', '停車位很難找'

class Service:
    _store_id = 0
    _id = 0
    _properties = ''
    _category = ''
    _state = 0

    def __init__(self, store_id, sid, properties, category, state):
        self._store_id = int(store_id) if store_id else None
        self._id = int(sid) if sid else None
        self._properties = properties
        self._category = category
        self._state = state

    @property
    def store_id(self):
        return self._store_id

    @property
    def id(self):
        return self._id

    @property
    def properties(self):
        return transform(self._properties)

    @property
    def category(self):
        return transform(self._category)

    @property
    def state(self):
        return transform(self._state)

    def to_string(self):
        return f"({self.store_id}, {self.id}, {self.properties}, {self.category}, {self.state})"

    def insert(self, database: SqlDatabase):
        database.add('services', self.to_string())
