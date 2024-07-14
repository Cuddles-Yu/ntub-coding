from 地圖資訊爬蟲.crawler.tables._base import *

class Service:
    _store_id = 0
    _properties = ''
    _category = ''
    _state = 0

    def __init__(self, store_id, properties, category, state):
        self._store_id = int(store_id) if store_id else None
        self._properties = properties
        self._category = category
        self._state = state

    @property
    def store_id(self):
        return self._store_id

    @property
    def id(self):
        return 'DEFAULT'

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
        return f"({self.id}, {self.store_id}, {self.properties}, {self.category}, {self.state})"

    def insert(self, connection):
        mdb.add_data(connection, 'services', self.to_string())
