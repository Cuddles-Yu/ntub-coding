from 地圖資訊爬蟲.crawler.tables._base import *

class Comment:
    _store_id = 0
    _sort = 0
    _contents = ''
    _time = ''
    _rating = 0
    _contributor_id = ''

    def __init__(self, store_id, sort, contents, time, rating, contributor_id):
        self._store_id = int(store_id) if store_id else None
        self._sort = int(sort)
        self._contents = contents
        self._time = time
        self._rating = int(rating)
        self._contributor_id = contributor_id

    @property
    def store_id(self):
        return self._store_id

    @property
    def sort(self):
        return self._sort

    @property
    def contents(self):
        return transform(escape_quotes(self._contents))

    @property
    def time(self):
        return transform(self._time)

    @property
    def rating(self):
        return self._rating

    @property
    def contributor_id(self):
        return transform(self._contributor_id)

    def to_string(self):
        return f"({self.store_id}, {self.sort}, {self.contents}, {self.time}, {self.rating}, {self.contributor_id})"

    def insert(self, connection):
        mdb.add_data(connection, 'comments', self.to_string())
