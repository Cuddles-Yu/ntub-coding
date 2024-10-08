<?php 
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';

  // 優先使用 GET 參數
  $storeId = $_GET['id'] ?? null;

  // 如果 storeId 或 storeName 不存在，根據現有的值獲取缺失的資訊
  if (empty($storeId)) {
    header("Location: /home");
    exit;
  }

  // 獲取店家資訊
  $storeInfo = getStoreInfoById($storeId);
  if (!empty($storeInfo)) {
    $storeName = htmlspecialchars($storeInfo['name']);
    $storeMark = htmlspecialchars($storeInfo['mark']);
    $markIcon = $markOptions[$storeMark]['tagIcon'] ?? '';
    $markClass = $markOptions[$storeMark]['buttonClass'] ?? '';
    $storeTag = htmlspecialchars($storeInfo['tag']);    
    $memberServices = getMemberServiceList();
    $memberWeights = getMemberNormalizedWeight();
    $score = getBayesianScore($memberWeights, $storeId);    
    $relevants = getRelevantComments($storeId);
    $Highests = getHighestComments($storeId);
    $Lowests = getLowestComments($storeId);
    $comments = getComments($storeId);
    $location = getLocation($storeId);
    $rating = getRating($storeId);
    $service = getService($storeId);
    $keywords = getAllKeywords($storeId);
    $foodKeywords = getFoodKeyword($storeId);
    $openingHours = getOpeningHours($storeId);
    $otherBranches = getOtherBranches($storeInfo['branch_title'], $storeId);
    $positiveMarks = getMarks($storeId, $_POSITIVE);
    $negativeMarks = getMarks($storeId, $_NEGATIVE);
    $neutralMarks = getMarks($storeId, [$_PREFER, $_NEUTRAL]);
    $targetsInfo = getTargets($storeId);
  } else {
    require_once $_SERVER['DOCUMENT_ROOT'].'/error/id-not-found.php';
    exit;
  }

  $serviceOrder = ['服務項目', '付款方式', '規劃', '無障礙程度', '停車場', '設施', '其他'];
?>

<!doctype html>
<html lang="zh-TW">

<head>
  <meta charset="utf-8" />
  <title>詳細資訊 - 評星宇宙</title>
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

  <!-- 載入 /styles/osm-map.css -->
  <link rel="stylesheet" type="text/css" href="./styles/osm-map.css">

  <link rel="stylesheet" href="styles/store-detail.css" />

</head>

