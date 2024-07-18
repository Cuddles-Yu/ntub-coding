from 地圖資訊爬蟲.crawler.tables._base import *

class OpenHours:
    _store_id = 0
    _day_of_week = ''
    _open_time = ''
    _close_time = ''

    def __init__(self, store_id, day_of_week, open_time, close_time):
        self._store_id = int(store_id) if store_id else None
        self._day_of_week = day_of_week
        self._open_time = open_time
        self._close_time = close_time

    @property
    def id(self):
        return 'DEFAULT'

    @property
    def store_id(self):
        return self._store_id

    @property
    def day_of_week(self):
        return transform(self._day_of_week)

    @property
    def open_time(self):
        return transform(self._open_time)

    @property
    def close_time(self):
        return transform(self._close_time)

    def to_string(self):
        return f"({self.id}, {self.store_id}, {self.day_of_week}, {self.open_time}, {self.close_time})"

    def insert(self, connection):
        mdb.add(connection, 'openhours', self.to_string())
