import pandas as pd

# 創建範例數據
data = {
    "text": [
        "餐廳很乾淨",
        "環境很好",
        "太貴了",
        "蛋塔好吃",
        "老闆服務態度差",
        "價格合理",
        "環境不錯",
        "產品質量高",
        "服務人員很友善"
    ],
    "label": [
        "環境",
        "環境",
        "產品售價",
        "產品好感",
        "商家服務",
        "產品售價",
        "環境",
        "產品好感",
        "商家服務"
    ]
}

# 轉換為DataFrame
df = pd.DataFrame(data)

# 保存為CSV文件
df.to_csv('data.csv', index=False)