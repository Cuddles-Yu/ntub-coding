from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

class Comment:
    _store_id = 0
    _index = 0
    _data_id = ''
    _contents = ''
    _time = ''
    _rating = 0
    _has_image = 0
    _food_rating = 0
    _service_rating = 0
    _atmosphere_rating = 0
    _contributor_level = 0
    _environment_state = ''
    _price_state = ''
    _product_state = ''
    _service_state = ''
    _sample_of_most_relevant = 0
    _sample_of_highest_rating = 0
    _sample_of_lowest_rating = 0

    def __init__(self, store_id, index, data_id, contents, time, rating, has_image,
                 food_rating, service_rating, atmosphere_rating, contributor_level, environment_state, price_state, product_state, service_state,
                 sample_of_most_relevant, sample_of_highest_rating, sample_of_lowest_rating):
        self._store_id = int(store_id) if store_id else None
        self._index = int(index)
        self._data_id = data_id
        self._contents = contents
        self._time = time
        self._rating = int(rating)
        self._has_image = int(has_image)
        self._food_rating = int(food_rating) if food_rating else None
        self._service_rating = int(service_rating) if service_rating else None
        self._atmosphere_rating = int(atmosphere_rating) if atmosphere_rating else None
        self._contributor_level = int(contributor_level)
        self._environment_state = environment_state
        self._price_state = price_state
        self._product_state = product_state
        self._service_state = service_state
        self._sample_of_most_relevant = int(sample_of_most_relevant)
        self._sample_of_highest_rating = int(sample_of_highest_rating)
        self._sample_of_lowest_rating = int(sample_of_lowest_rating)

    @property
    def store_id(self):
        return self._store_id

    @property
    def index(self):
        return self._index

    @property
    def data_id(self):
        return transform(self._data_id)

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
    def has_image(self):
        return self._has_image

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

    @property
    def sample_of_most_relevant(self):
        return self._sample_of_most_relevant

    @property
    def sample_of_highest_rating(self):
        return self._sample_of_highest_rating

    @property
    def sample_of_lowest_rating(self):
        return self._sample_of_lowest_rating

    def to_string(self):
        return (f"({self.store_id}, {self.index}, {self.data_id}, {self.contents}, {self.time}, {self.rating}, {self.has_image}, " +
                f"{self.food_rating}, {self.service_rating}, {self.atmosphere_rating}, {self.contributor_level}, {self.environment_state}, {self.price_state}, {self.product_state}, {self.service_state}, " +
                f"{self.sample_of_most_relevant}, {self.sample_of_highest_rating}, {self.sample_of_lowest_rating})")

    def exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exist('comments', 'data_id', self.data_id)

    def update_if_exists(self, database: SqlDatabase):
        if not self.exists(database):
            database.add('comments', self.to_string())
