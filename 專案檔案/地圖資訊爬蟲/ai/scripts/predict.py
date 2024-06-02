import jieba.posseg as pseg


def predict(model_sentiment, model_label, vectorizer, new_comments, label_mapping):
    # 分詞和詞性標註
    new_comments_processed = [' '.join([f"{word}/{flag}" for word, flag in pseg.cut(comment)]) for comment in
                              new_comments]
    new_comments_transformed = vectorizer.transform(new_comments_processed)

    predictions_sentiment = model_sentiment.predict(new_comments_transformed)
    predictions_label = model_label.predict(new_comments_transformed)

    # 將數字標籤轉換為文字標籤
    inv_label_mapping = {v: k for k, v in label_mapping.items()}
    predictions_label_text = [inv_label_mapping[pred] for pred in predictions_label]

    return predictions_sentiment, predictions_label_text
