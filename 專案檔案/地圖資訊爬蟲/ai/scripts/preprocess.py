import pandas as pd
import jieba.posseg as pseg
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.model_selection import train_test_split


def load_data(filepath):
    data = pd.read_csv(filepath)
    return data


def segment_and_tag(text):
    words = pseg.cut(text)
    result = []
    for word, flag in words:
        result.append(f"{word}/{flag}")
    return ' '.join(result)


def preprocess_data(data):
    # 進行分詞和詞性標註
    data['processed_text'] = data['text'].apply(segment_and_tag)

    vectorizer = TfidfVectorizer()
    X = vectorizer.fit_transform(data['processed_text'])

    # 將標籤和情感轉換為數字
    sentiment_mapping = {'正面': 1, '負面': 0}
    label_mapping = {label: idx for idx, label in enumerate(data['label'].unique())}

    y_sentiment = data['sentiment'].map(sentiment_mapping)
    y_label = data['label'].map(label_mapping)

    X_train, X_test, y_train_sentiment, y_test_sentiment, y_train_label, y_test_label = train_test_split(
        X, y_sentiment, y_label, test_size=0.2, random_state=42)

    return X_train, X_test, y_train_sentiment, y_test_sentiment, y_train_label, y_test_label, vectorizer, label_mapping
