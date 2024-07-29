from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

class Favorite:
    _user_id = 0
    _store_id = 0

    def __init__(self, user_id, store_id):
        self._user_id = int(user_id)
        self._store_id = int(store_id)

    @property
    def user_id(self):
        return self._user_id

    @property
    def store_id(self):
        return self._store_id

    @property
    def create_time(self):
        return 'DEFAULT'

    def to_string(self):
        return f"({self.user_id}, {self.store_id}, {self.create_time})"

    def insert(self, database: SqlDatabase):
        database.add('favorites', self.to_string())
