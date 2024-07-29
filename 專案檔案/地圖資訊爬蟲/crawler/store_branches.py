from datetime import datetime
from 地圖資訊爬蟲.crawler.module.functions.EdgeDriver import EdgeDriver
from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase

if __name__ == '__main__':
    database = SqlDatabase('mapdb', 'root', '11236018')
    driver = EdgeDriver(database)
    # 取得商家關鍵字
    old_urls = [url[0] for url in database.fetch('all', '''
        SELECT link FROM stores
        ORDER BY id
    ''')]
    with open('BRANCHES_INDEX', 'r', encoding='utf-8') as f:
        content = f.read()
        branches_index = int(content) if content.isdigit() else 0
    print(f'正在執行擴增商家 -> {database.name}\n')
    START_TIME = datetime.now()
    for i in range(branches_index, len(old_urls)):
        print(f'\r正在取得商家週邊的其他商家({i+1}/{len(old_urls)})...\n', end='')
        driver.get(old_urls[i])
        driver.search_and_scroll('餐廳')
        with open('BRANCHES_INDEX', 'w', encoding='utf-8') as f: f.write(str(i+1))
    # 計算時間差
    TIME_DIFFERENCE = datetime.now() - START_TIME
    MINUTES_ELAPSE = TIME_DIFFERENCE.total_seconds() / 60
    print(f'\r【✅已完成】耗時:{MINUTES_ELAPSE:.2f}分鐘', end='')
    driver.exit()
