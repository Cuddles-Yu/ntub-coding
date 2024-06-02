from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, classification_report


def train_model(X_train, y_train_sentiment, y_train_label):
    model_sentiment = LogisticRegression()
    model_label = LogisticRegression()

    model_sentiment.fit(X_train, y_train_sentiment)
    model_label.fit(X_train, y_train_label)

    return model_sentiment, model_label


def evaluate_model(model_sentiment, model_label, X_test, y_test_sentiment, y_test_label):
    y_pred_sentiment = model_sentiment.predict(X_test)
    y_pred_label = model_label.predict(X_test)

    accuracy_sentiment = accuracy_score(y_test_sentiment, y_pred_sentiment)
    accuracy_label = accuracy_score(y_test_label, y_pred_label)

    report_sentiment = classification_report(y_test_sentiment, y_pred_sentiment)
    report_label = classification_report(y_test_label, y_pred_label)

    return accuracy_sentiment, accuracy_label, report_sentiment, report_label
