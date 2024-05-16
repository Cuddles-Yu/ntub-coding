import jieba
from transformers import BertTokenizer, BertForSequenceClassification
import torch

# 加載模型和分詞器
model_name = "bert-base-chinese"
tokenizer = BertTokenizer.from_pretrained(model_name)
model = BertForSequenceClassification.from_pretrained(model_name, num_labels=2)  # 假設情感分析是二分類

# 示例句子
sentence = "店員態度差建議業主多做員工訓練.."

# 使用 jieba 斷詞
words = jieba.cut(sentence, cut_all=False)
word_list = list(words)

# 將斷詞結果轉換為模型需要的格式
inputs = tokenizer(word_list, return_tensors="pt", padding=True, truncation=True, max_length=512)

# 進行預測
with torch.no_grad():
    outputs = model(**inputs)
    predictions = torch.nn.functional.softmax(outputs.logits, dim=-1)

# 打印每個詞的情感分析結果
labels = ['Negative', 'Positive']
for word, prediction in zip(word_list, predictions):
    label = labels[prediction.argmax().item()]
    print(f"Text: {word} | Sentiment: {label}")
