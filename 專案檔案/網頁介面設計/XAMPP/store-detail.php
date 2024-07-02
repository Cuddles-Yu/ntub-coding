<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>店家詳細內容</title>
  <link rel="stylesheet" href="styles/store-detail-styles.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <meta name="keywords" content="評價, google map" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
</head>
<body style="margin-top: 80px; background-color: #ffffff">

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
$comments = getComments($storeName);
$location = getLocation($storeName);
$rating = getRating($storeName);

?>

<!--頁首-->
<div class="fixed-top"><img src="images/icon.png" class="team-icon">評星宇宙
  <!--主選單-->
  <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
    aria-controls="offcanvasRight" style="float: right; background-color: #485465; border-color: #485465">
    <img class="Gutters-icon" src="images/Gutters.png" /></button>
  <!--選單內頁-->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel"
    style="background-color: #485465">
    <div class="offcanvas-header">
      <img class="header-icon2" src="images/icon2.png">
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="Options">
        <li><img class="Options-icon" src="images/home.png" />主頁</li>
        <li><img class="Options-icon" src="images/people.png" />會員專區</li>
        <li><img class="Options-icon" src="images/schedule.png" />行程規劃</li>
      </ul>
      <div class="line"></div>
      <ul class="Options2">
        <li><img class="Options2-icon" src="images/!.png" />使用說明</li>
        <li><img class="Options2-icon" src="images/mail.png" />問題回饋</li>
        <li><img class="Options2-icon" src="images/team.png" />成員介紹</li>
      </ul>
    </div>
  </div>
</div>

