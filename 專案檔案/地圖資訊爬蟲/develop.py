import 地圖資訊爬蟲.crawler.module.modify_database as mdb
from 地圖資訊爬蟲.crawler.module.core_database import *

### 主程式 ###
connection = connect(use_database=True)
if connection is None: exit()

# print(mdb.select_table_value_by_column(connection, 'DISTINCT name, id', 'stores'))

for i in mdb.select_table_value_by_column(connection, 'DISTINCT word', 'keywords'):
    print(i[0])

# 關閉資料庫連線階段
connection.close()

# pyautogui.hotkey('ctrl', 't', interval=0.1)
# print(f'\r目前搜尋進度[{str(i+1).zfill(len(str(maxCount)))}/{maxCount} ({round((i+1) * 100 / maxCount, 2)}%)]', end='')
from datetime import datetime

# import pandas as pd

# data['留言內容'].append(f'[{", ".join(comments)}]')

# driver.switch_to.window(driver.window_handles[1])

# 1.模擬滑鼠右鍵打開功能選單來取得座標位置
# canvas = driver.find_element(By.XPATH, "//*[name()='canvas']")
# ActionChains(driver).context_click(canvas).perform()
# WebDriverWait(driver, 10).until(
#     EC.presence_of_element_located((By.CLASS_NAME, 'mLuXec'))
# )
# data['座標'].append(driver.find_element(By.CLASS_NAME, 'mLuXec').text)

# progressPercentage = round((i + 1) * 100 / maxCount, 2)
# print(f'\r已完成 {str(i + 1).zfill(len(str(maxCount)))}/{maxCount}, {progressPercentage}% [{"▮" * int(progressPercentage // 10 + 1)}{"▯" * int(10 - (progressPercentage // 10 + 1))}]\n', end='')

# import pandas as pd
#
# data = {
#     '測試1': None,
#     '測試2': '',
#     '測試3': 0
# }
#
# pd.DataFrame(data, index=[1]).to_csv('data.csv', encoding='utf-8-sig')

# return re.sub(r'\d', '', address).split('區')[0] + '區'

# village = re.search(r'(?P<village>\S+里)', 'XGQ4+VQ 外南區 新北市中和區')
# print(village.group() if village else 'None')

# 準備寫入檔案前先清空資料表
# if i == 0: pd.DataFrame(columns=data.keys()).to_csv(filePath, index=False, encoding='utf-8-sig')
# 新增資料至資料表
# pd.DataFrame(data, index=[1]).to_csv(filePath, mode='a', header=False, index=False, encoding='utf-8-sig')

# class ButtonType:
#     write = ('撰寫評論', 0)
#     search = ('查詢評論', 1)
#     order = ('排序評論', 2)
#
# print(ButtonType.write[1])


# -(使用者名稱: filtered_comments[index].find_element(By.CLASS_NAME, 'd4r55').text)

# print(', '.join('No. 72, No. 36, Section 1, Zhongxiao West Road, 中正區台北市100'.split(', ')[0: 2]))

# print(''.join(re.findall(r'[0-9]+', '102 篇評論')))

# from 地圖資訊爬蟲.crawler.module.core_database import *
# from itertools import chain

### 主程式 ###
# connection = connect(use_database=True)
# if connection is None: exit()
#
# cursor = connection.cursor()
# if exists(cursor, NAME):
#     try:
#         cursor.execute(f'''
#             SELECT s.link FROM `stores` AS s
#             INNER JOIN `rates` AS r ON s.name = r.store_name
#             WHERE r.avg_ratings = 0.0
#         ''')
#         print(set(chain.from_iterable(cursor.fetchall())))
#     except mysql.connector.Error as error:
#         # 處理錯誤
#         print("Error:", error)
#     finally:
#         cursor.close()
#
# # 關閉資料庫連線階段
# cursor.close()
# connection.close()
#
# datetime.now().strftime('%Y-%m-%d %H:%M:%S')

# from numpy import sin, cos, arccos, pi, round
#
#
# def rad2deg(radians):
#     degrees = radians * 180 / pi
#     return degrees
#
#
# def deg2rad(degrees):
#     radians = degrees * pi / 180
#     return radians
#
#
# def getDistanceBetweenPointsNew(coordinate1: list, coordinate2: list, unit='kilometers'):
#     theta = coordinate1[1] - coordinate2[1]
#     distance = 60 * 1.1515 * rad2deg(
#         arccos(
#             (sin(deg2rad(coordinate1[0])) * sin(deg2rad(coordinate2[0]))) +
#             (cos(deg2rad(coordinate1[0])) * cos(deg2rad(coordinate2[0])) * cos(deg2rad(theta)))
#         )
#     )
#     match unit:
#         case 'miles':
#             return round(distance, 2)
#         case 'kilometers':
#             return round(distance * 1.609344, 2)
#
# print(getDistanceBetweenPointsNew((25.0510350, 121.5405672), (25.0385156, 121.5548592)))
