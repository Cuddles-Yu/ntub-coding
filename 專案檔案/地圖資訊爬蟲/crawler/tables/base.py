# 常數宣告
MAP_URL = 'https://www.google.com.tw/maps/place/+/'

def transform(param: str) -> str:
    if param == 'DEFAULT': return param
    return f"'{param}'" if param is not None else 'NULL'

def escape_quotes(param: str) -> str:
    if param is None: return None
    return param.replace('\\', '\\\\').replace('"', '\\"').replace("'", "\\'")

def to_map_url(urls: list) -> list:
    return [MAP_URL + url.split('/')[-1].split('?')[0] for url in urls]
