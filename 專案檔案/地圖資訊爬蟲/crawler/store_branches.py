from datetime import datetime
from enum import Enum
from 地圖資訊爬蟲.crawler.tables.base import *
from 地圖資訊爬蟲.crawler.module.functions.common import *
from 地圖資訊爬蟲.crawler.module.functions.EdgeDriver import EdgeDriver
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase
from 地圖資訊爬蟲.crawler.tables import Store

class Mode(Enum):
    expand_nearby = {'名稱': '周邊擴展', '瀏覽': True, '隨機': True}
    search_branches = {'名稱': '分店查詢', '瀏覽': True, '隨機': False}
    refresh_branches = {'名稱': '分店更新', '瀏覽': False}
    remove_branches = {'名稱': '移除分店', '瀏覽': False}

INDEX_FILE = 'doc/BRANCHES_INDEX.json'
CURRENT_MODE = Mode.remove_branches

PERMITTED_STATE = "crawler_state IN ('成功', '完成', '超時', '基本', '建立')"
QUALIFIED_STATE = "crawler_state IN ('成功', '完成', '超時', '基本')"

EXCEPTED_STORE = ['義樂麵屋']

if __name__ == '__main__':
    CURRENT_MODE_NAME = CURRENT_MODE.value.get('名稱')
    CURRENT_MODE_BROWSE = CURRENT_MODE.value.get('瀏覽')
    RANDOM_MODE = CURRENT_MODE.value.get('隨機', False)

    database = SqlDatabase('mapdb', 'root', '11236018')
    if CURRENT_MODE_BROWSE:
        driver = EdgeDriver(database)
        branches_state = load_json(INDEX_FILE)
        branches_index = branches_state.get(CURRENT_MODE_NAME).get(database.name, 0) if not RANDOM_MODE else 0
    START_TIME = datetime.now()
    if RANDOM_MODE: print(f'隨機{CURRENT_MODE_NAME}模式 -> 已開啟')
    print(f'正在執行{CURRENT_MODE_NAME} -> {database.name}\n')

    match CURRENT_MODE:

        case Mode.remove_branches:
            target_stores = database.fetch_column('all', 0, f'''
                SELECT branch_title FROM (
                    SELECT branch_title, COUNT(*) AS c FROM stores
                    WHERE branch_title IS NOT NULL and {PERMITTED_STATE}
                    GROUP BY branch_title
                    ORDER BY c DESC) AS sub
                WHERE c = 1
            ''')
            if target_stores:
                for target_store in target_stores:
                    stores = database.fetch('all', f'''
                        SELECT id, name, link FROM stores
                        WHERE branch_title = {transform(escape_quotes(target_store))}
                        ORDER BY id
                    ''')
                    for sid, name, link in stores:
                        Store.newObject(name, link).change_branch(database, title=None, name=None)
                        print(f'❌已移除分店【{name}】')
                print()
            else:
                print(f'未找到資料庫中不存在其他分店的商家。')
                exit()

        case Mode.expand_nearby:
            if RANDOM_MODE:
                old_urls = database.fetch('all', f'''
                    SELECT id, name, link FROM stores
                    WHERE {QUALIFIED_STATE}
                    ORDER BY id
                ''')
                shuffle(old_urls)
            else:
                old_urls = database.fetch('all', f'''
                    SELECT id, name, link FROM stores
                    WHERE id >= {branches_index} AND {QUALIFIED_STATE}
                    ORDER BY id
                ''')
            for sid, name, link in old_urls:
                print(f"\r正在取得商家'{name}'週邊的其他商家...\n", end='')
                driver.get(link)
                driver.search_and_save_results('餐廳')
                if not RANDOM_MODE:
                    branches_state[CURRENT_MODE_NAME][database.name] = sid + 1
                    write_json(branches_state, INDEX_FILE)
                time.sleep(1)

        case Mode.refresh_branches:
            has_branches = False
            stores = database.fetch('all', f'''
                SELECT name, link FROM stores
                WHERE branch_title IS NULL and name NOT LIKE '%店%' AND {QUALIFIED_STATE}
            ''')
            branch_stores = database.fetch('all', f'''
                SELECT name, link FROM stores
                WHERE branch_title IS NULL AND name LIKE '%店%' AND {QUALIFIED_STATE}
            ''')
            branch_titles = database.fetch_column('all', 0, f'''
                SELECT DISTINCT branch_title FROM stores
                WHERE branch_title IS NOT NULL and branch_title != ''
            ''')

            if branch_stores:
                for name, link in branch_stores:
                    for branch_title in branch_titles:
                        if normalize_branch_title(branch_title) in normalize_branch_title(name):
                            new_branch_title, new_branch_name = get_store_branch_title(name)
                            Store.newObject(name, link).change_branch(database, title=branch_title, name=new_branch_name)
                            has_branches = True
                            if new_branch_name:
                                print(f'✳️已建立分店【{name}】')
                            else:
                                print(f'☑️已設定分店【{name}】')
                            break
            if stores:
                for name, link in stores:
                    if name in EXCEPTED_STORE: continue
                    for branch_title in branch_titles:
                        if normalize_branch_title(branch_title) in normalize_branch_title(name):
                            Store.newObject(name, link).change_branch(database, title=branch_title, name=None)
                            has_branches = True
                            print(f'☑️已設定分店【{name}】')
                            break
            if has_branches:
                print()
            else:
                print(f'未找到尚未建立或設定分店的商家。')
                exit()

        case Mode.search_branches:
            if RANDOM_MODE:
                stores = database.fetch('all', f'''
                    SELECT id, name, link FROM stores
                    WHERE branch_title IS NULL AND name LIKE '%店%' AND {QUALIFIED_STATE}
                    ORDER BY id
                ''')
                shuffle(stores)
            else:
                stores = database.fetch('all', f'''
                    SELECT id, name, link FROM stores
                    WHERE branch_title IS NULL AND name LIKE '%店%' AND id >= {branches_index} AND {QUALIFIED_STATE}
                    ORDER BY id
                ''')
            for sid, name, link in stores:
                has_branches = False
                branch_title, branch_name = get_store_branch_title(name)
                if not branch_title: continue
                print(f"\r正在取得'{branch_title}({branch_name})'的其他分店...\n", end='')
                driver.get(link)
                urls, store_names = driver.search_and_scroll(branch_title, show_hint=False)
                if urls and store_names:
                    for url, store_name in zip(urls, store_names):
                        if normalize_branch_title(branch_title) in normalize_branch_title(store_name):
                            new_branch_title, new_branch_name = get_store_branch_title(store_name)
                            store = Store.newObject(store_name, url, branch_title=branch_title, branch_name=new_branch_name)
                            if store.exists(database):
                                if branch_name != new_branch_name:
                                    if not database.is_value_exists('stores', name=store.name, branch_title=store.branch_title):
                                        print(f'✴️已存在分店【{store_name}】')
                                        store.change_branch(database, branch_title, new_branch_name)
                                    else:
                                        print(f'#️⃣已擁有分店【{store_name}】')
                                    if not has_branches: has_branches = True
                            else:
                                print(f'✳️已建立分店【{store_name}】')
                                store.insert_if_not_exists(database)
                                if not has_branches: has_branches = True
                        else:
                            print(f'⛔不屬於分店【{store_name}】')
                if has_branches:
                    self = Store.newObject(name, link, branch_title=branch_title)
                    if not database.is_value_exists('stores', name=self.name, branch_title=self.branch_title):
                        print(f'☑️已設定分店【{name}】')
                        self.change_branch(database, branch_title, branch_name)
                print()
                if not RANDOM_MODE:
                    branches_state[CURRENT_MODE_NAME][database.name] = sid + 1
                    write_json(branches_state, INDEX_FILE)
                time.sleep(1)

    # 計算時間差
    TIME_DIFFERENCE = datetime.now() - START_TIME
    MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60
    print(f'\r【✅已完成】{CURRENT_MODE_NAME} | 耗時:{MINUTES_ELAPSE:.2f}分鐘', end='')
    if CURRENT_MODE_BROWSE: driver.exit()
