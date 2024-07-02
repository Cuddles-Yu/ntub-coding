from 地圖資訊爬蟲.crawler.tables._base import *

class Contributor:
    _id = ''
    _level = 0

    def __init__(self, uid, level):
        self._id = uid
        self._level = int(level)

    @property
    def id(self):
        return transform(self._id)

    @property
    def level(self):
        return self._level

    def to_string(self):
        return f"({self.id}, {self.level})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'contributors', 'id', self.id)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection): mdb.add_data(connection, 'contributors', self.to_string())
