from 地圖資訊爬蟲.crawler.module.functions import SqlDatabase
import 地圖資訊爬蟲.crawler.tables.Store as Store

db = SqlDatabase.SqlDatabase('mapdb', 'root', '11236018')

all_stores = db.fetch('all', f'''
    SELECT id, name, link FROM stores
    ORDER BY id
''')
total_store_count = len(all_stores)

repair_ids = []
for i, (sid, name, link) in enumerate(all_stores):
    if sid != i+1:
        repair_ids.append(i+1)
    if i+1 >= total_store_count: break

for repair_id in repair_ids:
    sid, name, link = all_stores.pop(-1)
    if sid <= total_store_count: continue
    Store.newObject(name, link).change_id(db, repair_id)
    print(f"✏️已成功更新商家id'{name}' ({sid} -> {repair_id})")

db.set_increment('stores', total_store_count+1)

if repair_ids:
    print(f"\n✅已完成更新資料庫中所有缺少的商家序列id。")
else:
    print(f"未找到需進行更新的商家序列id。")
    print(f"已重設stores資料表的自動遞增值為 -> {total_store_count+1}")

