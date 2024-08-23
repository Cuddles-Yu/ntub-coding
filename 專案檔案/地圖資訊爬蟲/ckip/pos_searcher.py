from enum import Enum
from 地圖資訊爬蟲.ckip.module.functions import *
from 地圖資訊爬蟲.crawler.module.functions.common import *

JSON_FILE = 'doc/pos_target.json'
ALL_ANALYSIS_FILE = 'tagger/results/all_analysis.json'

POS_MARK = '='
RANGE = 3
DISPLAY_LIMIT = 200

if __name__ == '__main__':
    ### 讀取詞性檔案 ###
    print(f'\r正在讀取留言分析結構...', end='')
    all_analysis = load_json(ALL_ANALYSIS_FILE)
    while True:
        print(f'\r請輸入要查詢斷詞前後的關鍵字[空字串:結束] ', end='')
        target = input()
        if not target.strip(): break

        if POS_MARK in target:
            target = target.replace(POS_MARK, '')
            target_analysis = [
                a for a in all_analysis
                if target in a.get('詞性', [])
            ]
            display_counter = 0
            for analysis in target_analysis:
                if display_counter >= DISPLAY_LIMIT: break
                s = analysis.get('斷詞', [])
                p = analysis.get('詞性', [])
                for i, pos in enumerate(p):
                    if pos == target:
                        combine = ''
                        filtered_s = s[max(0, i-RANGE):min(len(s), i+RANGE+1)]
                        filtered_p = p[max(0, i-RANGE):min(len(p), i+RANGE+1)]
                        # if filtered_p != ['VA', 'Dfa', 'VH']: continue
                        for _w, _p in zip(filtered_s, filtered_p):
                            combine += f' {_w}({_p})'
                        print(f'    {combine.strip()}')
                        display_counter += 1
        else:
            target_analysis = [
                a for a in all_analysis
                if target in a.get('斷詞', [])
            ]
            for analysis in target_analysis:
                s = analysis.get('斷詞', [])
                p = analysis.get('詞性', [])
                i = s.id(target)
                print(s[i-RANGE:i+RANGE+1], p[i-RANGE:i+RANGE+1])
