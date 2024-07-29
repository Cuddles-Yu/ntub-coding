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
        environment_rating=0.0,
        price_rating=0.0,
        product_rating=0.0,
        service_rating=0.0,
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
    _environment_rating = 0.0
    _price_rating = 0.0
    _product_rating = 0.0
    _service_rating = 0.0
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
        self._environment_rating = float(environment_rating)
        self._price_rating = float(price_rating)
        self._product_rating = float(product_rating)
        self._service_rating = float(service_rating)
        self._store_responses = int(store_responses)

    @property
    def store_id(self):
        return self._store_id

    @property
    def avg_rating(self):
        return self._avg_rating

    @property
    def total_reviews(self):
        return self._total_reviews

    @property
    def total_browses(self):
        return self._total_browses

    @property
    def total_samples(self):
        return self._total_samples

    @property
    def total_withcomments(self):
        return self._total_withcomments

    @property
    def total_withoutcomments(self):
        return self._total_withoutcomments

    @property
    def mixreviews_count(self):
        return self._mixreviews_count

    @property
    def additionalcomments_count(self):
        return self._additionalcomments_count

    @property
    def real_rating(self):
        return self._real_rating

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
        return f"({self.store_id}, {self.avg_rating}, {self.total_reviews}, {self.total_browses}, {self.total_samples}, {self.total_withcomments}, {self.total_withoutcomments}, {self.mixreviews_count}, {self.additionalcomments_count}, {self.real_rating}, {self.environment_rating}, {self.price_rating}, {self.product_rating}, {self.service_rating}, {self.store_responses})"

    def exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exist('rates', 'store_id', self.store_id)

    def insert_if_not_exists(self, database: SqlDatabase):
        if not self.exists(database): database.add('rates', self.to_string())
