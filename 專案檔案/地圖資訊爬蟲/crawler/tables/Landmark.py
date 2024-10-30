from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

def newObject(name, lng, lat):
    return Landmark(
        category=None,
        name=name,
        longitude=lng,
        latitude=lat
    )

class Landmark:
    _category = ''
    _name = ''
    _longitude = 0.0
    _latitude = 0.0

    def __init__(self, category, name, longitude, latitude):
        self._category = category
        self._name = name
        self._longitude = float(longitude) if longitude else None
        self._latitude = float(latitude) if latitude else None

    @property
    def id(self):
        return 'DEFAULT'

    @property
    def category(self):
        return self._category

    @category.setter
    def category(self, value):
        self._category = value

    @property
    def name(self):
        return self._name

    @name.setter
    def name(self, value):
        self._name = value

    @property
    def longitude(self):
        return self._longitude

    @property
    def latitude(self):
        return self._latitude

    def to_string(self):
        return f"({self.id}, {get(self.category)}, {get(self.name)}, {get(self.longitude)}, {get(self.latitude)})"

    def exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exists('landmarks', name=self.name)

    def insert_if_not_exists(self, database: SqlDatabase):
        if not self.exists(database): database.add('landmarks', self.to_string())
