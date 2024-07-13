from 地圖資訊爬蟲.crawler.tables._base import *

class Location:
    _store_id = 0
    _longitude = 0.0
    _latitude = 0.0
    _postal_code = ''
    _city = ''
    _dist = ''
    _vil = ''
    _details = ''

    def __init__(self, store_id, longitude, latitude, postal_code, city, dist, vil, details):
        self._store_id = int(store_id) if store_id else None
        self._longitude = float(longitude) if longitude else None
        self._latitude = float(latitude) if latitude else None
        self._postal_code = postal_code
        self._city = city
        self._dist = dist
        self._vil = vil
        self._details = details

    @property
    def store_id(self):
        return self._store_id

    @property
    def longitude(self):
        return self._longitude

    @property
    def latitude(self):
        return self._latitude

    @property
    def postal_code(self):
        return transform(self._postal_code)

    @property
    def city(self):
        return transform(self._city)

    @property
    def dist(self):
        return transform(self._dist)

    @property
    def vil(self):
        return transform(self._vil)

    @property
    def details(self):
        return transform(self._details)

    def to_string(self):
        return f"({self.store_id}, {self.longitude}, {self.latitude}, {self.postal_code}, {self.city}, {self.dist}, {self.vil}, {self.details})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'locations', 'store_id', self.store_id)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection): mdb.add_data(connection, 'locations', self.to_string())
