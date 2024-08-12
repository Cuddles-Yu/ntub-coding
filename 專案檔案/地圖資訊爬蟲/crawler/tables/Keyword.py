from 地圖資訊爬蟲.crawler.tables.base import *
import 地圖資訊爬蟲.crawler.keyword_image as ki
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

class Keyword:
    _store_id = 0
    _word = ''
    _count = 0
    _source = ''
    _image_url = ''
    _source_url = ''

    def __init__(self, store_id, word, count, source, image_url, source_url):
        self._store_id = int(store_id) if store_id else None
        self._word = word
        self._count = int(count)
        self._source = source
        self._image_url = image_url
        self._source_url = source_url

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

    @property
    def image_url(self):
        return transform(self._image_url)

    @property
    def source_url(self):
        return transform(self._source_url)

    def to_string(self):
        return f"({self.store_id}, {self.word}, {self.count}, {self.source}, {self.image_url}, {self.source_url})"

    def exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exists('keywords', store_id=self.store_id, word=self.word)

    def is_recommend(self):
        return self._source == 'recommend'

    def insert_after_search(self, driver, database: SqlDatabase, store_name):
        image_url, source_url = ki.search(driver, store_name, self._word, self._store_id)
        self._image_url = image_url
        self._source_url = source_url
        self.insert_if_not_exists(database)

    def insert_if_not_exists(self, database: SqlDatabase):
        if self._word.strip() == '': return
        if not self.exists(database): database.add('keywords', self.to_string())
