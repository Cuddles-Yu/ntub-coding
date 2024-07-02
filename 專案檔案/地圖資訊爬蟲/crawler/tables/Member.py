from 地圖資訊爬蟲.crawler.tables._base import *

class Member:
    _email = ''
    _username = ''
    _password = ''
    _profile_picture = ''
    _popular_weight = 0
    _environment_weight = 0
    _price_weight = 0
    _product_weight = 0
    _service_weight = 0

    def __init__(self, email, username, password, profile_picture, popular_weight, environment_weight, price_weight, product_weight, service_weight):
        self._email = email
        self._username = username
        self._password = password
        self._profile_picture = profile_picture
        self._popular_weight = int(popular_weight)
        self._environment_weight = int(environment_weight)
        self._price_weight = int(price_weight)
        self._product_weight = int(product_weight)
        self._service_weight = int(service_weight)

    @property
    def id(self):
        return 'DEFAULT'

    @property
    def email(self):
        return transform(self._email)

    @property
    def username(self):
        return transform(self._username)

    @property
    def password(self):
        return transform(self._password)

    @property
    def profile_picture(self):
        return transform(self._profile_picture)

    @property
    def popular_weight(self):
        return self._popular_weight

    @property
    def environment_weight(self):
        return self._environment_weight

    @property
    def price_weight(self):
        return self._price_weight

    @property
    def product_weight(self):
        return self._product_weight

    @property
    def service_weight(self):
        return self._service_weight

    @property
    def create_time(self):
        return 'DEFAULT'

    def to_string(self):
        return f"({self.id}, {self.email}, {self.username}, {self.password}, {self.profile_picture}, {self.popular_weight}, {self.environment_weight}, {self.price_weight}, {self.product_weight}, {self.service_weight}, {self.create_time})"

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'members', 'email', self.email)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection): mdb.add_data(connection, 'members', self.to_string())
