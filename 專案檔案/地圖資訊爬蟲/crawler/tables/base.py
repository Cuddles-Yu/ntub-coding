# 常數宣告
MAP_URL = 'https://www.google.com.tw/maps/place/+/'

def transform(param) -> str:
    if param is None: return 'NULL'
    if not isinstance(param, str): return param
    param = str(param)
    if param == 'DEFAULT': return param
    if param.startswith('"') and param.endswith('"'): return param
    if param.startswith("'") and param.endswith("'"): return param
    return f"'{param}'"

def escape_quotes(param) -> str:
    if param is None: return None
    if not isinstance(param, str): return param
    return param.replace('\\', '\\\\').replace('"', '\\"').replace("'", "\\'")

def to_map_url(urls: list) -> list:
    return [MAP_URL + url.split('/')[-1].split('?')[0] for url in urls]
