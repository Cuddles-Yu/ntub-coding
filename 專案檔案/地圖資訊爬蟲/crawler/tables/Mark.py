from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

class Mark:
    _store_id = 0
    _comment_id = 0
    _id = 0
    _object = ''
    _adjective = ''
    _target = ''
    _state = ''

    def __init__(self, store_id, comment_id, sid, obj, adjective, target, state):
        self._store_id = int(store_id) if store_id else None
        self._comment_id = int(comment_id) if comment_id else None
        self._id = int(sid) if sid else None
        self._object = obj
        self._adjective = adjective
        self._target = target
        self._state = state

    @property
    def store_id(self):
        return self._store_id

    @property
    def comment_id(self):
        return self._comment_id

    @property
    def id(self):
        return self._id

    @property
    def object(self):
        return transform(self._object)

    @property
    def adjective(self):
        return transform(self._adjective)

    @property
    def target(self):
        return transform(self._target)

    @property
    def state(self):
        return transform(self._state)

    def to_string(self):
        return f"({self.store_id}, {self.comment_id}, {self.id}, {self.object}, {self.adjective}, {self.target}, {self.state})"

    def insert(self, database: SqlDatabase):
        database.add('marks', self.to_string())
