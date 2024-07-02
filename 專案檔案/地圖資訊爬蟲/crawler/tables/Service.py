from 地圖資訊爬蟲.crawler.tables._base import *

class Service:
    _store_id = 0
    _dine_in = True
    _take_away = True
    _delivery = True

    def __init__(self, store_id, dine_in, take_away, delivery):
        self._store_id = int(store_id) if store_id else None
        self._dine_in = dine_in
        self._take_away = take_away
        self._delivery = delivery

    @property
    def store_id(self):
        return self._store_id

    @property
    def dine_in(self):
        return transform(self._dine_in)

    @property
    def take_away(self):
        return transform(self._take_away)

    @property
    def delivery(self):
        return transform(self._delivery)

    def to_string(self):
        return f"({self.store_id}, {self.dine_in}, {self.take_away}, {self.delivery})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'services', 'store_id', self.store_id)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection): mdb.add_data(connection, 'services', self.to_string())