<!--MAP、店家詳細資訊框架-->
<div class="container-fluid">
  <div class="row">
    <!--MAP--><!--"API串聯"-->
    <div class="col-12 col-md-6 col-lg-7" style="height: 45em;">
      <div id="map">map</div>
    </div>
    <!--店家詳細資訊-->
    <div class="col-12 col-md-6 col-lg-5">
      <div class="title">
        <div class="row">
          <div class="col-5">
            <div class="label">
              <h3><?php echo htmlspecialchars($storeInfo['name']); ?></h3>
            </div>
          </div>
          <div class="col-2">
            <div class="tag">
              <div class="title-tag" style="background-color: #6A6A6A;border-radius: 10px;color: white;width: 6em;height: 2em;vertical-align: middle;">
                <img src="images/restaurant-2.png" style="width: 1em;"><?php echo htmlspecialchars($storeInfo['category']); ?>
              </div>
            </div>
          </div>
        </div>
        <!--餐廳星數-->
        <div class="row">
          <div class="score">
            <?php
            $avgRating = isset($rating['avg_ratings']) ? $rating['avg_ratings'] : 0;
            for ($i = 1; $i <= 5; $i++) {
              echo '<img class="star' . $i . '" src="images/star.png"' . ($i <= $avgRating ? '' : ' style="visibility: hidden;"') . '>';
            }
            ?>
          </div>
        </div>
      </div>
      <!--地址、電話、網頁與四大評分欄 框架-->
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-6 col-lg-5" style="text-align: left;">
            <div class="information" style="font-size: small; line-height: 3;">
              <ul class="text-left" style="padding-left: 0;">
                <li>地址: <a href="https://maps.google.com/?q=<?php echo urlencode($location['store_id']); ?>">
                          <?php echo htmlspecialchars($location['postal_code'] . '' . $location['dist'] . '' . $location['vil'] . '' . $location['city'] . '' . $location['details']); ?></a></li>
                <li>電話: <?php echo htmlspecialchars($storeInfo['phone_number']); ?></li>
                <li>網頁: <a href="<?php echo htmlspecialchars($storeInfo['website']); ?>">
                          <?php echo htmlspecialchars($storeInfo['website']); ?></a></li>
              </ul>
            </div>
          </div>
          <!--五大評分欄-->
          <div class="col-12 col-md-6 col-lg-7">
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
                  style="width: 10%; background-color: #B45F5F; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                  10%</div><!--填入"熱門指數"width也要跟著改-->
              </div>
              <div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="25"
                aria-valuemin="0" aria-valuemax="100"
                style="background-color: #d9d9d9; margin-bottom: 0.375em;">
                <div class="progress-bar "
                  style="width: 25%; background-color: #562B08; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                  25%</div><!--填入"環境指數"width也要跟著改-->
              </div>
              <div class="progress" role="progressbar" aria-label="Info example" aria-valuenow="50" aria-valuemin="0"
                aria-valuemax="100" style="background-color: #d9d9d9; margin-top: 0.55em; margin-bottom: 0.375em;">
                <div class="progress-bar"
                  style="width: 50%; background-color: #7B8F60;text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                  50%</div><!--填入"產品指數"width也要跟著改-->
              </div>
              <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="75"
                aria-valuemin="0" aria-valuemax="100"
                style="background-color: #d9d9d9;  margin-top: 0.57em; margin-bottom: 0.4375em;">
                <div class="progress-bar"
                  style="width: 75%; background-color: #5053AF; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                  75%</div><!--填入"服務指數"width也要跟著改-->
              </div>
              <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100"
                aria-valuemin="0" aria-valuemax="100"
                style="background-color: #d9d9d9; margin-top: 0.60em; margin-bottom: 0.375em;">
                <div class="progress-bar"
                  style="width: 80%; background-color: #C19237; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                  80%</div><!--填入"售價指數"width也要跟著改-->
              </div>
            </div>
          </div>
          <!--內用、外帶、外送-->
          <div class="row">
            <div class="col">
              <div class="state" style="text-align: left; vertical-align: top; font-size: small;">
                <div class="state-1"><img class="state-icon" src="images/YES.png">內用</div><!--選擇是否"內用"ICON 改img的src-->
                <div class="state-2"><img class="state-icon" src="images/YES.png">外帶</div><!--選擇是否"外帶"ICON 改img的src-->
                <div class="state-3"><img class="state-icon" src="images/NO-2.jpg">外送</div><!--選擇是否"外送"ICON 改img的src-->
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
          <div class="row overflow-auto" style="height: 17em;">
            <?php
            foreach ($comments as $comment) {
              $starCount = $comment['rating'];
              $userLevel = isset($comment['user_level']) ? $comment['user_level'] : 0;
              echo "<div class='group' style='margin-top: 0.625em; background-color: #F9F9F9; border-radius: 15px;'>";
              echo "<div class='row' style='margin-top: 0.5em;'>";
              echo "<div class='col-3'>";
              echo "<div class='user-information'>";
              echo "<img src='images/" . ($userLevel > 0 ? "wizard" : "user") . ".jpg' class='wizard'>";
              echo "<h6 class='leave' style='font-size: small; line-height: 2;text-align: center;'>嚮導等級 " . $userLevel . "</h6>";
              echo "</div></div>";
              echo "<div class='col-2'><div class='score'>";
              for ($i = 1; $i <= 5; $i++) {
                echo "<img class='star" . $i . "' src='images/star.png' style='width: 1em; " . ($i > $starCount ? "visibility: hidden;" : "") . "'>";
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
              <h4 style="position: relative;display: inline-block;text-align: left;font-weight: bold;">關鍵字</h4>
            </div>
          </div>
          <!--關鍵字 常用-->
          <div class="row">
            <div class="CommonlyUsed" style="display: flex; align-items: center;">
              <h6 style="width: 2.7em; margin-top: 0.6em; font-size: large;">常用</h6>
              <img class="arrow" src="images/arrow.jpg">
              <div class="keywords-tag"><?php echo htmlspecialchars($location['city']); ?></div>
              <div class="keywords-tag"><img class="keywords-tag-icon" src="images/restaurant.png"><?php echo htmlspecialchars($storeInfo['category']); ?></div>
              <div class="keywords-tag">$1-200</div>
            </div>
          </div>
          <!--關鍵字 分類-->
          <div class="row" style="margin-top: 1em;">
            <div class="CommonlyUsed" style="display: flex; align-items: center;">
              <h6 style="width: 2.7em; margin-top: 0.6em; font-size: large;">分類</h6>
              <img class="arrow" src="images/arrow.jpg">
              <div class="keywords-tag">水餃</div>
              <div class="keywords-tag">酸辣湯</div>
              <div class="keywords-tag">小菜</div>
              <div class="keywords-tag">仙草蜜</div>
              <div class="keywords-tag">排隊</div>
              <div class="keywords-tag">干貝</div>
              <div class="keywords-tag" style="border: 3px solid #584040;">...更多</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--底部欄-->
<div class="fixed-bottom">
  <div>
    <div>台北商業大學 | 資訊管理系</div>
    <div>北商資管專題 113206 小組</div>
    <div>成員：鄧惠中、余奕博、邱綺琳、陳彥瑾
      <en style="margin-right: 0.6em; float: right; font-size: 0.6em;">Copyright ©2024 All rights reserved.</en>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="scripts/ui-interactions.js"></script>
</body>
</html>
