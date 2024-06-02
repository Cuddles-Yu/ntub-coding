from scripts.preprocess import load_data, preprocess_data
from scripts.train import train_model, evaluate_model
from scripts.predict import predict

# 讀取資料
data = load_data('data/data.csv')

# 資料前處理
(X_train, X_test, y_train_sentiment, y_test_sentiment, y_train_label, y_test_label,
 vectorizer, label_mapping) = preprocess_data(data)

# 訓練模型
model_sentiment, model_label = train_model(X_train, y_train_sentiment, y_train_label)

# 評估模型
(accuracy_sentiment, accuracy_label, report_sentiment, report_label) = evaluate_model(
    model_sentiment, model_label, X_test, y_test_sentiment, y_test_label)

print(f'Sentiment Model Accuracy: {accuracy_sentiment}')
print('Sentiment Classification Report:')
print(report_sentiment)

print(f'Label Model Accuracy: {accuracy_label}')
print('Label Classification Report:')
print(report_label)

# 預測新評論
new_comments = ["餐廳很乾淨", "太貴了", "蛋塔太貴了", "老闆服務態度差", "蛋塔很好"]
predictions_sentiment, predictions_label_text = predict(
    model_sentiment, model_label, vectorizer, new_comments, label_mapping)

for comment, sentiment, label in zip(new_comments, predictions_sentiment, predictions_label_text):
    sentiment_text = '正面' if sentiment == 1 else '負面'
    print(f"Comment: {comment} -> Sentiment: {sentiment_text}, Label: {label}")
