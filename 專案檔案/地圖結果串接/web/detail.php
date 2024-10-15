<?php 
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';

  // 優先使用 GET 參數
  $STORE_ID = $_GET['id'] ?? null;

  // 如果 storeId 或 storeName 不存在，根據現有的值獲取缺失的資訊
  if (empty($STORE_ID)) {
    header("Location: /home");
    exit;
  }

  // 獲取店家資訊
  $storeInfo = getStoreInfoById($STORE_ID);
  if (!empty($storeInfo)) {
    $storeName = htmlspecialchars($storeInfo['name']);
    $storeMark = htmlspecialchars($storeInfo['mark']);
    $markIcon = $markOptions[$storeMark]['tagIcon'] ?? '';
    $markClass = $markOptions[$storeMark]['buttonClass'] ?? '';
    $storeTag = htmlspecialchars($storeInfo['tag']);
    $isFavorite = isFavorite($STORE_ID);
    $memberServices = getMemberServiceList();
    $memberWeights = getMemberNormalizedWeight();
    $score = getBayesianScore($memberWeights, $STORE_ID);
    $relevants = getRelevantComments($STORE_ID);
    $Highests = getHighestComments($STORE_ID);
    $Lowests = getLowestComments($STORE_ID);
    $comments = getComments($STORE_ID);
    $location = getLocation($STORE_ID);
    $rating = getRating($STORE_ID);
    $service = getService($STORE_ID);
    $keywords = getAllKeywords($STORE_ID);
    $foodKeywords = getFoodKeyword($STORE_ID);
    $otherBranches = getOtherBranches($storeInfo['branch_title'], $STORE_ID);
    $targetsInfo = getTargets($STORE_ID);
  } else {
    require_once $_SERVER['DOCUMENT_ROOT'].'/error/id-not-found.php';
    exit;
  }

  $serviceOrder = ['服務項目', '付款方式', '規劃', '無障礙程度', '停車場', '設施', '其他'];
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <title>詳細資訊 - 評星宇宙</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-solid-rounded/css/uicons-solid-rounded.css'>  
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
  <link rel="stylesheet" href="/styles/detail.css" />
  <link rel="stylesheet" href="/styles/map.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="normal-content section-content">
    <div id="favorite-button" onclick="toggleFavorite(this,<?=$STORE_ID?>)">
      <img src="<?=$isFavorite?'/images/button-favorite-active.png':'/images/button-favorite-inactive.png';?>">
      <h1 class="store-title" id="store-title" style="margin-left:10px"><?=$storeName?></h1>
    </div>
    <div class="love-group">
      <div class="type-rating-status-group">
        <h5 class="rating"><?=$score?></h5>
        <h6 class="rating-text">/ 綜合評分</h6>
        <button class="store-type btn btn-outline-gray" type="button" onclick="openSearchPage('<?=$storeTag?>')"><?=$storeTag?></button>
        <?php require_once $_SERVER['DOCUMENT_ROOT'].'/elem/open-hour-button.php';?>
        <?php if($storeMark): ?>
          <button type="button" class="btn <?=$markClass?> e-icon" data-bs-toggle="tooltip" 
            data-bs-placement="bottom" data-bs-title="<?=$storeMark?>餐廳" data-bs-custom-class="custom-tooltip1" data-bs-html="true">
            <?=$markIcon?>
          </button>
        <?php endif; ?>
        <?php if (!empty($otherBranches)): ?>
          <button class="btn btn-solid-gray status other-store-rating" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
            aria-expanded="false" aria-controls="collapseExample">
            其他分店
            <i class="fi fi-sr-angle-small-right text-icon button-down-arrow"></i>
          </button>
        <?php endif; ?>
      </div>
    </div>
    <div class="collapse multi-collapse" id="collapseExample">
      <div class="other-store">
        <?php foreach ($otherBranches as $branch): ?>
          <?php
            $branchTitle = htmlspecialchars($branch['branch_title']);
            $branchName = htmlspecialchars($branch['branch_name']);
            $branchId = htmlspecialchars($branch['id']);
            $branchScore = getBayesianScore($memberWeights, $branchId);
            $address = htmlspecialchars(($branch['city'] ?? '').($branch['dist'] ?? '').($branch['vil'] ?? '').($branch['details'] ?? ''));
          ?>
          <div class="other-store-display">
            <div class="other-store-group" style="width:100%;height:40px;" onclick="urlToDetailPage(<?=$branchId?>)">
              <p class="store-name no-flow" style="width:14%"><?=$branchTitle?></p>
              <p style="width:2%;text-align:center;color:lightgrey;;margin:0 0;">|</p>
              <p class="store-name no-flow" style="width:14%"><?=$branchName?></p>
              <p style="width:2%;text-align:center;color:lightgrey;margin:0 0;">|</p>
              <p class="other-rating no-flow" style="width:16%;text-align:center"><?=$branchScore?> / 綜合評分</p>
              <p style="width:2%;text-align:center;color:lightgrey;margin:0 0;">|</p>
              <p class="other-map address no-flow"><i class="fi fi-sr-map-marker address-img"></i><?=$address?></p>
        </div>
            <!-- <i class="fi fi-sr-bookmark collect" role="button"></i>-->
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

  <section class="normal-content section-content">
    <div class="first-row">
      <div class="anaysis col">
        <div class="title-group">
          <i class="fi fi-sr-bars-progress group-title-img"></i>
          <h5 class="group-title">指標分析</h5>
        </div>
        <table class="table table-borderless">
          <thead>
            <tr class="row1">
              <th scope="col" class="col1" style="width:30%"></th>
              <th scope="col" class="col2" style="width:15%"><?=$_POSITIVE ?></th>
              <th scope="col" class="col3" style="width:15%"><?=$_NEGATIVE ?></th>
              <th scope="col" class="col5" style="width:15%"><?=$_NEUTRAL ?></th>
              <th scope="col" class="col6" style="width:15%"><?=$_TOTAL ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $rowIndex = 1;?>
            <?php foreach ($memberWeights as $category => $data): ?>
              <?php
              $result = getProportionScore($category);
              $proportion = $result['proportion'];
              $proportionScore = $result['score'];
              $proportionColor = $data['weight'] > 0 ? $data['color'] : 'darkgrey';
              ?>
              <tr class="row<?=$rowIndex ?>">
                <td>
                  <div class="progress-group">
                    <h6 class="progress-text" style="color:<?=$proportionColor?>;padding-right:5px;"><?=$category?></h6>
                    <?php if($SESSION_DATA->success): ?>
                      <h6 class="progress-text" style="color:<?=$proportionColor?>;padding-right:5px;"><?=round($data['weight']*100)?>%</h6>
                    <?php endif; ?>
                    <h6 class="progress-text" style="color:<?=$proportionColor?>;">|</h6>
                    <h6 class="progress-percent" style="color:<?=$proportionColor?>"><?=$proportionScore?></h6>
                  </div>
                  <div class="progress col" role="progressbar" aria-label="Success example" aria-valuenow="" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar overflow-visible" style="width: <?=$proportion?>%; background-color: <?=$proportionColor?>;"></div>
                  </div>
                </td>
                <td class="evaluate-good"><?=$targetsInfo[$_POSITIVE][$category]??'NULL'?></td>
                <td class="evaluate-bad"><?=$targetsInfo[$_NEGATIVE][$category]??'NULL'?></td>
                <td class="evaluate-neutral"><?=$targetsInfo[$_NEUTRAL][$category]??'NULL'?></td>
                <td class="evaluate-all"><?=$targetsInfo[$_TOTAL][$category]??'NULL'?> 則</td>
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
                        <div class="service-item matched">
                          <?php if ($serviceItem['state'] == 1): ?>
                            <i class="fi fi-sr-check item-img-eligible"></i>
                          <h6 class="item-text-eligible"><?php echo htmlspecialchars($serviceItem['property']); ?></h6>
                          <?php else: ?>
                            <i class="fi fi-sr-cross item-img-ineligible"></i>
                            <h6 class="item-text-ineligible"><?php echo htmlspecialchars($serviceItem['property']); ?></h6>
                          <?php endif; ?>
                        </div>
                        <?php unset($memberServices[$serviceItem['property']]);?>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                <?php endforeach; ?>

              </div>
            </div>
          <?php endif; ?>

          <?php foreach ($serviceOrder as $category): ?>
            <?php if (isset($service[$category])): ?>
              <?php $serviceItems = $service[$category]; ?>
              <div class="service-group">
                <h6 class="service-title"><?php echo htmlspecialchars($category); ?></h6>
                <div class="item-group">
                  <?php foreach ($serviceItems as $serviceItem): ?>
                    <?php
                      $key = false;
                      foreach ($memberServices as $memberService) {
                        if (str_contains($serviceItem['property'], $memberService) || str_contains($serviceItem['category'], $memberService)) {
                          $key = true;
                          break;
                        }
                      }
                    ?>
                    <?php if (!$key): ?>
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

  <section class="normal-content section-content">
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
      <button type="button" class="btn question" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="評論樣本選擇" data-bs-custom-class="custom-tooltip1">
        <i class="fi fi-sr-interrogation question-img"></i>
      </button>
    </div>
    <div class="comment-keyword">
      <div class="keyword-title" id="keyword-title">        
        <h5 class="keyword-title-text">標記</h5>
        <!--篩選按鈕-->
        <div class="input-group mb-3 filter-button">
          <span class="input-group-text" id="basic-addon1"><i class="fi fi-sr-filter-list"></i></i>篩選</span>
          <select class="form-select" aria-label="Default select example" id="filterSelect">
            <option value="全部" selected>全部</option>
            <option value="氛圍">氛圍</option>
            <option value="產品">產品</option>
            <option value="服務">服務</option>
            <option value="售價">售價</option>
          </select>
        </div>
      </div>
      <!--### 生成Mark統計標籤 ###-->
      <?php
        $positiveMarks = getMarks($STORE_ID, $_POSITIVE);
        $negativeMarks = getMarks($STORE_ID, $_NEGATIVE);
        $neutralMarks = getMarks($STORE_ID, [$_PREFER, $_NEUTRAL]);
        $normalizedWeights = [
          $_POSITIVE => ['name' => 'good', 'marks' => $positiveMarks],
          $_NEGATIVE => ['name' => 'bad', 'marks' => $negativeMarks],
          $_NEUTRAL => ['name' => 'middle', 'marks' => $neutralMarks],
        ];
      ?>
      <?php foreach ($normalizedWeights as $category => $data): ?>
        <div class="group-gb <?=$data['name'] ?>-side">
          <h6 class="title-gb"><?=$category ?><i class="fi fi-sr-caret-right keyword-arrow"></i></h6>
          <div class="group-keyword">
            <?php foreach ($data['marks'] as $index => $keyword): ?>
              <div class="keywords">
                <button type="button" class="comment-<?=$data['name']?>" onclick="searchCommentsByTarget(this)">
                  <w class="object"><?=htmlspecialchars($keyword['object']); ?></w>
                  <w class="count">(<?=htmlspecialchars($keyword['count']); ?>)</w>
                </button>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="keyword-title" id="comment-title">
      <button id="reset-comment-search" class="btn btn-outline-gray btn-no-outline" onclick="resetCommentSearch()">
        <i class="fi fi-sr-undo" style="font-size: 1.5em;"></i>
      </button>
      <h5 class="keyword-title-text" id="comment-count-title">留言 0 則</h5> 
      <!--排序按鈕-->
      <div id="comments-order-bar" class="input-group mb-3 sort-button">
        <span class="input-group-text" id="basic-addon1"><i class="fi fi-sr-sort-amount-down"></i>排序</span>
        <select class="form-select comment-sort-select" aria-label="Default select example" id="sortSelect" style="max-width:150px;">
          <option value="相關性" selected>相關性</option>
          <option value="由高至低">由高至低</option>
          <option value="由低至高">由低至高</option>
        </select>
        <span class="input-group-text" id="inputGroup-sizing-default">篩選</span>
        <input type="text" class="form-control comment-keyword-input" id="commentKeyword" name="commentKeyword" placeholder="評論關鍵字">
        <button class="btn btn-outline-windows-blue" onclick="searchCommentsByKeyword()" id="search-button" style="border-color:lightgray;">搜尋</button>
        <button class="btn btn-outline-light-gray" onclick="clearSearchKeyword()" id="clear-button" style="border-color:lightgray;color:gray;">清除</button>
      </div>
    </div>
    <div class="comment-group" id="commentGroup" keyword=-1>
      <!-- 動態生成留言 -->
    </div>
  </section>
  <?php if ($foodKeywords): ?>
    <section class="normal-content section-content">
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
          <?php foreach ($foodKeywords as $foodKeyword): ?>
            <div class="card">
              <div class="card-body" onclick="openSearchPage('<?=$foodKeyword['word']?>')">
                <a class="card-text"><?=htmlspecialchars($foodKeyword['word']); ?>(<?=htmlspecialchars($foodKeyword['count']); ?>)</a>
              </div>
              <a class="card-a" href="https://www.google.com/search?udm=2&q=<?=urlencode($storeInfo['name'] . ' ' . $foodKeyword['word']); ?> " target="_blank"><img class="card-img" src="<?=$foodKeyword['image_url'] ?>"></a>
            </div>
          <?php endforeach; ?>        
        </div>
        <div class="carousel-arrow right-arrow" type="button"><i class="fi fi-sr-angle-right"></i></div>
      </div>
    </section>
  <?php endif; ?>
  <section class="normal-content section-content">
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
      </div>

    </div>
  </section>

  <?php if ($keywords): ?>
  <section class="normal-content section-content">
    <div class="title-group">
      <i class="fi fi-sr-interactive group-title-img"></i>
      <h5 class="group-title ">搜尋關鍵字</h5>
    </div>
    <div class="carousel-container-tag">
      <div class="carousel-arrow-tag left-arrow-2" type="button"><i class="fi fi-sr-angle-left"></i></div>

      <div class="tag-group">
        <?php 
          foreach ($keywords as $keyword) {
            echo '<button class="tag btn-outline-secondary btn" type="button" onclick="openSearchPage(\''.$keyword['word'].'\')">';
            echo '<a class="tag-text">'.htmlspecialchars($keyword['word']).' ('.htmlspecialchars($keyword['count']).')</a>';
            echo '</button>';
          }
        ?>
      </div>
      <div class="carousel-arrow-tag right-arrow-2" type="button"><i class="fi fi-sr-angle-right"></i></div>
  </section>
  <?php endif; ?>
  <section class="section-content">
    <!--資料爬蟲時間--><h6 class="update">資料更新時間：<?=$storeInfo['crawler_time'] ?></h6>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script src="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.min.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
  <script src="/scripts/detail.js" defer></script>
  <script src="/scripts/map.js"></script>
  <script src="/scripts/detail-landmark.js"></script>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
</body>
</html>