<body>

  <!-- ### 頁首 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <!-- ### 內容 ### -->
  <section class="primary-content section-content">
    <h1 class="store-title" id="store-title"><?=$storeName?></h1>
    <div class="love-group">
      <div class="type-rating-status-group">
        <!--綜合評分-->
        <h5 class="rating"><?=$score?></h5>
        <h6 class="rating-text">/ 綜合評分</h6>
        <a class="store-type" type="button" href="search?q=<?=$storeTag?>" target="_blank"><?=$storeTag?></a>
        <!--營業時間按鈕-->
        <button type="button" class="btn btn-outline-success status" data-bs-container="body" data-bs-toggle="popover2"
          data-bs-title="詳細營業時間" data-bs-placement="top" data-bs-html="true"
          data-bs-content="<?php
            foreach ($openingHours as $day => $hours) {
              echo htmlspecialchars($day) . ':<br>';
              if (empty($hours)) {
                echo '休息<br>';
              } else {
                foreach ($hours as $hour) {
                  if ($hour['open_time'] === null || $hour['close_time'] === null) {
                    echo '休息<br>';
                  } else {
                    $openTime = date('H:i', strtotime($hour['open_time']));
                    $closeTime = date('H:i', strtotime($hour['close_time']));
                    echo htmlspecialchars($openTime) . ' - ' . htmlspecialchars($closeTime) . '<br>';
                  }
                }
              }
              echo '<hr>';
            }
          ?>">
          <i class="fi fi-sr-clock status-img"></i>營業中
        </button>
        <?php if($storeMark): ?>
          <button type="button" class="btn <?=$markClass?> e-icon" data-bs-toggle="tooltip" 
            data-bs-placement="bottom" data-bs-title="<?=$storeMark?>餐廳" data-bs-custom-class="custom-tooltip1" data-bs-html="true">
            <?=$markIcon?>
          </button>
        <?php endif; ?>
        <?php if (!empty($otherBranches)): ?>
          <button class="btn btn-outline-secondary other-store-rating" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
            aria-expanded="false" aria-controls="collapseExample">
            其他分店
          </button>
        <?php endif; ?>
      </div>
      <a class="love" href="#"><img class="love-img" src="images/button-favorite.png"></a>
    </div>
    <div class="collapse multi-collapse" id="collapseExample">
      <div class="other-store">
        <?php foreach ($otherBranches as $branch): ?>
          <?php 
            $storeId = htmlspecialchars($branch['id']);
            $storeName = htmlspecialchars($branch['name']);
            $branchName = htmlspecialchars($branch['branch_name']);
            $branchId = htmlspecialchars($branch['id']);
            $avgRating = htmlspecialchars($branch['avg_ratings']);
            $address = htmlspecialchars(($branch['city'] ?? '').($branch['dist'] ?? '').($branch['vil'] ?? '').($branch['details'] ?? ''));
          ?>
          <div class="other-store-display">
            <a class="other-store-group col-11" href="detail?id=<?=$storeId?>"> <!-- 連結到分店詳細頁面 -->
              <li class="store-name col-4"><?=$branchName?></li>
              <p class="other-rating col-3"><?=$avgRating?> / 綜合評分</p>
              <p class="other-map address col"><i class="fi fi-sr-map-marker address-img"></i><?=$address?></p>
            </a>
            <i class="fi fi-sr-bookmark collect" role="button"></i>
          </div>
        <?php endforeach;?>
      </div>
    </div>
    <!-- 商家介紹 (根據使用者需求而產生的介紹(每個人看到的不一樣))-->
    <?php if (isset($intro)) : ?>
        <li id="item" class="introduction" data-content="<?=$intro; ?>"></li>
    <?php else : ?>
    <?php endif; ?>   
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
              <th scope="col" class="col2"><?=$_POSITIVE ?></th>
              <th scope="col" class="col3"><?=$_NEGATIVE ?></th>
              <th scope="col" class="col5"><?=$_NEUTRAL ?></th>
              <th scope="col" class="col6"><?=$_TOTAL ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $rowIndex = 1;?>
            <?php foreach ($memberWeights as $category => $data): ?>
              <?php
              $result = getProportionScore($category);
              $proportion = $result['proportion'];
              $score = $result['score'];
              ?>
              <tr class="row<?= $rowIndex ?>">
                <td>
                  <div class="progress-group">
                    <h6 class="progress-text" style="color:<?=$data['color']?>;padding-right:5px;"><?=$category?></h6>
                    <?php if($SESSION_DATA->success): ?>
                      <h6 class="progress-text" style="color:<?=$data['color']?>;padding-right:5px;"><?=$data['weight']*50?>%</h6>
                    <?php endif; ?>
                    <h6 class="progress-text" style="color:<?=$data['color']?>;">-></h6>
                    <h6 class="progress-percent" style="color:<?=$data['color']?>"><?=$score?></h6>
                    
                  </div>
                  <div class="progress col" role="progressbar" aria-label="Success example" aria-valuenow="" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar overflow-visible" style="width: <?=$proportion?>%; background-color: <?=$data['color']?>;"></div>
                  </div>
                </td>
                <td class="evaluate-good"><?= $targetsInfo[$_POSITIVE][$category] ?? 'NULL' ?></td>
                <td class="evaluate-bad"><?= $targetsInfo[$_NEGATIVE][$category] ?? 'NULL' ?></td>
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

          <?php if ($SESSION_DATA->success): ?>
            <div class="matched-services">
              <h6 class="service-title-eligible">需求項目</h6>
              <div class="item-group">
                <!-- 需求項目 動態生成 -->
                <?php foreach ($serviceOrder as $category): ?>
                  <?php if (isset($service[$category])): ?>
                    <?php $serviceItems = $service[$category]; ?>                    
                    <?php foreach ($serviceItems as $serviceItem): ?>
                      <?php 
                        $key = false;
                        foreach ($memberServices as $memberService) {
                          if (str_contains($serviceItem['property'], $memberService) || str_contains($serviceItem['category'], $memberService)) {
                            $key = true;
                            break;
                        }}
                      ?>
                      <?php if ($key): ?>
                        <!-- 顯示需求項目，state == 1 的優先顯示 -->
                        <div class="service-item matched">
                          <?php if ($serviceItem['state'] == 1): ?>
                            <i class="fi fi-sr-check item-img-eligible"></i>
                          <h6 class="item-text-eligible"><?php echo htmlspecialchars($serviceItem['property']); ?></h6>
                          <?php else: ?>
                            <i class="fi fi-sr-cross item-img"></i>
                            <h6 class="item-text"><?php echo htmlspecialchars($serviceItem['property']); ?></h6>
                          <?php endif; ?>                          
                        </div>
                        <?php unset($memberServices[$serviceItem['property']]); // 移除已顯示的服務項目 ?>
                      <?php endif; ?>
                    <?php endforeach; ?>
                    
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- 接著顯示所有其他服務項目 -->
          <?php foreach ($serviceOrder as $category): ?>
            <?php if (isset($service[$category])): ?>
              <?php $serviceItems = $service[$category]; ?>
              <div class="service-group">
                <h6 class="service-title"><?php echo htmlspecialchars($category); ?></h6>
                <div class="item-group">
                  <?php foreach ($serviceItems as $serviceItem): ?>
                    <?php if (
                      $serviceItem['property'] === '無障礙車位' || 
                      $serviceItem['property'] === '適合兒童' || 
                      $serviceItem['property'] === '氣氛悠閒') continue; ?>

                    <?php 
                      $key = false;
                      // 再次檢查是否已經顯示在需求項目中，避免重複顯示
                      foreach ($memberServices as $memberService) {
                        if (str_contains($serviceItem['property'], $memberService) || str_contains($serviceItem['category'], $memberService)) {
                          $key = true;
                          break;
                        }
                      }
                    ?>
                    <?php if (!$key): ?>
                      <!-- 其他項目顯示 -->
                      <div class="service-item">
                        <i class="fi <?php echo ($serviceItem['state'] == 1) ? 'fi-sr-checkbox' : 'fi-sr-square-x'; ?> item-img"></i>
                        <h6 class="item-text"><?php echo htmlspecialchars($serviceItem['property']); ?></h6>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>


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
      <!--### 生成Mark統計標籤 ###-->
      <?php
      $categories = [
        $_POSITIVE => ['name' => 'good', 'marks' => $positiveMarks],
        $_NEGATIVE => ['name' => 'bad', 'marks' => $negativeMarks],
        $_NEUTRAL => ['name' => 'middle', 'marks' => $neutralMarks],
      ];
      ?>
      <?php foreach ($categories as $category => $data): ?>
        <div class="group-gb <?=$data['name'] ?>-side">
          <h6 class="title-gb"><?=$category ?><i class="fi fi-sr-caret-right keyword-arrow"></i></h6>
          <div class="group-keyword">
            <?php foreach ($data['marks'] as $index => $keyword): ?>
              <div class="keywords">
                <button type="button" class="comment-<?=$data['name'] ?>" onclick="setCommentKeyword(this)" data-bs-toggle="modal" data-bs-target="#<?=$data['name'] ?>Modal<?=$index; ?>">
                  <w class="object"><?=htmlspecialchars($keyword['object']); ?></w>
                  <w class="count">(<?=htmlspecialchars($keyword['count']); ?>)</w>
                </button>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="keyword-title">
      <h5 class="keyword-title-text" id="comment-count-title"><!--動態生成留言數量--></h5> 
      <!--排序按鈕-->
      <div class="input-group mb-3 sort-button">
        <span class="input-group-text" id="basic-addon1"><i class="fi fi-sr-sort-amount-down"></i>排序</span>
        <select class="form-select comment-sort-select" aria-label="Default select example" id="sortSelect">
          <option value="相關性" selected>相關性</option>
          <option value="由高至低">由高至低</option>
          <option value="由低至高">由低至高</option>
        </select>
        <span class="input-group-text" id="inputGroup-sizing-default">篩選</span>
        <input type="text" class="form-control comment-keyword-input" id="commentKeyword" name="commentKeyword" placeholder="評論關鍵字">
        <button class="btn btn-outline-secondary" onclick="searchComments()" id="search-button">搜尋</button>
      </div>
    </div>
    <div class="comment-group" id="commentGroup" keyword=-1>
      <!-- 動態生成留言 -->
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
        <?php if ($foodKeywords): ?>
          <?php foreach ($foodKeywords as $foodKeyword): ?>
            <div class="card">
              <div class="card-body">
                <a class="card-text" href="search?q=<?=urlencode($foodKeyword['word']); ?>" target="_blank"><?=htmlspecialchars($foodKeyword['word']); ?>(<?=htmlspecialchars($foodKeyword['count']); ?>)</a><!--填入推薦食物名稱 href填入此食物的評星宇宙搜尋結果網址-->
              </div>
              <a class="card-a" href="https://www.google.com/search?udm=2&q=<?=urlencode($storeInfo['name'] . ' ' . $foodKeyword['word']); ?> " target="_blank"><img class="card-img" src="<?=$foodKeyword['image_url'] ?>"><!--src填入推薦食物照片連結 href填入搜尋此食物的google連結--></a>
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
            <li class="introduction-item"><a class="introduction-item" onclick="navigateToStore(<?=htmlspecialchars(json_encode($location['latitude'])); ?>, <?=htmlspecialchars(json_encode($location['longitude'])); ?>)">
                <?=htmlspecialchars($location['city'] . '' . $location['dist'] . '' . $location['vil'] . '' . $location['city'] . '' . $location['details']); ?></a></li>
          </div>
          <div class="store-introduction-group">
            <li class="introduction-title">電話</li>
            <li class="introduction-item"><?=htmlspecialchars($storeInfo['phone_number']); ?></li>
          </div>
          <div class="store-introduction-group">
            <li class="introduction-title">相關網站</li>
            <li class="introduction-item"><a class="introduction-item" href="<?=htmlspecialchars($storeInfo['website']); ?>" target="_blank">
                <?=htmlspecialchars($storeInfo['website']); ?></a></li>
          </div>
        </div>
      </div>
      <div id="map" class="map">
        <button type="button" id="locateButton" onclick="defaultLocate()">使用您的位置</button>
      </div>
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
              echo '<a class="tag-text" href="search?q=' . urlencode($keyword['word']) . '" target="_blank">' . htmlspecialchars($keyword['word']) . ' (' . htmlspecialchars($keyword['count']) . ')</a>'; //<!--填入關鍵字 href填入此的評星宇宙搜尋結果網址-->
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
  <section class="section-content">
    <!--資料爬蟲時間--><h6 class="update">資料更新時間：<?=$storeInfo['crawler_time'] ?></h6>
  </section>

  <!-- ### 頁尾 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>

  <script>
    function setCommentKeyword(button) {
      const objectValue = button.querySelector('.object').textContent;
      const searchInput = document.getElementById('commentKeyword');
      searchInput.value = objectValue;
      document.getElementById('search-button').click();
    }
  </script>

  <script>
    document.querySelectorAll('.home-menu').forEach(page => {
      page.removeAttribute('href');
      page.setAttribute('style', 'cursor:default;');
    });
    document.querySelectorAll('.home-page').forEach(page => {
      page.removeAttribute('href');
      page.setAttribute('style', 'cursor:default;');
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.title = `${document.getElementById('store-title').textContent.trim()}詳細資訊 - 評星宇宙`;
      searchComments();
    });
    function searchComments() {
      const storeId = <?=$storeId?>;
      const searchTerm = document.getElementById('commentKeyword').value.trim();
      const commentGroup = document.getElementById('commentGroup');
      const keywordTitleText = document.getElementById('comment-count-title');
      if (commentGroup.getAttribute('keyword') === searchTerm) return;      
      commentGroup.innerHTML = '';
      
      const formData = new FormData();
      formData.set('id', storeId);
      formData.set('q', searchTerm);
      
      fetch('struc/comment_keyword.php', {
          method: 'POST',
          credentials: 'include',
          credentials: 'same-origin',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          commentGroup.setAttribute('keyword', searchTerm);
          commentGroup.innerHTML = data.html;
          if (searchTerm === '') {
              keywordTitleText.textContent = '留言 ' + data.count + ' 則';
          } else {
              keywordTitleText.textContent = '留言搜尋結果 ' + data.count + ' 則';
          }
      })
      .catch(error => console.error('發送 fetch 請求時發生錯誤：', error));
    }
    document.getElementById('commentKeyword').addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        event.preventDefault(); // 防止表單的預設提交行為
        document.getElementById('search-button').click(); // 觸發按鈕點擊事件
      }
    });
  </script>

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
      commentGroup.scrollTo(0, 0);
    });
  </script>

  <script>
    var storeId = <?=json_encode($storeId); ?>;
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

  <!-- 載入主程式 -->
  <script src="./scripts/map.js"></script>
  <script src="./scripts/storedetail-landmark.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="scripts/ui-interactions.js"></script>

  <script src="scripts/store-detail.js"></script>

</body>

</html>