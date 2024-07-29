from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

class Comment:
    _store_id = 0
    _index = 0
    _contents = ''
    _has_image = 0
    _time = ''
    _rating = 0
    _food_rating = 0
    _service_rating = 0
    _atmosphere_rating = 0
    _contributor_level = 0
    _environment_state = ''
    _price_state = ''
    _product_state = ''
    _service_state = ''

    def __init__(self, store_id, index, contents, has_image, time, rating, food_rating, service_rating, atmosphere_rating, contributor_level, environment_state, price_state, product_state, service_state):
        self._store_id = int(store_id) if store_id else None
        self._index = int(index)
        self._contents = contents
        self._has_image = int(has_image)
        self._time = time
        self._rating = int(rating)
        self._food_rating = int(food_rating) if food_rating else None
        self._service_rating = int(service_rating) if service_rating else None
        self._atmosphere_rating = int(atmosphere_rating) if atmosphere_rating else None
        self._contributor_level = int(contributor_level)
        self._environment_state = environment_state
        self._price_state = price_state
        self._product_state = product_state
        self._service_state = service_state

    @property
    def store_id(self):
        return self._store_id

    @property
    def index(self):
        return self._index

    @property
    def contents(self):
        return transform(escape_quotes(self._contents))

    @property
    def has_image(self):
        return transform(self._has_image)

    @property
    def time(self):
        return transform(self._time)

    @property
    def rating(self):
        return self._rating

    @property
    def food_rating(self):
        return transform(self._food_rating)

    @property
    def service_rating(self):
        return transform(self._service_rating)

    @property
    def atmosphere_rating(self):
        return transform(self._atmosphere_rating)

    @property
    def contributor_level(self):
        return self._contributor_level

    @property
    def environment_state(self):
        return transform(self._environment_state)

    @property
    def price_state(self):
        return transform(self._price_state)

    @property
    def product_state(self):
        return transform(self._product_state)

    @property
    def service_state(self):
        return transform(self._service_state)

    def to_string(self):
        return f"({self.store_id}, {self.index}, {self.contents}, {self.has_image}, {self.time}, {self.rating}, {self.food_rating}, {self.service_rating}, {self.atmosphere_rating}, {self.contributor_level}, {self.environment_state}, {self.price_state}, {self.product_state}, {self.service_state})"

    def insert(self, database: SqlDatabase):
        database.add('comments', self.to_string())
