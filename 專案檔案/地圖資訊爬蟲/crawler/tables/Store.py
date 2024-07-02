from 地圖資訊爬蟲.crawler.tables._base import *

class Store:
    _name = ''
    _description = ''
    _tag = ''
    _preview_image = ''
    _link = ''
    _website = ''
    _phone_number = ''

    def __init__(self, name, description, tag, preview_image, link, website, phone_number):
        self._name = name
        self._description = description
        self._tag = tag
        self._preview_image = preview_image
        self._link = link
        self._website = website
        self._phone_number = phone_number

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
    def time(self):
        return 'DEFAULT'

    def to_string(self):
        return f"({self.id}, {self.name}, {self.description}, {self.tag}, {self.preview_image}, {self.link}, {self.website}, {self.phone_number}, {self.time})"

    def get_id(self, connection):
        return mdb.get_value(connection, 'id', 'stores', 'name', self.name)

    def exists(self, connection) -> bool:
        return mdb.is_value_exist(connection, 'stores', 'name', self.name)

    def check_if_perfect(self, connection, comments_count: int) -> bool:
        if not mdb.is_value_exist(connection, 'stores', 'name', self.name): return False
        store_id = mdb.get_value(connection, 'id', 'stores', 'name', self.name)

        return (
            int(mdb.get_value(connection, 'total_ratings', 'rates', 'store_id', store_id)) == comments_count and
            (
                int(mdb.get_value(connection, 'total_ratings', 'rates', 'store_id', store_id))
                ==
                int(mdb.get_value(connection, 'sample_ratings', 'rates', 'store_id', store_id))
            )
        )

    def check_if_sample(self, connection, max_samples: int) -> str:
        if not mdb.is_value_exist(connection, 'stores', 'name', self.name): return 'not_exists'
        store_id = mdb.get_value(connection, 'id', 'stores', 'name', self.name)
        if not mdb.is_value_exist(connection, 'rates', 'store_id', store_id): return 'ref_location'

        s = mdb.get_value(connection, 'sample_ratings', 'rates', 'store_id', store_id)
        t = mdb.get_value(connection, 'total_ratings', 'rates', 'store_id', store_id)
        samples = int(s) if t else 0
        total = int(t) if t else 0
        if (
            mdb.is_value_exist(connection, 'locations', 'store_id', store_id) and
            int(samples) >= max_samples or int(samples) == int(total)
        ): return 'is_exists'
        else: return 'need_repair'

    def insert_if_not_exists(self, connection):
        if not self.exists(connection):  mdb.add_data(connection, 'stores', self.to_string())


class Reference(Store):
    def __init__(self, name):
        super().__init__(name, None, None, None, None, None, None)
