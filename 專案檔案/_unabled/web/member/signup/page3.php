<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員註冊頁面 Part III</title>
    <link rel="stylesheet" href="styles/new_signup_page_3.css">
</head>
<body>
    <div class="login-container">
        <form class="login-form" action="new_signup_page_3.html" method="post">
            <div id="title">Part 3. 個人化權重設定</div>
            <div class="description_text">評星宇宙制定了五項指標權重，使搜尋結果更符合您的需求。</div>
            <div class="description_text">以下資訊可在會員資訊頁面進行設定及修改。</div>
            
            <div class="slider-container">
                <label for="environment">環境</label>
                <input type="range" id="environment" name="environment" min="0" max="100" value="50" oninput="updateValue('environment')">
                <span id="environment_value">50%</span>
            </div>
            
            <div class="slider-container">
                <label for="product">產品</label>
                <input type="range" id="product" name="product" min="0" max="100" value="50" oninput="updateValue('product')">
                <span id="product_value">50%</span>
            </div>
            
            <div class="slider-container">
                <label for="service">服務</label>
                <input type="range" id="service" name="service" min="0" max="100" value="50" oninput="updateValue('service')">
                <span id="service_value">50%</span>
            </div>
            
            <div class="slider-container">
                <label for="price">售價</label>
                <input type="range" id="price" name="price" min="0" max="100" value="50" oninput="updateValue('price')">
                <span id="price_value">50%</span>
            </div>
            
            <div class="slider-container">
                <label for="popularity">熱門度</label>
                <input type="range" id="popularity" name="popularity" min="0" max="100" value="50" oninput="updateValue('popularity')">
                <span id="popularity_value">50%</span>
            </div>

            <div class="next_area">
                <button id="skip_button" type="submit">
                    <span>略過</span>
                </button>
                <button id="next_button" type="submit">
                    <span>完成</span>
                </button>
            </div>
        </form>
    </div>

    <script src="scripts/new_signup_page_3.js"></script>
</body>
</html>