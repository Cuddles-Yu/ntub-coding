from 地圖資訊爬蟲.crawler.tables._base import *

class Keyword:
    _store_id = 0
    _word = ''
    _count = 0
    _source = ''

    def __init__(self, store_id, word, count, source):
        self._store_id = int(store_id) if store_id else None
        self._word = word
        self._count = int(count)
        self._source = source

    @property
    def store_id(self):
        return self._store_id

    @property
    def word(self):
        return transform(self._word)

    @property
    def count(self):
        return self._count

    @property
    def source(self):
        return transform(self._source)

    def to_string(self):
        return f"({self.store_id}, {self.word}, {self.count}, {self.source})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'keywords', 'word', self.word)

    def insert_if_not_exists(self, connection):
        if self._word.strip() == '': return
        if not self.exists(connection): mdb.add_data(connection, 'keywords', self.to_string())
