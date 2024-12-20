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
    $totalWithComments = (int)$storeInfo['total_withcomments'];
    $website = htmlspecialchars($storeInfo['website']);
    $phoneNumber = htmlspecialchars($storeInfo['phone_number']);
    $markIcon = $markOptions[$storeMark]['tagIcon'] ?? '';
    $markClass = $markOptions[$storeMark]['buttonClass'] ?? '';
    $storeTag = htmlspecialchars($storeInfo['tag']);
    $isFavorite = isFavorite($storeId);
    $memberServices = getMemberServiceList();
    $memberWeights = getMemberNormalizedWeight();
    $score = getBayesianScore($memberWeights, $storeId);
    $relevants = getRelevantComments($storeId);
    $Highests = getHighestComments($storeId);
    $Lowests = getLowestComments($storeId);
    $comments = getComments($storeId);
    $location = getLocation($storeId);
    $fullAddress = htmlspecialchars($location['city'].$location['dist'].$location['vil'].$location['city'].$location['details']);
    $lat = $location['latitude'];
    $lng = $location['longitude'];
    $rating = getRating($storeId);
    $service = getService($storeId);
    $keywords = getKeywords($storeId, 20);
    $foodKeywords = getFoodKeyword($storeId);
    $otherBranches = getOtherBranches($storeInfo['branch_title'], $storeId);
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
  <title>詳細資訊 - 評星宇宙</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/common/map.css?v=<?=$VERSION?>">
  <link rel="stylesheet" href="/styles/common/base.css?v=<?=$VERSION?>">
  <link rel="stylesheet" href="/styles/detail.css?v=<?=$VERSION?>" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>
  <header id="storeinfo-header">
    <div id="favorite-button" onclick="toggleFavorite(this,<?=$storeId?>)">
      <i class="store-bookmark fi <?=$isFavorite?'fi-sr-heart':'fi-br-heart'?>" style="font-size:34px;top:2px;position:relative;"></i>
      <h1 class="store-title no-flow" id="store-title" style="margin-left:10px;max-width:1490px;"><?=$storeName?></h1>
    </div>
    <hr class="header-separator">
  </header>
  <section class="top-content section-content">
    <div id="favorite-button" onclick="toggleFavorite(this,<?=$storeId?>)">
      <i class="store-bookmark fi <?=$isFavorite?'fi-sr-heart':'fi-br-heart'?>" style="font-size:34px;top:2px;position:relative;"></i>
      <h1 class="store-title" id="store-title" style="margin-left:10px"><?=$storeName?></h1>
    </div>
    <div class="love-group">
      <div class="type-rating-status-group">
        <h5 class="rating"><?=$score?></h5>
        <h6 class="rating-text">/ 綜合評分</h6>
        <?php if($totalWithComments < 30): ?>
          <button
            class="store-type btn btn-outline-red"
            type="button"
            data-bs-container="body"
            data-bs-toggle="tooltip"
            data-bs-title="這間餐廳尚未有足夠的評論，統計資料可能沒辦法精確反映餐廳表現"
            data-bs-placement="bottom"
            data-bs-custom-class="tooltip tooltip-red"
            data-bs-html="true">
            評論不足 <i class="fi fi fi-sr-interrogation status-img"></i>
          </button>
        <?php endif; ?>
        <button class="store-type btn btn-outline-gray" type="button" onclick="openSearchPage('<?=$storeTag?>')"><i class="fi fi-br-search status-img"></i><?=$storeTag?></button>
        <?php if($storeMark): ?>
          <button type="button" class="store-type <?=$markClass?>"><?=$markIcon?></button>
        <?php endif; ?>
        <?php require_once $_SERVER['DOCUMENT_ROOT'].'/elem/open-hour-button.php';?>
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
              <p class="store-name no-flow-margin" style="width:14%"><?=$branchTitle?></p>
              <p style="width:2%;text-align:center;color:lightgrey;;margin:0 0;">|</p>
              <p class="store-name no-flow-margin" style="width:14%"><?=$branchName?></p>
              <p style="width:2%;text-align:center;color:lightgrey;margin:0 0;">|</p>
              <p class="other-rating no-flow-margin" style="width:16%;text-align:center"><?=$branchScore?> / 綜合評分</p>
              <p style="width:2%;text-align:center;color:lightgrey;margin:0 0;">|</p>
              <p class="other-map address no-flow-margin"><i class="fi fi-sr-map-marker address-img"></i><?=$address?></p>
        </div>
            <!-- <i class="fi fi-sr-heart collect" role="button"></i>-->
          </div>
        <?php endforeach;?>
      </div>
    </div>
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
      <div class="invisible">
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
    </div>
    <div class="comment-keyword">
      <div class="keyword-title" id="keyword-title">
        <h5 class="keyword-title-text">標記</h5>
        <div class="input-group mb-3 filter-button">
          <span class="input-group-text" id="basic-addon1"><i class="fi fi-sr-filter-list"></i></i>篩選</span>
          <select class="form-select" aria-label="Default select example" id="filterSelect">
            <option value="<?=$_ALL?>" selected><?=$_ALL?></option>
            <option value="<?=$_ATMOSPHERE?>"><?=$_ATMOSPHERE?></option>
            <option value="<?=$_PRODUCT?>"><?=$_PRODUCT?></option>
            <option value="<?=$_SERVICE?>"><?=$_SERVICE?></option>
            <option value="<?=$_PRICE?>"><?=$_PRICE?></option>
          </select>
        </div>
      </div>
      <?php $commentTargets = [$_POSITIVE => 'good', $_NEGATIVE => 'bad', $_NEUTRAL => 'middle']; ?>
      <?php foreach ($commentTargets as $target => $name): ?>
        <div class="group-gb <?=$name?>-side">
          <h6 class="title-gb"><?=$target?><i class="fi fi-sr-caret-right keyword-arrow"></i></h6>
          <div id="group-keyword-<?=$name?>" class="group-keyword">
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
        <button
          class="more-info hide-button"
          type="button"
          data-bs-container="body"
          data-bs-toggle="tooltip"
          data-bs-title="
            <strong>[互動說明]</strong>
            <br>
            點擊文字 ➔ 查詢餐點關鍵字
            <br>
            點擊圖片 ➔ 搜尋餐點圖片
          "
          data-bs-placement="bottom"
          data-bs-custom-class="tooltip tooltip-windows-blue"
          data-bs-html="true">
          <i class="fi fi-sr-interrogation question-img trans-windows-blue-button"></i>
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
              <a class="card-a" draggable="false" href="https://www.google.com/search?udm=2&q=<?=urlencode($storeName.' '.$storeTag.' '. $foodKeyword['word']); ?> " target="_blank"><img class="card-img" src="<?=$foodKeyword['image_url'] ?>"></a>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="carousel-arrow right-arrow" type="button"><i class="fi fi-sr-angle-right"></i></div>
      </div>
    </section>
  <?php endif; ?>
  <section class="normal-content section-content">
    <div class="introduction-display" style="min-height:185px;">
      <div class="store-introduction">
        <div class="title-group">
          <i class="fi fi-sr-search-alt group-title-img"></i>
          <h5 class="group-title ">餐廳資訊</h5>
        </div>
        <div class="introduction-item-group">
          <div class="store-introduction-group">
            <li class="introduction-title">地址</li>
            <div class="introduction-item pointer no-flow-margin underline" onclick="confirmNavigate(<?=$lat?>,<?=$lng?>)"><?=$fullAddress?></div>
          </div>
          <div class="store-introduction-group">
            <li class="introduction-title">電話</li>
            <?php if($phoneNumber):?>
              <li class="introduction-item no-flow-margin"><?=$phoneNumber;?></li>
            <?php else:?>
              <li class="introduction-item">(未提供)</li>
            <?php endif;?>
          </div>
          <div class="store-introduction-group">
            <li class="introduction-title">相關網站</li>
            <?php if($website):?>
              <div class="introduction-item pointer no-flow-margin underline" onclick="confirmExternalLink('<?=$website?>')"><?=$website?></div>
            <?php else:?>
              <li class="introduction-item">(未提供)</li>
            <?php endif;?>
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
        <?php foreach ($keywords as $keyword): ?>
          <?php
            $word = htmlspecialchars($keyword['word']);
            $count = htmlspecialchars($keyword['count']);
          ?>
          <button class="tag btn-outline-light-gray btn" type="button" onclick='openSearchPage("<?=$word?>")'>
            <a class="tag-text"><i class="fi fi-br-search status-img"></i><?=$word?> (<?=$count?>)</a>
          </button>
        <?php endforeach; ?>
      </div>
      <div class="carousel-arrow-tag right-arrow-2" type="button"><i class="fi fi-sr-angle-right"></i></div>
  </section>
  <?php endif; ?>

  <section class="section-content" style="margin-top:20px;">
    <!--資料爬蟲時間--><h6 class="update">資料更新時間：<?=$storeInfo['crawler_time'] ?></h6>
  </section>

  <?php if($storeId):?>
    <button id="back-to-top-btn" class="circle-button" style="bottom:90px;display:none;"><i class="fi fi-sr-up circle-button-icon"></i></button>
    <button id="share-store-btn" class="circle-button" onclick="shareStore(<?=$storeId?>);"><i class="fi fi-sr-share circle-button-icon"></i></button>
  <?php else:?>
    <button id="back-to-top-btn" class="circle-button" style="display:none;"><i class="fi fi-sr-up circle-button-icon"></i></button>
  <?php endif;?>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/map.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/detail.js?v=<?=$VERSION?>" defer></script>
  <script src="/scripts/detail-landmark.js?v=<?=$VERSION?>"></script>
</body>
</html>