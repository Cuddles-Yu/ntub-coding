from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

class Mark:
    _store_id = 0
    _comment_id = 0
    _id = 0
    _contents = ''
    _target = ''
    _state = ''
    _count = 1

    def __init__(self, store_id, comment_id, sid, contents, target, state, count):
        self._store_id = int(store_id) if store_id else None
        self._comment_id = int(comment_id) if comment_id else None
        self._id = int(sid) if sid else None
        self._contents = contents
        self._target = target
        self._state = state
        self._count = int(count) if count else None

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
    def contents(self):
        return transform(self._contents)

    @property
    def target(self):
        return transform(self._target)

    @property
    def state(self):
        return transform(self._state)

    @property
    def count(self):
        return self._count

    def to_string(self):
        return f"({self.store_id}, {self.comment_id}, {self.id}, {self.contents}, {self.target}, {self.state}, {self.count})"

    def insert(self, database: SqlDatabase):
        database.add('marks', self.to_string())
