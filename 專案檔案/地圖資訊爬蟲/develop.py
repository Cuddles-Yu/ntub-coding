import pyautogui

# pyautogui.hotkey('ctrl', 't', interval=0.1)
# print(f'\r目前搜尋進度[{str(i+1).zfill(len(str(maxCount)))}/{maxCount} ({round((i+1) * 100 / maxCount, 2)}%)]', end='')

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

import re

village = re.search(r'(?P<village>\S+里)', 'XGQ4+VQ 外南區 新北市中和區')
print(village.group() if village else 'None')