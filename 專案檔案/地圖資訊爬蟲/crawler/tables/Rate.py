from 地圖資訊爬蟲.crawler.tables._base import *

class Rate:
    _store_id = 0
    _avg_ratings = 0.0
    _total_ratings = 0
    _sample_ratings = 0
    _total_comments = 0
    _real_ratings = 0.0
    _environment_rating = 0.0
    _price_rating = 0.0
    _product_rating = 0.0
    _service_rating = 0.0
    _store_responses = 0

    def __init__(self, store_id, avg_ratings, total_ratings, sample_ratings, total_comments, real_ratings, environment_rating, price_rating, product_rating, service_rating, store_responses):
        self._store_id = int(store_id) if store_id else None
        self._avg_ratings = float(avg_ratings)
        self._total_ratings = int(total_ratings)
        self._sample_ratings = int(sample_ratings)
        self._total_comments = int(total_comments)
        self._real_ratings = float(real_ratings)
        self._environment_rating = float(environment_rating)
        self._price_rating = float(price_rating)
        self._product_rating = float(product_rating)
        self._service_rating = float(service_rating)
        self._store_responses = int(store_responses)

    @property
    def store_id(self):
        return self._store_id

    @property
    def avg_ratings(self):
        return self._avg_ratings

    @property
    def total_ratings(self):
        return self._total_ratings

    @property
    def sample_ratings(self):
        return self._sample_ratings

    @property
    def total_comments(self):
        return self._total_comments

    @property
    def real_ratings(self):
        return self._real_ratings

    @property
    def environment_rating(self):
        return self._environment_rating

    @property
    def price_rating(self):
        return self._price_rating

    @property
    def product_rating(self):
        return self._product_rating

    @property
    def service_rating(self):
        return self._service_rating

    @property
    def store_responses(self):
        return self._store_responses

    def to_string(self):
        return f"({self.store_id}, {self.avg_ratings}, {self.total_ratings}, {self.sample_ratings}, {self.total_comments}, {self.real_ratings}, {self.environment_rating}, {self.price_rating}, {self.product_rating}, {self.service_rating}, {self.store_responses})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'rates', 'store_id', self.store_id)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection): mdb.add_data(connection, 'rates', self.to_string())
