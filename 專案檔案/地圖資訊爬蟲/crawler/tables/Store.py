from 地圖資訊爬蟲.crawler.tables._base import *

class Store:
    _name = ''
    _description = ''
    _tag = ''
    _preview_image = ''
    _link = ''
    _website = ''
    _phone_number = ''
    _last_update = ''
    _crawler_state = ''
    _crawler_description = ''

    def __init__(self, name, description, tag, preview_image, link, website, phone_number, last_update, crawler_state, crawler_description):
        self._name = name
        self._description = description
        self._tag = tag
        self._preview_image = preview_image
        self._link = link
        self._website = website
        self._phone_number = phone_number
        self._last_update = last_update
        self._crawler_state = crawler_state
        self._crawler_description = crawler_description

    @property
    def id(self):
        return 'DEFAULT'

    @property
    def name(self):
        return transform(escape_quotes(self._name))

    @property
    def description(self):
        return transform(self._description)

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

    @property
    def last_update(self):
        return transform(self._last_update)

    @property
    def crawler_state(self):
        return transform(self._crawler_state)

    @property
    def crawler_description(self):
        return transform(self._crawler_state)

    @property
    def crawler_time(self):
        return 'DEFAULT'

    def to_string(self):
        return f"({self.id}, {self.name}, {self.description}, {self.tag}, {self.preview_image}, {self.link}, {self.website}, {self.phone_number}, {self.last_update}, {self.crawler_state}, {self.crawler_description}, {self.crawler_time})"

    def get_id(self, connection):
        return mdb.get_value(connection, 'id', 'stores', 'name', self.name)

    def get_tag(self):
        return self._tag

    def change_state(self, connection, state, description) -> bool:
        if not self.exists(connection): return False
        mdb.update(connection, 'stores', f'crawler_state={transform(state)}, crawler_description={transform(description)}', f'name={self.name}')
        return True

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'stores', 'name', self.name)

    def get_state(self, connection) -> (str, str):
        return (
            mdb.get_value(connection, 'crawler_state', 'stores', 'name', self.name),
            mdb.get_value(connection, 'crawler_description', 'stores', 'name', self.name)
        )

    def insert_if_not_exists(self, connection):
        if not self.exists(connection):  mdb.add_data(connection, 'stores', self.to_string())


class Reference(Store):
    def __init__(self, name):
        super().__init__(name, None, None, None, None, None, None, None, None, None)
