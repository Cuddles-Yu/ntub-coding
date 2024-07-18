from 地圖資訊爬蟲.crawler.tables._base import *

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

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'administrators', 'email', self.email)

    def insert_if_not_exists(self, connection):
        if not self.exists(connection): mdb.add(connection, 'administrators', self.to_string())
