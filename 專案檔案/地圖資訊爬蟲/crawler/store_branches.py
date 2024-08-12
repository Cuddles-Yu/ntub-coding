import time
from datetime import datetime
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions.EdgeDriver import EdgeDriver
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from 地圖資訊爬蟲.crawler.tables import Store

MODE = ['擴展', '分店']

def get_store_branch_title(_store_name):
    _title, _name = None, None
    if not re.findall(r'[^酒的關飯]店', _store_name): return None, None
    if re.findall(SEPARATOR_PATTERN, _store_name):
        pattern = re.compile(
            r'(?P<branch>[^\s\-—–_（）(){}]*' + IGNORE_PATTERN + '|[總分]店)'
        )
        match = pattern.search(_store_name)
        if match:
            _name = re.sub(SYMBOL_PATTERN, '', match.group('branch')).strip()
            if len(_name) > 1:
                store_group = _store_name[:match.start('branch')]
                if re.findall(SEPARATOR_PATTERN, store_group):
                    last_match = list(re.finditer(fr'(?P<separator>{SEPARATOR_PATTERN})', store_group))[-1]
                    _title = store_group[:last_match.start('separator')].strip()
                else:
                    _title = store_group.strip()
                _title = re.sub(SYMBOL_PATTERN, '', _title).strip()
    else:
        pattern = re.compile(
            fr'(?P<branch>..{IGNORE_PATTERN}|[總分]店)'
        )
        match = pattern.search(_store_name)
        if match:
            _name = match.group('branch').strip()
            _title = re.sub(SYMBOL_PATTERN, '', _store_name.split(_name)[0].strip()).strip()
    if not _title or not _name or len(_title) <= 1: return None, None
    return _title, _name

if __name__ == '__main__':
    current_mode = MODE[1]
    database = SqlDatabase('mapdb', 'root', '11236018')
    driver = EdgeDriver(database)
    branches_state = load_json('BRANCHES_INDEX.json')
    branches_index = branches_state.get(current_mode).get(database.name, 0)
    match current_mode:
        case '擴展':
            old_urls = [url[0] for url in database.fetch('all', '''
                SELECT link FROM stores
                ORDER BY id
            ''')]
            print(f'正在執行擴增商家 -> {database.name}\n')
            START_TIME = datetime.now()

            for i in range(branches_index, len(old_urls)):
                print(f'\r正在取得商家週邊的其他商家({i+1}/{len(old_urls)})...\n', end='')
                driver.get(old_urls[i])
                urls, store_names = driver.search_and_save_results('餐廳')
                branches_state[current_mode][database.name] = i+1
                write_json(branches_state, 'BRANCHES_INDEX.json')
                time.sleep(1)
        case '分店':
            print(f'正在查詢商家分店 -> {database.name}\n')
            START_TIME = datetime.now()

            SEPARATOR_PATTERN = r'\s*[\s\-—–_（）(){}]+\s*'
            SYMBOL_PATTERN = r'[【】（）()｜{}]*'
            IGNORE_PATTERN = r'(?<!專門|專賣|小吃|大飯|涼麵|點心|精肉|司總|料理|骨總|利麵|美食|1號|固定|餐館|星來)店'
            results = database.fetch('all', f'''
                SELECT id, name, link FROM stores
                WHERE crawler_description IS NOT NULL and branch_title IS NULL and name LIKE '%店%' and id >= {branches_index}
                ORDER BY id
            ''')
            for sid, name, link in results:
                has_branches = False
                branch_title, branch_name = get_store_branch_title(name)
                if not branch_title: continue
                print(f"\r正在取得'{branch_title}({branch_name})'的其他分店...\n", end='')
                driver.get(link)
                urls, store_names = driver.search_and_scroll(branch_title, show_hint=False)
                if urls and store_names:
                    for url, store_name in zip(urls, store_names):
                        if branch_title in store_name:
                            if not has_branches: has_branches = True
                            new_branch_title, new_branch_name = get_store_branch_title(store_name)
                            store = Store.newObject(store_name, url, branch_title=branch_title, branch_name=new_branch_name)
                            if store.exists(database):
                                store.change_branch(database, branch_title, branch_name)
                                print(f'✴️已存在分店【{store_name}】')
                            else:
                                store.insert_if_not_exists(database)
                                print(f'✳️已建立分店【{store_name}】')
                        else:
                            print(f'⛔不屬於分店【{store_name}】')
                print()
                if has_branches: Store.newObject(name, link).change_branch(database, branch_title, branch_name)
                branches_state[current_mode][database.name] = sid+1
                write_json(branches_state, 'BRANCHES_INDEX.json')
                time.sleep(1)

    # 計算時間差
    TIME_DIFFERENCE = datetime.now() - START_TIME
    MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60
    print(f'\r【✅已完成】耗時:{MINUTES_ELAPSE:.2f}分鐘', end='')
    driver.exit()
