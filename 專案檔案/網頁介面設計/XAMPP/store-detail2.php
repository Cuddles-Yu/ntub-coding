<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>店家詳細內容</title>
  <link rel="stylesheet" href="styles/store-detail-styles.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <meta name="keywords" content="評價, google map" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/> 

    <!-- 載入 leaflet.awesome-markers.css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.css" />

    <!-- 載入 MarkerCluster.css -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
    <!-- 載入 osm_map.css -->
    <link rel="stylesheet" type="text/css" href="./osm_map.css"> 
</head>

<body style="margin-top: 80px;  background-color: #ffffff">
  <?php
  // 引入資料庫連接和查詢函數
  require 'queries.php';
  
  // 獲取商家名稱
  $storeName = $_GET['name'] ?? '';

  if ($storeName) {
      $sql = "SELECT * FROM stores WHERE name = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $storeName);
      $stmt->execute();
      $result = $stmt->get_result();
      $store = $result->fetch_assoc();

      if ($store) {
          // 顯示商家資訊
          echo "<h1>" . htmlspecialchars($store['name']) . "</h1>";
          echo "<p>" . htmlspecialchars($store['description']) . "</p>";
          // 顯示其他商家資訊
      } else {
          echo "<p>找不到該商家</p>";
      }
  } else {
      echo "<p>未提供商家名稱</p>";
  }
  
  // 獲取店家資訊
  $storeInfo = getStoreInfo($storeName);
  $comments = getComments($storeInfo['id']);
  $location = getLocation($storeInfo['id']);
  $rating = getRating($storeInfo['id']);
  $service = getService($storeInfo['id']);
  $keywords = getKeyword($storeInfo['id']);
  $allKeywords = getAllKeywords($storeInfo['id']);
  ?>
  <!--頁首-->
  <div class="fixed-top"><img src="images/Logo設計_圖像(藍+).png" class="team-icon"><a class="title" href="https://commentspace.ascdc.tw/search.html">評星宇宙</a>
    <!--會員頭像-->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end position-absolute top-0 end-0" style="margin-top: 10px;">
      <button class="btn btn-primary me-md-2" type="button" style="background-color: #485465; border-color: #485465; border-radius: 15px;">
        <a href="https://commentspace.ascdc.tw/member/information.html"><img class="member-button" src="images/avatar.jpg"></a>
      </button>   
      <!--主選單-->
      <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
        aria-controls="offcanvasRight" style="background-color: #485465; border-color: #485465">
        <img class="Gutters-icon" src="images/Gutters.png" /></button>
      <!--選單內頁-->
      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel"
        style="background-color: #485465">
        <div class="offcanvas-header">
          <img class="header-icon2" src="images/Logo設計_圖像(藍+).png">
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="Options">
            <li><a href="https://commentspace.ascdc.tw/search.html" class="Options-link"><img class="Options-icon" src="images/home.png" />主頁</a></li>
            <li><a href="https://commentspace.ascdc.tw/member/information.html" class="Options-link"><img class="Options-icon" src="images/people.png" />會員專區</a></li>
            <li><a href="#" class="Options-link"><img class="Options-icon" src="images/!.png" />使用說明</a></li>
            <li><a href="#" class="Options-link"><img class="Options-icon" src="images/mail.png" />問題回饋</a></li>
            <li><a href="#" class="Options-link"><img class="Options-icon" src="images/team.png" />成員介紹</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!--MAP、店家詳細資訊框架-->
  <div class="container-fluid">
    <div class="row">
      <!--MAP--><!--"API串聯"-->
      <div class="col-12 col-md-6 col-lg-7" style="height: 47em;">
        <!-- 地圖物件: map -->
        <div id="map">
            <!-- 使用者定位按鈕 -->
            <button type="button" id="locateButton" onclick="userLocate()">使用您的位置</button>
        </div>

        <!-- 載入地圖框架 leaflet.js -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <!-- 載入 leaflet.awesome-markers.min.js -->
        <script src="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.min.js"></script>

        <!-- 載入 Font Awesome Kit -->
        <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>

        <!-- 載入 Markercluster.js -->
        <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

        <!-- 載入JSON資料 data.json -->
        <script src="./data.json"></script>

        <!-- 載入主程式 osm_map.js -->
        <script src="./osm_map.js"></script>
      </div>
      <!--店家詳細資訊-->
      <div class="col-12 col-md-6 col-lg-5">
        <div class="title">
          <div class="row">
            <div class="col-6">
              <!--綜合評分-->
              <button type="button" class="btn btn-primary position-relative" style="width: 8vw; background-color: #e74c30; border-color: #e74c30; margin-bottom: 1vh">
                綜合評分 85 分<!--填入計算分數-->
              </button>
            </div>
            <div class="col-2">
              <div class="tag">
                <button type="button" class="btn btn-primary position-relative title-tag" style="background-color: #6A6A6A;border-color: #6A6A6A;color: white;width: 6em;height: 2.3em;vertical-align: middle;">
                <img src="images/restaurant-2.png" style="width: 1em;"><?php echo htmlspecialchars($storeInfo['tag']); ?><!--填入"餐廳種類"ex:餐廳、西餐廳-->
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-3">
                <!--餐廳星數-->
                <div class="score">
                <?php
                $avgRating = isset($rating['avg_ratings']) ? $rating['avg_ratings'] : 0;
                for ($i = 1; $i <= 5; $i++) {
                  if ($i <= $avgRating) {
                      echo '<img class="star' . $i . '" src="images/star-y.png">';
                  } else {
                      echo '<img class="star' . $i . '" src="images/star-w.png">';
                  }
              }
                ?>
                </div>
              </div>
              <div class="col-1" style="margin-left: -0.5vw;">
                <div style="border: #485465 solid 1px; text-align: center; border-radius: 10px; color: black;">4.0</div>
              </div>
            </div>
          </div>          
        </div>
        <!--地址、電話、網頁與四大評分欄 框架-->
        <div class="container" >
          <div class="row">
            <div class="col-12 col-md-6 col-lg-6 " style="text-align: left;">
              <div class="information" style="font-size: small; line-height: 3;">
                <ul class="text-left" style="padding-left: 0;">
                  <li>地址: <a href="https://maps.google.com/?q=<?php echo urlencode($storeInfo['name']); ?>" >
                          <?php echo htmlspecialchars($location['postal_code'] . ' ' . $location['dist'] . ' ' . $location['vil'] . ' ' . $location['city'] . ' ' . $location['details']); ?></a></li><!--填入"餐廳地址"-->
                  <li>電話: <?php echo htmlspecialchars(!empty($storeInfo['phone_number']) ? $storeInfo['phone_number'] : '無'); ?></li><!--填入"餐廳電話"-->
                  <li>網頁:  <a href="<?php echo htmlspecialchars(!empty($storeInfo['website']) ? $storeInfo['website'] : '#'); ?>" target="_blank">
                            <?php echo htmlspecialchars(!empty($storeInfo['website']) ? $storeInfo['website'] : '無'); ?></a></li><!--填入"餐廳網址"-->
                </ul>
              </div>
            </div>
            <!--五大評分欄-->
            <div class="col-12 col-md-6 col-lg-6">
              <div class="standard-text" style="position: relative; float: left;">
                <div class="text0">熱門</div>
                <div class="text1">環境</div>
                <div class="text2">產品</div>
                <div class="text3">服務</div>
                <div class="text4">售價</div>
              </div>
              <!--評分條-->
              <div class="standard" style="vertical-align: top;">
                <div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="25"
                  aria-valuemin="0" aria-valuemax="100"
                  style="background-color: #d9d9d9; margin-bottom: 0.625em;">
                  <div class="progress-bar "
                        style="width: <?php echo isset($store["real_rating"]) ? htmlspecialchars($store["real_rating"]) : '0'; ?>%;; background-color: #B45F5F; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                        <?php echo isset($store["real_rating"]) ? htmlspecialchars($store["real_rating"]) : '0'; ?>%</div><!--填入"熱門指數"width也要跟著改-->
                    </div>
                    <div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="25"
                      aria-valuemin="0" aria-valuemax="100"
                      style="background-color: #d9d9d9; margin-bottom: 0.375em;">
                      <div class="progress-bar "
                        style="width: <?php echo isset($store["environment_rating"]) ? htmlspecialchars($store["environment_rating"]) : '0'; ?>%; background-color: #562B08; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                        <?php echo isset($store["environment_rating"]) ? htmlspecialchars($store["environment_rating"]) : '0'; ?>%</div><!--填入"環境指數"width也要跟著改-->
                    </div>
                    <div class="progress" role="progressbar" aria-label="Info example" aria-valuenow="50" aria-valuemin="0"
                      aria-valuemax="100" style="background-color: #d9d9d9; margin-top: 0.55em; margin-bottom: 0.375em;">
                      <div class="progress-bar"
                        style="width: <?php echo isset($store["product_rating"]) ? htmlspecialchars($store["product_rating"]) : '0'; ?>%; background-color: #7B8F60;text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                        <?php echo isset($store["product_rating"]) ? htmlspecialchars($store["product_rating"]) : '0'; ?>%</div><!--填入"產品指數"width也要跟著改-->
                    </div>
                    <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="75"
                      aria-valuemin="0" aria-valuemax="100"
                      style="background-color: #d9d9d9;  margin-top: 0.57em; margin-bottom: 0.4375em;">
                      <div class="progress-bar"
                        style="width: <?php echo isset($store["service_rating"]) ? htmlspecialchars($store["service_rating"]) : '0'; ?>%; background-color: #5053AF; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                        <?php echo isset($store["service_rating"]) ? htmlspecialchars($store["service_rating"]) : '0'; ?>%</div><!--填入"服務指數"width也要跟著改-->
                    </div>
                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100"
                      aria-valuemin="0" aria-valuemax="100"
                      style="background-color: #d9d9d9; margin-top: 0.60em; margin-bottom: 0.375em;">
                      <div class="progress-bar"
                        style="width: <?php echo isset($store["price_rating"]) ? htmlspecialchars($store["price_rating"]) : '0'; ?>%; background-color: #C19237; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                        <?php echo isset($store["price_rating"]) ? htmlspecialchars($store["price_rating"]) : '0'; ?>%</div><!--填入"售價指數"width也要跟著改-->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--內用、外帶、外送-->
            <div class="row">
              <div class="col">
                <div class="state" style="text-align: left; vertical-align: top; font-size: small;">
                <div class="state-1"><img class="state-icon" src="images/<?php echo ($service['dine_in'] == 1 ? 'YES' : 'NO'); ?>.png">內用</div><!-- "內用"的狀態圖示根據資料庫結果改變 -->
                <div class="state-2"><img class="state-icon" src="images/<?php echo ($service['take_away'] == 1 ? 'YES' : 'NO'); ?>.png">外帶</div><!-- "外帶"的狀態圖示根據資料庫結果改變 -->
                <div class="state-3"><img class="state-icon" src="images/<?php echo ($service['delivery'] == 1 ? 'YES' : 'NO'); ?>.png">外送</div><!-- "外送"的狀態圖示根據資料庫結果改變 -->
                </div>
              </div>
            </div>

            <!--詳細評論標題-->
            <div class="row">
              <div class="comment-group" style="margin-top: 1em;">
                <img class="message" src="images/message.png">
                <h4 style="text-align: left; margin-left: 2em; margin-top: 0.15em; font-weight: bold;">評論</h4>
              </div>
            </div>
            <!--詳細評論內容-->
            <div class="row overflow-auto" style="height: 15em;">
            <?php
              foreach ($comments as $comment) {
                $starCount = $comment['rating'];
                $userLevel = isset($comment['contributor_id']) ? $comment['contributor_id'] : 0;
                echo "<div class='group' style='margin-top: 0.625em; background-color: #F9F9F9; border-radius: 15px;'>";
                echo "<div class='row' style='margin-top: 0.5em;'>";
                echo "<div class='col-3'>";
                echo "<div class='user-information'>";
                echo "<img src='images/" . ($userLevel > 0 ? "wizard" : "user") . ".jpg' class='wizard'>";
                echo "<h6 class='leave' style='font-size: small; line-height: 2;text-align: center;'>嚮導等級 </h6>";
                echo "</div></div>";
                echo "<div class='col-2'><div class='score'>";
                for ($i = 1; $i <= 5; $i++) {
                  // 判斷目前的星星數是否小於或等於實際評分
                  $starImage = ($i <= $starCount) ? 'star-y.png' : 'star-w.png'; // 如果小於等於評分，顯示黄色星星，否則顯示空白星星
                  echo "<img class='star" . $i . "' src='images/" . $starImage . "' style='width: 1em;'>";
                }
                echo "</div></div>";
                echo "<div class='col-2'><h6 class='time' style='font-size: small; line-height: 2;'>" . $comment['time'] . "</h6></div>";
                echo "</div><div class='row' style='margin-bottom: 0.5em;'><div><div class='message-font'>" . htmlspecialchars($comment['contents']) . "</div></div></div>";
                echo "</div>";
            }
            ?>
            </div>
            <!--關鍵字-->  
            <div class="row">
              <div class="keywords-group" style="margin-top: 1em;vertical-align: top;">
                <img class="keywords" src="images/keywords.jpg" style="display: inline-block;">
                <h4 style="position: relative;display: inline-block;text-align: left;font-weight: bold; ">關鍵字</h4>
              </div>
            </div>
            <!--關鍵字 常用-->
            <div class="row">
              <div class="CommonlyUsed" style="display: flex; align-items: center;">
                <h6 style="width: 2.7em; margin-top: 0.6em; font-size: large;">常用</h6>
                <img class="arrow" src="images/arrow.jpg">
                <!--關鍵字TAG--> <!--動態生成"關鍵字組" 可參考254、255、256-->
                <div class="keywords-tag"><?php echo htmlspecialchars($location['city']); ?></div><!--填入"所在市區"-->
                <div class="keywords-tag"><img class="keywords-tag-icon" src="images/restaurant.png"><?php echo htmlspecialchars($storeInfo['tag']); ?></div><!--填入"餐廳種類"-->
                <div class="keywords-tag">$1-200</div>
              </div>
            </div>
            <!--關鍵字 分類--> 
            <div class="row" style="margin-top: 1em;">
            <div class="CommonlyUsed" style="display: flex; align-items: center;">
            <h6 style="width: 2.7em; margin-top: 0.6em; font-size: large;">分類</h6>
            <img class="arrow" src="images/arrow.jpg">
                <!-- 關鍵字TAG -->
                <div class="stores-keywords" style="display: flex; align-items: center; flex-wrap: nowrap; overflow-x: auto; ">
                <?php if (!empty($allKeywords)): ?>
                  <div class="keywords-container" style="display: flex;">
                  <?php foreach ($allKeywords as $keyword): ?>
                    <div class="keywords-tag"><?php echo htmlspecialchars($keyword['word']); ?></div>
                    <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                      <p>No keywords found for this store.</p>
                      <?php endif; ?>
                      </div>
                      </div>
                    </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!--底部欄-->
  <div class="bottom">
      台北商業大學 | 資訊管理系<br>
      北商資管專題 113206 小組<br>
      成員：鄧惠中、余奕博、邱綺琳、陳彥瑾
      <en style="margin-right: 0.6em; float: right; font-size: xx-small">Copyright ©2024 All rights reserved.</en>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="scripts/ui-interactions.js"></script>
</body>

</html>
