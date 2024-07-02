<?php
require "db.php";

function getPopularStores($location, $count)
{
    global $conn;
    $sql = "   SELECT DISTINCT s.name, r.real_rating, r.total_comments, l.city, l.details, s.tag, r.environment_rating, r.product_rating, r.service_rating, r.price_rating FROM stores AS s
        INNER JOIN keywords AS k ON s.id = k.store_id
        INNER JOIN rates AS r ON s.id = r.store_id
        INNER JOIN locations AS l ON s.id = l.store_id
        INNER JOIN tags AS t ON s.tag = t.tag
        ORDER BY total_ratings DESC
        LIMIT 50;
    ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        // 從結果中隨機選取30個
        if (count($rows) > 30) {
            $random_keys = array_rand($rows, 30);
            $random_rows = [];
            foreach ($random_keys as $key) {
                $random_rows[] = $rows[$key];
            }
        } else {
            $random_rows = $rows; // 如果結果少於30個，則返回全部結果
        }
        return $random_rows;
    }
}

$location = "台北市";
$popularStores = getPopularStores($location, 30);

?>

<div class="accordion" id="accordionPanelsStayOpenExample" style="margin-top: 0.625em;">
  <div class="accordion-item">
    <h2 class="accordion-header">
        <div class="accordion-button" style="background-color: #EBEBEB;">
            熱門推薦
        </div>
    </h2>
    <div id="panelsStayOpen-collapse" class="accordion-collapse collapse show" style="overflow: scroll; overflow-x: hidden; max-height: 34vh;">
        <?php foreach ($popularStores as $index => $store): ?>
            <div class="accordion-body" style="background-color: #E7EAEE; margin: 0.625em; border-radius: 15px;"onclick="navigateToDetail(this);">
                <div class="container text-center" style="display: contents;">
                  <div class="row">
                    <div class="col-4" style="text-align: left;">
                      <div class="row">
                        <!-- 店家資訊-左側 -->
                        <div style="font-size: large; font-weight: bold;"><?php echo htmlspecialchars(
                            $store["name"]
                        ); ?></div><!--填入"商家名稱"-->
                        <button type="button" class="btn btn-danger" style="width: 5.9vw; margin-left: 0.5vw; font-size: x-small;">綜合評分 85 分</button><!--填入"綜合名稱"-->
                      </div>
                      <div class="row">
                      <div class="score">
                      <?php
                      $realRating = isset($store["real_rating"])
                          ? $store["real_rating"]
                          : 0; // 確保有分數，否則默認為0
                      for ($i = 1; $i <= 5; $i++):
                          $starImage =
                              $i <= $realRating
                                  ? "star-y.png"
                                  : "star-w.png"; ?>
                      <img class="star<?php echo $i; ?>" src="images/<?php echo $starImage; ?>">
                      <?php
                      endfor;
                      ?>
                      </div>
                      </div>
                      <!--標籤-->
                      <div class="row">
                        <div class="tag" style="margin-top:1em;">
                          <h7><?php echo htmlspecialchars(
                              $store["tag"]
                          ); ?></h7><!--填入"餐廳標籤·地址"-->
                        </div>
                      </div>
                      <!--地址-->
                      <div class="row">
                        <div class="tag" style="margin-top:1em;">
                          <h7><?php echo htmlspecialchars(
                              $store["city"] . " " . $store["details"]
                          ); ?></h7><!--填入"餐廳標籤·地址"-->
                        </div>
                      </div>
                    </div>
                        
                    <div class="col-5">
                      <!--progress組-中間-->
                      <div class="standard-group" style="display: contents;">
                        <div class="standard-text" style="position: relative; float: left">
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
                              style="width: 20%; background-color: #562B08; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                              20%</div><!--填入"環境指數"width也要跟著改-->
                          </div>
                          <div class="progress" role="progressbar" aria-label="Info example" aria-valuenow="50" aria-valuemin="0"
                            aria-valuemax="100" style="background-color: #d9d9d9; margin-top: 0.55em; margin-bottom: 0.375em;">
                            <div class="progress-bar"
                              style="width: <?php echo htmlspecialchars(
                                  $store["product_rating"]
                              ); ?>%; background-color: #7B8F60;text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                              <?php echo htmlspecialchars(
                                  $store["product_rating"]
                              ); ?>%</div><!--填入"產品指數"width也要跟著改-->
                          </div>
                          <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="75"
                            aria-valuemin="0" aria-valuemax="100"
                            style="background-color: #d9d9d9;  margin-top: 0.57em; margin-bottom: 0.4375em;">
                            <div class="progress-bar"
                              style="width: <?php echo htmlspecialchars(
                                  $store["service_rating"]
                              ); ?>%; background-color: #5053AF; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                              <?php echo htmlspecialchars(
                                  $store["service_rating"]
                              ); ?>%</div><!--填入"服務指數"width也要跟著改-->
                          </div>
                          <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100"
                            aria-valuemin="0" aria-valuemax="100"
                            style="background-color: #d9d9d9; margin-top: 0.60em; margin-bottom: 0.375em;">
                            <div class="progress-bar"
                              style="width: <?php echo htmlspecialchars(
                                  $store["price_rating"]
                              ); ?>%; background-color: #C19237; text-align: left; padding-left: 0.625em; font-size: 0.8em;">
                              <?php echo htmlspecialchars(
                                  $store["price_rating"]
                              ); ?>%</div><!--填入"售價指數"width也要跟著改-->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <!--部分快捷-右側-->
                      <div class="right-box">
                        <img src="images/love.png"><a href="#"
                          style="color: #374957; text-decoration: none; font-weight: bold;">&nbsp;最愛</a><br>
                        <img src="images/map.png"><a href="https://maps.google.com/?q=<?php echo urlencode(
                            $store["name"]
                        ); ?>"
                          style="color: #374957; text-decoration: none; font-weight: bold;" target="_blank">&nbsp;地圖</a><br><!--第302行 a href="#" 改為店家地圖-->
                        <img src="images/web.png"><a href="#" 
                          style="color: #374957; text-decoration: none; font-weight: bold;" target="_blank">&nbsp;官網</a><!--第304行 a href="#" 改為店家網址-->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>            
        </div>
    </div>
</div>