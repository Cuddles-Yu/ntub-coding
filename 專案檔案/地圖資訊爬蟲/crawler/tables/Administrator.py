from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

class Administrator:
    _email = ''
    _password = ''

    def __init__(self, email, password):
        self._email = email
        self._password = password

    @property
    def id(self):
        return 'DEFAULT'

    @property
    def email(self):
        return transform(self._email)

    @property
    def password(self):
        return transform(self._password)

    @property
    def create_time(self):
        return 'DEFAULT'

    def to_string(self):
        return f"({self.id}, {self.email}, {self.password}, {self.create_time})"

    def exists(self, database: SqlDatabase) -> bool:
        return database.is_value_exist('administrators', 'email', self.email)

    def insert_if_not_exists(self, database: SqlDatabase):
        if not self.exists(database): database.add('administrators', self.to_string())
