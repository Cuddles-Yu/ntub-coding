import 地圖結果資料庫.python.modify_database as mdb

# 常數宣告
IMG_URL = 'https://lh5.googleusercontent.com/p/'
MAP_URL = 'https://www.google.com.tw/maps/place/'

def transform(param):
    return f"'{param}'" if param is not None else 'NULL'

class Store:
    _name = ''
    _category = ''
    _tag = ''
    _preview_image = ''
    _link = ''
    _website = ''
    _phone_number = ''

    def __init__(self, name, category, tag, preview_image, link, website, phone_number):
        self._name = name
        self._category = category
        self._tag = tag
        self._preview_image = preview_image
        self._link = link
        self._website = website
        self._phone_number = phone_number

    @property
    def name(self):
        return transform(self._name)

    @property
    def category(self):
        return transform(self._category)

    @property
    def tag(self):
        return transform(self._tag)

    @property
    def preview_image(self):
        return transform(self._preview_image)

    @property
    def link(self):
        return transform(self._link)

    @property
    def website(self):
        return transform(self._website)

    @property
    def phone_number(self):
        return transform(self._phone_number)

    def to_string(self):
        return f"({self.name}, {self.category}, {self.tag}, {self.preview_image}, {self.link}, {self.website}, {self.phone_number})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'stores', 'name', self._name)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection):  mdb.add_data(connection, 'stores', self.to_string())

    def get_link(self) -> str:
        return MAP_URL + self._link

    def get_preview_image(self) -> str:
        return IMG_URL + self._preview_image


class User:
    _id = ''
    _level = 0

    def __init__(self, uid, level):
        self._id = uid
        self._level = int(level)

    @property
    def id(self):
        return transform(self._id)

    @property
    def level(self):
        return self._level

    def to_string(self):
        return f"({self.id}, {self.level})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'users', 'id', self._id)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection): mdb.add_data(connection, 'users', self.to_string())

class Comment:
    _store_name = ''
    _sort = 0
    _contents = ''
    _time = ''
    _rating = 0
    _user_id = ''

    def __init__(self, store_name, sort, contents, time, rating, user_id):
        self._store_name = store_name
        self._sort = int(sort)
        self._contents = contents
        self._time = time
        self._rating = int(rating)
        self._user_id = user_id

    @property
    def store_name(self):
        return transform(self._store_name)

    @property
    def sort(self):
        return self._sort

    @property
    def contents(self):
        return transform(self._contents)

    @property
    def time(self):
        return transform(self._time)

    @property
    def rating(self):
        return self._rating

    @property
    def user_id(self):
        return transform(self._user_id)

    def to_string(self):
        return f"({self.store_name}, {self.sort}, {self.contents}, {self.time}, {self.rating}, {self.user_id})"

    def insert(self, connection):
        mdb.add_data(connection, 'comments', self.to_string())

class Keyword:
    _store_name = ''
    _word = ''
    _count = 0

    def __init__(self, store_name, word, count):
        self._store_name = store_name
        self._word = word
        self._count = int(count)

    @property
    def store_name(self):
        return transform(self._store_name)

    @property
    def word(self):
        return transform(self._word)

    @property
    def count(self):
        return self._count

    def to_string(self):
        return f"({self.store_name}, {self.word}, {self.count})"

    def insert(self, connection):
        mdb.add_data(connection, 'keywords', self.to_string())

class Location:
    _store_name = ''
    _longitude = ''
    _latitude = ''
    _postal_code = ''
    _city = ''
    _dist = ''
    _vil = ''
    _details = ''

    def __init__(self, store_name, longitude, latitude, postal_code, city, dist, vil, details):
        self._store_name = store_name
        self._longitude = longitude
        self._latitude = latitude
        self._postal_code = postal_code
        self._city = city
        self._dist = dist
        self._vil = vil
        self._details = details

    @property
    def store_name(self):
        return transform(self._store_name)

    @property
    def longitude(self):
        return transform(self._longitude)

    @property
    def latitude(self):
        return transform(self._latitude)

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
        return f"({self.store_name}, {self.longitude}, {self.latitude}, {self.postal_code}, {self.city}, {self.dist}, {self.vil}, {self.details})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'locations', 'store_name', self._store_name)

    def insert(self, connection):
        mdb.add_data(connection, 'locations', self.to_string())

class Rate:
    _store_name = ''
    _avg_ratings = 0.0
    _total_ratings = 0
    _total_comments = 0
    _real_ratings = 0.0
    _store_responses = 0

    def __init__(self, store_name, avg_ratings, total_ratings, total_comments, real_ratings, store_responses):
        self._store_name = store_name
        self._avg_ratings = float(avg_ratings)
        self._total_ratings = int(total_ratings)
        self._total_comments = int(total_comments)
        self._real_ratings = float(real_ratings)
        self._store_responses = int(store_responses)

    @property
    def store_name(self):
        return transform(self._store_name)

    @property
    def avg_ratings(self):
        return self._avg_ratings

    @property
    def total_ratings(self):
        return self._total_ratings

    @property
    def total_comments(self):
        return self._total_comments

    @property
    def real_ratings(self):
        return self._real_ratings

    @property
    def store_responses(self):
        return self._store_responses

    def to_string(self):
        return f"({self.store_name}, {self.avg_ratings}, {self.total_ratings}, {self.total_comments}, {self.real_ratings}, {self.store_responses})"

    def insert(self, connection):
        mdb.add_data(connection, 'rates', self.to_string())
