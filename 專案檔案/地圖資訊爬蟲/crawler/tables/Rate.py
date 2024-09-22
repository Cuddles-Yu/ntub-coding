from typing import Optional
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

def newObject():
    return Rate(
        store_id=None,
        avg_rating=0.0,
        total_reviews=0,
        total_browses=0,
        total_samples=0,
        total_withcomments=0,
        total_withoutcomments=0,
        mixreviews_count=0,
        additionalcomments_count=0,
        real_rating=0.0,
        store_responses=0
    )

class Rate:
    _store_id = 0
    _avg_rating = 0.0
    _total_reviews = 0
    _total_browses = 0
    _total_samples = 0
    _total_withcomments = 0
    _total_withoutcomments = 0
    _mixreviews_count = 0
    _additionalcomments_count = 0
    _real_rating = 0.0
    _store_responses = 0

    def __init__(self, store_id, avg_rating, total_reviews, total_browses, total_samples, total_withcomments,
                 total_withoutcomments, mixreviews_count, additionalcomments_count, real_rating, environment_rating,
                 price_rating, product_rating, service_rating, store_responses):
        self._store_id = int(store_id) if store_id else None
        self._avg_rating = float(avg_rating)
        self._total_reviews = int(total_reviews)
        self._total_browses = int(total_browses)
        self._total_samples = int(total_samples)
        self._total_withcomments = int(total_withcomments)
        self._total_withoutcomments = int(total_withoutcomments)
        self._mixreviews_count = int(mixreviews_count)
        self._additionalcomments_count = int(additionalcomments_count)
        self._real_rating = float(real_rating)
        self._store_responses = int(store_responses)

    @property
    def store_id(self):
        return self._store_id

    @store_id.setter
    def store_id(self, value):
        self._store_id = value

    @property
    def avg_rating(self):
        return self._avg_rating

    @avg_rating.setter
    def avg_rating(self, value):
        self._avg_rating = value

    @property
    def total_reviews(self):
        return self._total_reviews

    @total_reviews.setter
    def total_reviews(self, value):
        self._total_reviews = value

    @property
    def total_browses(self):
        return self._total_browses

    @total_browses.setter
    def total_browses(self, value):
        self._total_browses = value

    @property
    def total_samples(self):
        return self._total_samples

    @total_samples.setter
    def total_samples(self, value):
        self._total_samples = value

    @property
    def total_withcomments(self):
        return self._total_withcomments

    @total_withcomments.setter
    def total_withcomments(self, value):
        self._total_withcomments = value

    @property
    def total_withoutcomments(self):
        return self._total_withoutcomments

    @total_withoutcomments.setter
    def total_withoutcomments(self, value):
        self._total_withoutcomments = value

    @property
    def mixreviews_count(self):
        return self._mixreviews_count

    @mixreviews_count.setter
    def mixreviews_count(self, value):
        self._mixreviews_count = value

    @property
    def additionalcomments_count(self):
        return self._additionalcomments_count

    @additionalcomments_count.setter
    def additionalcomments_count(self, value):
        self._additionalcomments_count = value

    @property
    def real_rating(self):
        return self._real_rating

    @real_rating.setter
    def real_rating(self, value):
        self._real_rating = value

    @property
    def store_responses(self):
        return self._store_responses

    @store_responses.setter
    def store_responses(self, value):
        self._store_responses = value

    def to_string(self):
        return f"({self.store_id}, {self.avg_rating}, {self.total_reviews}, {self.total_browses}, {self.total_samples}, {self.total_withcomments}, {self.total_withoutcomments}, {self.mixreviews_count}, {self.additionalcomments_count}, {self.real_rating}, {self.store_responses})"

    def exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exists('rates', store_id=self.store_id)

    def insert_if_not_exists(self, database: SqlDatabase):
        if not self.exists(database): database.add('rates', self.to_string())

    def newObject(self):
        return Rate(
            store_id=self._store_id,
            avg_rating=self._avg_rating,
            total_reviews=self._total_reviews,
            total_browses=self._total_browses,
            total_samples=self._total_samples,
            total_withcomments=self._total_withcomments,
            total_withoutcomments=self._total_withoutcomments,
            mixreviews_count=self._mixreviews_count,
            additionalcomments_count=self._additionalcomments_count,
            real_rating=self._real_rating,
            store_responses=self._store_responses
        )