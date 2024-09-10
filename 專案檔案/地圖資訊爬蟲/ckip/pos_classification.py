from enum import Enum
from 地圖資訊爬蟲.ckip.module.functions import *

JSON_FILE = 'doc/pos_target.json'
OUTPUT_FILE = 'doc/pos_target.json'
ALL_POS_FILE = 'tagger/results/all_pos.json'

class Classification(Enum):
    # 類別
    category_adj = '形容詞'
    category_obj = '受詞'
    # 詞性
    adjective = '狀態不及物動詞(VH)'
    object = '普通名詞(Na)'
    # 屬性-通用
    ignore = '忽略'
    check = '觀察'
    nothing = '無'
    foreign = '外文'
    # 屬性-形容詞
    positive = '正面'
    negative = '負面'
    neutral = '中立'
    prefer = '喜好'
    # 屬性-受詞
    environment = '環境'
    product = '產品'
    service = '服務'
    price = '售價'

def to_adjective_class(code):
    match code:
        case '9':  # 喜好
            return Classification.prefer.value, False
        case '0':  # 中立
            return Classification.neutral.value, False
        case '-':  # 負面
            return Classification.negative.value, True
        case '=':  # 正面
            return Classification.positive.value, True
        case 'o':  # 忽略
            return Classification.ignore.value, False
        case 'p':  # 觀察
            return Classification.check.value, False
        case _:
            return None, None

def to_object_class(code):
    match code:
        case '0':
            return Classification.nothing.value
        case '1':
            return Classification.environment.value
        case '2':
            return Classification.product.value
        case '3':
            return Classification.service.value
        case '4':
            return Classification.price.value
    return None

if __name__ == '__main__':
    ### 讀取詞性檔案 ###
    all_pos = load_json(ALL_POS_FILE)
    adjective_dict = all_pos.get(Classification.adjective.value, {})
    object_dict = all_pos.get(Classification.object.value, {})
    ### 讀取劃分檔案 ###
    pos_target = load_json(JSON_FILE)
    adj_dict = pos_target.get(Classification.category_adj.value, {})
    obj_dict = pos_target.get(Classification.category_obj.value, {})

    print(f"請選擇要進行'詞性屬性'劃分的類別代碼 [1形容詞;2受詞] ", end='')
    match input():
        case '1':
            POS_TYPE = Classification.category_adj.value
        case '2':
            POS_TYPE = Classification.category_obj.value
        case 'admin':
            POS_TYPE = '測試'
        case _:
            print(f'不包含指定的類別代碼。')
            exit()

    counter = 0
    match POS_TYPE:
        case '測試':
            pass
        case Classification.category_adj.value:
            print(f"進行'{POS_TYPE}'監督式詞性屬性劃分 [*結束;9喜好;0中立;-負面;=正面;o忽略;p觀察]\n")
            positive_list = adj_dict.get(Classification.positive.value, {})
            negative_list = adj_dict.get(Classification.negative.value, {})
            neutral_list = adj_dict.get(Classification.neutral.value, [])
            prefer_list = adj_dict.get(Classification.prefer.value, [])
            ignore_list = adj_dict.get(Classification.ignore.value, [])
            check_list = adj_dict.get(Classification.check.value, [])
            for adj, count in adjective_dict.items():
                counter += 1
                if (
                    is_keyword_in_pos(positive_list, adj) or
                    is_keyword_in_pos(negative_list, adj) or
                    adj in neutral_list or
                    adj in prefer_list or
                    adj in ignore_list or
                    adj in check_list
                ): continue

                print(f'進度[{counter}/{len(adjective_dict)}] {adj}({count}) ', end='')
                pos_class, need_state_classification = to_adjective_class(input())
                if not pos_class: break
                if need_state_classification:
                    print(f"【{pos_class}形容詞】{adj} | 指標劃分 [*結束;0無;1環境;2產品;3服務;4售價] ", end='')
                    state_class = to_object_class(input())
                    if not state_class: break
                    pos_target[Classification.category_adj.value][pos_class][state_class].append(adj)
                else:
                    pos_target[Classification.category_adj.value][pos_class].append(adj)

        case Classification.category_obj.value:
            print(f"進行'{POS_TYPE}'監督式詞性屬性劃分 [*結束;0忽略;1環境;2產品;3服務;4售價;\\觀察]\n")
            environment_list = obj_dict.get(Classification.environment.value, [])
            product_list = obj_dict.get(Classification.product.value, [])
            service_list = obj_dict.get(Classification.service.value, [])
            price_list = obj_dict.get(Classification.price.value, [])
            ignore_list = obj_dict.get(Classification.ignore.value, [])
            check_list = obj_dict.get(Classification.check.value, [])
            for obj, count in object_dict.items():
                counter += 1
                filed = False
                if obj in environment_list or obj in product_list or obj in service_list or obj in price_list or obj in ignore_list or obj in check_list: continue
                print(f'進度[{counter}/{len(adjective_dict)}] {obj}({count}) ', end='')
                control = input()
                if '1' in control:
                    filed = True
                    pos_target[Classification.category_obj.value][Classification.environment.value].append(obj)
                    print(f"[✏️已紀錄] 將'{obj}'劃分至「環境」指標")
                if '2' in control:
                    filed = True
                    pos_target[Classification.category_obj.value][Classification.product.value].append(obj)
                    print(f"[✏️已紀錄] 將'{obj}'劃分至「產品」指標")
                if '3' in control:
                    filed = True
                    pos_target[Classification.category_obj.value][Classification.service.value].append(obj)
                    print(f"[✏️已紀錄] 將'{obj}'劃分至「服務」指標")
                if '4' in control:
                    filed = True
                    pos_target[Classification.category_obj.value][Classification.price.value].append(obj)
                    print(f"[✏️已紀錄] 將'{obj}'劃分至「售價」指標")
                if '0' in control:
                    filed = True
                    pos_target[Classification.category_obj.value][Classification.ignore.value].append(obj)
                    print(f"[✏️已紀錄] 將'{obj}'劃分至「忽略」")
                if '\\' in control:
                    filed = True
                    pos_target[Classification.category_obj.value][Classification.check.value].append(obj)
                    print(f"[✏️已紀錄] 將'{obj}'劃分至「觀察」")
                if not filed: break

    print(f"\n已將劃分結果輸出至 -> {OUTPUT_FILE}")
    write_json(pos_target, OUTPUT_FILE)
