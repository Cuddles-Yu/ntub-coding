<!doctype html>
<html> <!-- 網頁詳細頁面 -->

<head>
  <meta charset="utf-8" />
  <title>店家詳細內容</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <meta name="keywords" content="評價, google map" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-solid-rounded/css/uicons-solid-rounded.css'>

  <!-- 載入 leaflet.css -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin="" />

  <!-- 載入 leaflet.awesome-markers.css -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.css" />

  <!-- 載入 MarkerCluster.css -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

  <!-- 載入 osm_map.css -->
  <link rel="stylesheet" type="text/css" href="./osm_map.css">

  <link rel="stylesheet" href="styles/store-detail.css" />
</head>

<body>
  <?php
  // 引入資料庫連接和查詢函數
  require 'queries.php';
  require 'analysis.php';


  // 優先使用 GET 參數
  $userId = $_GET['uid'] ?? null;
  $storeId = $_GET['id'] ?? null;

 // 如果 storeId 或 storeName 不存在，根據現有的值獲取缺失的資訊
 if (empty($storeId)) {
  Header("Location: home.html");
  exit;
}

  // 獲取店家資訊
  $storeInfo = getStoreInfoById($storeId);
  $relevants = getRelevantComments($storeId);
  $Highests = getHighestComments($storeId);
  $Lowests = getLowestComments($storeId);
  $comments = getComments($storeId);

  $location = getLocation($storeId);
  $rating = getRating($storeId);
  $service = getService($storeId);
  $keywords = getAllKeywords($storeId);
  $foodKeyword = getFoodKeyword($storeId);
  $openingHours = getOpeningHours($storeId);
  $otherBranches = getOtherBranches($storeInfo['branch_title'], $storeId);
  $positive = getPositiveKeywords($storeId);
  $negative = getNegativeKeywords($storeId);
  $subjective = getSubjectiveKeywords($storeId);
  $neutral = getNeutralKeywords($storeId);

  $targetsInfo = getTargets($storeId);

  ?>
  <!-- 頂欄 -->
  <header>
    <!-- 基本項目 -->
    <div id="web_name">
        <img src="images/Logo設計_圖像(藍+).png" id="web_logo">
        <a href="home.html">評星宇宙</a>
    </div>

    <div id="nav_menu1">
        <a class="link_text" href="home.html">網站首頁</a>
        <div class="vertical-line"></div>
        <a class="link_text" href="#">使用說明</a>
        <div class="vertical-line"></div>
        <a class="link_text" href="https://forms.gle/t7CfCTF7phHKU9yJ8" target="_blank">使用回饋</a>
        <div class="vertical-line"></div>
        <a class="link_text" href="develop-team.html">成員介紹</a>
    </div>

    <div id="user_icon" type="button">
        <img src="images/user.jpg" id="user_icon_logo">
    </div>

    <div id="login_button">
        <button id="login" href="#">登入</button>
        <button id="signup" href="#">註冊</button>
    </div>

    <!-- 漢堡圖示 -->
    <button id="hamburger_btn" class="hamburger">&#9776;</button>
    <div id="overlay"></div>
    <nav id="nav_menu2">
        <a class="link_text" href="home.html">網站首頁</a>
        <a class="link_text" href="#">使用說明</a>
        <a class="link_text" href="https://forms.gle/t7CfCTF7phHKU9yJ8" target="_blank">使用回饋</a>
        <a class="link_text" href="develop-team.html">成員介紹</a>
        <button id="login2" href="#">登入</button>
        <button id="signup2" href="#">註冊</button>
    </nav>
    <hr>
  </header>

  <section class="primary-content section-content">
    <h1 class="store-title"><?php if (isset($storeInfo['name'])) {
                              echo htmlspecialchars($storeInfo['name']);
                            } else {
                              echo "商店名稱不存在";
                            }
                            ?></h1>
    <div class="love-group">
      <div class="type-rating-status-group">
        <!--綜合評分-->
        <h5 class="rating"><?php echo getBayesianScore($userId, $storeId, $conn); ?></h5>
        <h6 class="rating-text">/綜合評分</h6>
        <div class="store-type" type="button"><?php echo htmlspecialchars($storeInfo['tag']); ?></div>
        <!--營業時間按鈕-->
        <button type="button" class="btn btn-outline-success status" data-bs-container="body" data-bs-toggle="popover2"
          data-bs-title="詳細營業時間" data-bs-placement="bottom" data-bs-html="true"
          data-bs-content="<?php
                            foreach ($openingHours as $day => $hours) {
                              echo htmlspecialchars($day) . ':<br>';
                              if (empty($hours)) {
                                echo '&nbsp;&nbsp;休息<br>';
                              } else {
                                foreach ($hours as $hour) {
                                  if ($hour['open_time'] === null || $hour['close_time'] === null) {
                                    echo '&nbsp;&nbsp;休息<br>';
                                  } else {
                                    $openTime = date('H:i', strtotime($hour['open_time']));
                                    $closeTime = date('H:i', strtotime($hour['close_time']));
                                    echo '&nbsp;&nbsp;' . htmlspecialchars($openTime) . ' - ' . htmlspecialchars($closeTime) . '<br>';
                                  }
                                }
                              }
                              echo '<hr>';
                            }
                            ?>">
          <i class="fi fi-sr-clock status-img"></i>營業中
        </button>

        <!--分店綜合評分比較-->
        <?php if (!empty($otherBranches)) { ?>
          <!-- 顯示 "其他分店" 按鈕 -->
          <button class="btn btn-outline-secondary other-store-rating" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            其他分店
          </button>
          <h6 class="update">資料更新時間：2024/07/16 13:50<!--填入資料更新時間--></h6>
      </div>
      <a class="love" href="#"><img class="love-img" src="images/love.png"></a>
    </div>
    <div class="collapse multi-collapse" id="collapseExample">
      <div class="other-store">

      <?php
          // 顯示每個分店的資訊
          foreach ($otherBranches as $branch) {
            $storeId = htmlspecialchars($branch['id']);
            $storeName = htmlspecialchars($branch['name']);
            $branchName = htmlspecialchars($branch['branch_name']);
            $branchId = htmlspecialchars($branch['id']); // 取得分店的 ID
            $avgRating = htmlspecialchars($branch['avg_ratings']);
            $address = htmlspecialchars(($branch['city'] ?? '') . ($branch['dist'] ?? '')  . ($branch['vil'] ?? '') . ($branch['details'] ?? ''));

            echo '<div class="other-store-display">';
            echo '<a class="other-store-group col-11" href="store-detail.php?id='  . $storeId . '">'; // 連結到分店詳細頁面
            echo '<li class="store-name col-4">' . $branchName . '</li>';
            echo '<p class="other-rating col-3">' . $avgRating . ' /綜合評分</p>';
            echo '<p class="other-map address col"><i class="fi fi-sr-map-marker address-img"></i>' . $address . '</p>';
            echo '</a>';
            echo '<i class="fi fi-sr-bookmark collect" role="button"></i>';
            echo '</div>';
          }
        } ?>

      </div>
    </div>
    <!-- 商家介紹 (根據使用者需求而產生的介紹(每個人看到的不一樣))-->
    <li id="item" class="introduction" data-content="這是一間熱門度高、環境中等、服務很好的店家"></li> 
  </section>

  <section class="secondary-content section-content">
    <div class="first-row">
      <div class="anaysis col">
        <div class="title-group">
          <i class="fi fi-sr-bars-progress group-title-img"></i>
          <h5 class="group-title">指標分析</h5>
        </div>
        <table class="table table-borderless">
          <thead>
            <tr class="row1">
              <th scope="col" class="col1"></th>
              <th scope="col" class="col2">正面</th>
              <th scope="col" class="col3">負面</th>
              <th scope="col" class="col4">喜好</th>
              <th scope="col" class="col5">中性</th>
              <th scope="col" class="col6">評價總數</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              $categories = [
                  $_ENVIRONMENT => ['weight' => '30', 'color' => '#562B08'], 
                  $_PRODUCT => ['weight' => '30', 'color' => '#7B8F60'],
                  $_SERVICE => ['weight' => '30', 'color' => '#5053AF'],
                  $_PRICE => ['weight' => '30', 'color' => '#C19237'],
              ];
              uasort($categories, function($a, $b) {
                return $b['weight'] <=> $a['weight'];
              });
              $rowIndex = 1;
            ?>
            <?php foreach ($categories as $category => $data): ?>
              <?php 
                $result = getProportionScore($category);
                $proportion = $result['proportion'];
                $score = $result['score'];
              ?>
              <tr class="row<?= $rowIndex ?>">
                  <td>
                      <div class="progress-group">
                          <h6 class="progress-text" style="color: <?= $data['color'] ?>;"><?= $category ?></h6>
                          <h6 class="progress-percent" style="color: <?= $data['color'] ?>;"><?= $score ?></h6>
                      </div>
                      <div class="progress col" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                          <div class="progress-bar overflow-visible" style="width: <?= $proportion.'%' ?>; background-color: <?= $data['color'] ?>;"></div>
                      </div>
                  </td>
                  <td class="evaluate-good"><?= $targetsInfo[$_POSITIVE][$category] ?? 'NULL' ?></td>
                  <td class="evaluate-bad"><?= $targetsInfo[$_NEGATIVE][$category] ?? 'NULL' ?></td>
                  <td class="evaluate-prefer"><?= $targetsInfo[$_PREFER][$category] ?? 'NULL' ?></td>
                  <td class="evaluate-neutral"><?= $targetsInfo[$_NEUTRAL][$category] ?? 'NULL' ?></td>
                  <td class="evaluate-all"><?= $targetsInfo[$_TOTAL][$category] ?? 'NULL' ?></td>
              </tr>
              <?php $rowIndex++; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div> 
      <div class="divider-line"></div>
      <div class="service col">
        <div class="title-group">
          <i class="fi fi-sr-following group-title-img"></i>
          <h5 class="group-title">服務項目</h5>
        </div>
        <div class="service-item-group">
          <?php
          // 定義顯示順序
          $order = ['服務項目', '付款方式', '規劃', '無障礙程度', '停車場', '設施', '其他'];
          foreach ($order as $category) {
            if (isset($service[$category])) {
              $serviceItems = $service[$category];
              echo '<div class="service-group">';
              echo '<h6 class="service-title">' . htmlspecialchars($category) . '</h6>';
              echo '<div class="item-group">';

              foreach ($serviceItems as $serviceItem) {
                if ($serviceItem['state'] == 1) {
                  echo '<div class="service-item">';
                  echo '<i class="fi fi-sr-checkbox item-img"></i>';
                  echo '<h6 class="item-text">' . htmlspecialchars($serviceItem['property']) . '</h6>';
                  echo '</div>';
                } else {
                  echo '<div class="service-item">';
                  echo '<i class="fi fi-sr-square-x item-img"></i>';
                  echo '<h6 class="item-text">' . htmlspecialchars($serviceItem['property']) . '</h6>';
                  echo '</div>';
                }
              }
              echo '</div>';  // item-group 結束
              echo '</div>';  // service-group 結束
            }
          } ?>
        </div>
      </div>
    </div>
  </section>

  <section class="tertiary-content section-content">
    <div class="comment-title">
      <div class="title-group">
        <i class="fi fi-sr-comment-alt group-title-img"></i>
        <h5 class="group-title">評論</h5>
      </div>

      <!--樣本選擇按鈕-->
      <div class="btn-group sample-group" role="group" aria-label="Basic radio toggle button group">
        <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
        <label class="btn btn-outline-secondary sample-button" for="btnradio1">全部</label>

        <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
        <label class="btn btn-outline-secondary sample-button" for="btnradio2">最相關</label>

        <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
        <label class="btn btn-outline-secondary sample-button" for="btnradio3">評分最高</label>

        <input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off">
        <label class="btn btn-outline-secondary sample-button" for="btnradio4">評分最低</label>
      </div>
      <button type="button" class="btn question" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="留言樣本選擇" data-bs-custom-class="custom-tooltip1">
        <i class="fi fi-sr-interrogation question-img"></i>
      </button>
    </div>
    <div class="comment-keyword">
      <div class="keyword-title">
        <h5 class="keyword-title-text">關鍵字</h5>
        <!--篩選按鈕-->
        <div class="input-group mb-3 filter-button">
          <span class="input-group-text" id="basic-addon1"><i class="fi fi-sr-filter-list"></i></i>篩選</span>
          <select class="form-select" aria-label="Default select example" id="filterSelect">
            <option value="all" selected>全部</option>
            <option value="氛圍">氛圍</option>
            <option value="產品">產品</option>
            <option value="服務">服務</option>
            <option value="售價">售價</option>
          </select>
        </div>
      </div>
      <div class="group-gb good-side">
        <h6 class="title-gb">正面<i class="fi fi-sr-caret-right keyword-arrow"></i></h6>
        <div class="group-keyword">
          <!--動態生成 正面標籤-->
          <!-- 動態生成關鍵字 -->
          <?php foreach ($positive as $index => $keyword): ?>
            <div class="keywords">
              <!-- 按鈕 -->
              <button type="button" class="btn comment-good" data-bs-toggle="modal" data-bs-target="#goodModal<?php echo $index; ?>"><!--依據動態生成的順序修改data-bs-target與展開內容的id數字-->
                <?php echo htmlspecialchars($keyword['object']); ?> (<?php echo htmlspecialchars($keyword['count']); ?>)
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="group-gb bad-side">
        <h6 class="title-gb">負面<i class="fi fi-sr-caret-right keyword-arrow"></i></h6>
        <div class="group-keyword">
          <!--動態生成 負面標籤-->
          <!-- 動態生成關鍵字 -->
          <?php foreach ($negative as $index => $keyword): ?>
            <div class="keywords">
              <!-- 按鈕 -->
              <button type="button" class="btn comment-bad" data-bs-toggle="modal" data-bs-target="#badModal<?php echo $index; ?>"><!--依據動態生成的順序修改data-bs-target與展開內容的id數字-->
                <?php echo htmlspecialchars($keyword['object']); ?> (<?php echo htmlspecialchars($keyword['count']); ?>)
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="group-gb neutral-side">
        <h6 class="title-gb">喜好<i class="fi fi-sr-caret-right keyword-arrow"></i></h6>
        <div class="group-keyword">
          <!--動態生成 喜好標籤-->
          <!-- 動態生成關鍵字 -->
          <?php foreach ($subjective as $index => $keyword): ?>
            <div class="keywords">
              <!-- 按鈕 -->
              <button type="button" class="btn comment-neutral" data-bs-toggle="modal" data-bs-target="#neutralModal<?php echo $index; ?>"><!--依據動態生成的順序修改data-bs-target與展開內容的id數字-->
                <?php echo htmlspecialchars($keyword['object']); ?> (<?php echo htmlspecialchars($keyword['count']); ?>)
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="group-gb middle-side">
        <h6 class="title-gb">中性<i class="fi fi-sr-caret-right keyword-arrow"></i></h6>
        <div class="group-keyword">

          <!-- 動態生成關鍵字 -->
          <?php foreach ($neutral as $index => $keyword): ?>
            <div class="keywords">
              <!-- 按鈕 -->
              <button type="button" class="btn comment-middle" data-bs-toggle="modal" data-bs-target="#middleModal<?php echo $index; ?>"><!--依據動態生成的順序修改data-bs-target與展開內容的id數字-->
                <?php echo htmlspecialchars($keyword['object']); ?> (<?php echo htmlspecialchars($keyword['count']); ?>)
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="keyword-title">
      <h5 class="keyword-title-text">留言 <?php echo count($comments); ?>則</h5><!--括號填入留言數量-->
      <!--排序按鈕-->
      <div class="input-group mb-3 sort-button">
        <span class="input-group-text" id="basic-addon1"><i class="fi fi-sr-sort-amount-down"></i>排序</span>
        <select class="form-select" aria-label="Default select example" id="sortSelect">
          <option value="相關性" selected>相關性</option>
          <option value="由高至低">由高至低</option>
          <option value="由低至高">由低至高</option>
        </select>
        <span class="input-group-text" id="inputGroup-sizing-default">篩選</span>
        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
        <button class="btn btn-outline-secondary" type="button" id="button-addon2">搜尋</button>
      </div>
    </div>


    <div class="comment-group" id="commentGroup">
      <?php foreach ($comments as $index => $comment): ?>
        <div class="comment-item" data-rating="<?php echo $comment['rating']; ?>" data-index="<?php echo $index; ?>">
          <div class="comment-information">
            <img class="avatar" src="images/<?php echo $comment['contributor_level'] == 0 ? 'user.jpg' : 'wizard.jpg'; ?>"><!--若留言為嚮導 則src為images/wizard.jpg 若不是嚮導則src為images/user.jpg-->
            <div class="star-group">
              <?php for ($i = 0; $i < 5; $i++): ?>
                <img class="star" src="images/<?php echo $i < $comment['rating'] ? 'star-y.png' : 'star-w.png'; ?>">
              <?php endfor; ?><!--滿星src為images/star-y.png 空星src為images/star-w.png-->
            </div>
            <p class="time">時間：<?php echo htmlspecialchars($comment['time']); ?></p>
          </div>
          <div class="comment">
            <p class="comment-text"><?php echo htmlspecialchars($comment['contents']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    
  </section>

  <section class="fourth-content section-content">
    <div class="title-group">
      <i class="fi fi-sr-hand-holding-heart group-title-img"></i>
      <h5 class="group-title">吃過都推薦</h5>
      <button type="button" class="btn question-2" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="點擊文字搜尋餐點餐廳<br>點擊圖片搜尋餐點照片" data-bs-custom-class="custom-tooltip1" data-bs-html="true">
        <i class="fi fi-sr-interrogation question-img"></i>
      </button>
    </div>
    <div class="carousel-container">
      <div class="carousel-arrow left-arrow" type="button"><i class="fi fi-sr-angle-left"></i></div>
      <div class="group-card">
        <!--推薦食物-->
        <?php if ($foodKeyword): ?>
          <?php foreach ($foodKeyword as $foodKeyword): ?>
            <div class="card">
              <div class="card-body">
                <a class="card-text" href="search.html?keyword=<?php echo urlencode($foodKeyword['word']); ?>" target="_blank"><?php echo htmlspecialchars($foodKeyword['word']); ?>(<?php echo htmlspecialchars($foodKeyword['count']); ?>)</a><!--填入推薦食物名稱 href填入此食物的評星宇宙搜尋結果網址-->
              </div>
              <a href="https://www.google.com/search?udm=2&q=<?php echo urlencode($storeInfo['name'] . ' ' . $foodKeyword['word']); ?> " target="_blank"><img class="card-img" src="<?php echo $foodKeyword['image_url'] ?>"><!--src填入推薦食物照片連結 href填入搜尋此食物的google連結--></a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="card">
            <div class="card-body">
              <a class="card-text" target="_blank">暫無推薦餐點</a>
            </div>
            <a target="_blank"><img class="card-img" src="images/預設.jpg"><!--src填入推薦食物照片連結 href填入搜尋此食物的google連結--></a><!--暫無推薦餐點照片 先放水餃照片代替-->
          </div>
        <?php endif; ?>
      </div>
      <div class="carousel-arrow right-arrow" type="button"><i class="fi fi-sr-angle-right"></i></div>
    </div>
  </section>
  <section class="fifth-content section-content">
    <div class="introduction-display">
      <div class="store-introduction">
        <div class="title-group">
          <i class="fi fi-sr-search-alt group-title-img"></i>
          <h5 class="group-title ">餐廳資訊</h5>
        </div>
        <div class="introduction-item-group">
          <div class="store-introduction-group">
            <li class="introduction-title">地址</li>
            <li class="introduction-item"><a class="introduction-item" onclick="navigateToStore(<?php echo htmlspecialchars(json_encode($location['latitude'])); ?>, <?php echo htmlspecialchars(json_encode($location['longitude'])); ?>)">
                <?php echo htmlspecialchars($location['city'] . '' . $location['dist'] . '' . $location['vil'] . '' . $location['city'] . '' . $location['details']); ?></a></li>
          </div>
          <div class="store-introduction-group">
            <li class="introduction-title">電話</li>
            <li class="introduction-item"><?php echo htmlspecialchars($storeInfo['phone_number']); ?></li>
          </div>
          <div class="store-introduction-group">
            <li class="introduction-title">相關網站</li>
            <li class="introduction-item"><a class="introduction-item" href="<?php echo htmlspecialchars($storeInfo['website']); ?>" target="_blank">
                <?php echo htmlspecialchars($storeInfo['website']); ?></a></li>
          </div>
        </div>
      </div>
      <div id="map" class="map">
    </div>
    
  </section>

  <section class="sixth-content section-content">
    <div class="title-group">
      <i class="fi fi-sr-interactive group-title-img"></i>
      <h5 class="group-title ">最多人提到</h5>
    </div>
    <div class="carousel-container-tag">
      <div class="carousel-arrow-tag left-arrow-2" type="button"><i class="fi fi-sr-angle-left"></i></div>

      <div class="tag-group">
        <?php if ($keywords) {
          foreach ($keywords as $keyword) {
            if ($keyword['count'] > 1) { // 檢查 count 的數量是否大於 1
              echo '<button class="tag btn-outline-secondary btn" type="button">';
              echo '<a class="tag-text" href="search.html?keyword=' . urlencode($keyword['word']) . '" target="_blank">' . htmlspecialchars($keyword['word']) . ' (' . htmlspecialchars($keyword['count']) . ')</a>'; //<!--填入關鍵字 href填入此的評星宇宙搜尋結果網址-->
              echo '</button>';
            }
          }
        } else {
          echo '<div class="tag">';
          echo '<p class="tag-text">無相關關鍵字</p>';
          echo '</div>';
        } ?>
      </div>
      <div class="carousel-arrow-tag right-arrow-2" type="button"><i class="fi fi-sr-angle-right"></i></div>
  </section>


  <!--底部欄-->
  <footer>
    <div class="bottom">
      台北商業大學 | 資訊管理系<br>
      北商資管專題 113206 小組<br>
      成員：鄧惠中、余奕博、邱綺琳、陳彥瑾
      <en style="margin-right: 9.6px; float: right; font-size: 9.6px;">Copyright ©2024 All rights reserved.</en>
    </div>
  </footer>
  <script>
    document.getElementById('sortSelect').addEventListener('change', function() {
      const sortValue = this.value;
      const commentGroup = document.getElementById('commentGroup');
      const comments = Array.from(commentGroup.getElementsByClassName('comment-item'));

      comments.sort((a, b) => {
        const ratingA = parseInt(a.getAttribute('data-rating'));
        const ratingB = parseInt(b.getAttribute('data-rating'));
        const indexA = parseInt(a.getAttribute('data-index'));
        const indexB = parseInt(b.getAttribute('data-index'));

        if (sortValue === '由高至低') {
          return ratingB - ratingA;
        } else if (sortValue === '由低至高') {
          return ratingA - ratingB;
        } else {
          return indexA - indexB; // 相關性排序，根據原始順序
        }
      });

      comments.forEach(comment => commentGroup.appendChild(comment));
    });
  </script>

<script>
    var storeId = <?php echo json_encode($storeId); ?>;
  </script>

<script>
function navigateToStore(storeLat, storeLng) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;
            var googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${storeLat},${storeLng}`;
            window.open(googleMapsUrl, '_blank');
        }, function(error) {
            alert('無法取得您的位置: ' + error.message);
        });
    } else {
        alert('您的瀏覽器不支援地理定位功能。');
    }
}
</script>

  <!-- 載入地圖框架 leaflet.js -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <!-- 載入 leaflet.awesome-markers.min.js -->
  <script src="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.min.js"></script>

  <!-- 載入 Font Awesome Kit -->
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>

  <!-- 載入 Markercluster.js -->
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

  <!-- 載入主程式 osm_map.js -->
  <script src="./storedetail-landmark.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="scripts/ui-interactions.js"></script>

  <script src="scripts/store-detail.js"></script>
</body>

</html>