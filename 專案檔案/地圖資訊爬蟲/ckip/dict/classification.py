from 地圖資訊爬蟲.ckip.module.functions import *

FILES_PATH = ['adjectives/negative.txt', 'adjectives/neutral.txt', 'adjectives/positive.txt', 'adjectives/preference.txt']

if __name__ == '__main__':
    complete_words = []
    for file in FILES_PATH:
        with open(file, 'r', encoding='utf-8') as f:
            for complete_word in f.read().split('\n'):
                if complete_word != '' and complete_word not in complete_words: complete_words.append(complete_word)

    data = load_json('../adjective.json')
    print(f'進行詞庫監督式標籤劃分[-1負面;0中立;1正面;2偏好]')
    for tag, words in data.items():
        print(f'目前類別: {tag}\n')
        for word, count in words.items():
            if word in complete_words: continue
            print(f'【 {word} 】 ', end='')
            state = input()
            if state == '-1':
                with open(FILES_PATH[0], 'a', encoding='utf-8') as f:
                    f.write(f'{word}\n')
            elif state == '0':
                with open(FILES_PATH[1], 'a', encoding='utf-8') as f:
                    f.write(f'{word}\n')
            elif state == '1':
                with open(FILES_PATH[2], 'a', encoding='utf-8') as f:
                    f.write(f'{word}\n')
            elif state == '2':
                with open(FILES_PATH[3], 'a', encoding='utf-8') as f:
                    f.write(f'{word}\n')
