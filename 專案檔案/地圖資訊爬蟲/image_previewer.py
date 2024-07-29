from 地圖資訊爬蟲.crawler.module.functions.SqlDatabase import SqlDatabase


html_content = '''
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>餐廳評論</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        .card {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .card img {
            max-width: 150px;
            margin-right: 20px;
            border-radius: 8px;
        }
        .card-content {
            flex: 1;
        }
        .card-content h2 {
            margin: 0;
            color: #333;
        }
        .card-content p {
            margin: 10px 0;
            color: #666;
        }
        .card-content a {
            text-decoration: none;
            color: #1e90ff;
        }
    </style>
</head>
<body>
    <div class="container">
'''

end = '''
    </div>
</body>
</html>
'''

database = SqlDatabase('mapdb', 'root', '11236018')
result = database.fetch('all', '''
    SELECT name, word, image_url, source_url FROM stores AS s, keywords AS k
    WHERE s.id = k.store_id and source = 'recommend' and image_url IS NOT NULL
    ORDER BY count DESC
''')
for i in range(len(result)):
    name, word, image_url, source_url = result[i]
    card = f'''
        <div class="card">
            <img src="{image_url}" alt="餐點圖片">
            <div class="card-content">
                <h2>{name}</h2>
                <p>{word}</p>
                <a href="{source_url}" target="_blank">來源網站</a>
            </div>
        </div>
    '''
    html_content += card
html_content += end

with open('preview.html', 'w+', encoding='utf-8') as f:
    f.write(html_content)
database.close()
