<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>首頁 - 評星宇宙</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <meta name="keywords" content="評價, google map" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">

  <!-- 載入 leaflet.css -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
  crossorigin=""/> 

  <!-- 載入 leaflet.awesome-markers.css -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.css" />

  <!-- 載入 MarkerCluster.css -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
  <!-- 載入 /styles/osm-map.css -->
  <link rel="stylesheet" type="text/css" href="./styles/osm-map.css">

  <link rel="stylesheet" href="styles/home.css" />
  <link rel="stylesheet" href="styles/form.css" />
  <link rel="stylesheet" href="styles/map.css" />
</head>

<body>
  
  <!-- ### 頁首 ### -->
  <?php require $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>


  <!-- ### 內容 ### -->
  <section class="secondary-content">
    <h2 class="title-text">美食餐廳搜尋</h2>
      <div class="search">        
        <div class="form-floating search-keyword">
            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="關鍵字">
            <label for="keyword">請輸入查詢關鍵字</label>
        </div>
        <!--篩選條件-->
        <button type="button" class="btn btn-secondary mt-3 filter-button" data-bs-toggle="modal" data-bs-target="#exampleModal">
          篩選條件
        </button>
        
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">篩選條件</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="post" action="/home">
                <div class="modal-body">
                  <!-- 篩選選項 -->
                  <div class="input-group input-group-sm mb-3">
                    <p class="checkbox-title">搜尋半徑</p>
                    <input id="search-radius-input" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    <span class="input-group-text" id="inputGroup-sizing-sm">公尺</span>
                  </div>
                  <p class="checkbox-title">個人需求</p>
                  <div class="checkbox-container">                
                    <div class="checkbox-item">
                      <input type="checkbox" id="parking" value="">
                      <label for="parking">停車場</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="wheelchair-accessible" value="">
                      <label for="wheelchair-accessible">無障礙</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="vegetarian" value="">
                      <label for="vegetarian">素食料理</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="healthy" value="">
                      <label for="healthy">健康料理</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="kids_friendly" value="">
                      <label for="kids_friendly">兒童友善</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="pets_friendly" value="">
                      <label for="pets_friendly">寵物友善</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="personal7" value="">
                      <label for="personal7">性別友善</label>
                    </div>                    
                  </div>
                  <p class="checkbox-title">用餐方式</p>
                  <div class="checkbox-container">                
                    <div class="checkbox-item">
                      <input type="checkbox" id="delivery" value="">
                      <label for="delivery">外送</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="takeaway" value="">
                      <label for="takeaway">外帶</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="dine-in" value="">
                      <label for="dine-in">內用</label>
                    </div>                  
                  </div>
                  <p class="checkbox-title">用餐時段</p>
                  <div class="checkbox-container">                
                    <div class="checkbox-item">
                      <input type="checkbox" id="breakfast" value="">
                      <label for="breakfast">早餐</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="brunch" value="">
                      <label for="brunch">早午餐</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="lunch" value="">
                      <label for="lunch">午餐</label>
                    </div>  
                    <div class="checkbox-item">
                      <input type="checkbox" id="dinner" value="">
                      <label for="dinner">晚餐</label>
                    </div>                  
                  </div>
                  <p class="checkbox-title">營業時間</p>
                  <div class="checkbox-container">                
                    <div class="checkbox-item">
                      <input type="checkbox" id="BusinessHours" value="">
                      <label for="BusinessHours">營業中</label>
                    </div>                                      
                  </div>
                  <p class="checkbox-title">用餐氛圍</p>
                  <div class="checkbox-container">                
                    <div class="checkbox-item">
                      <input type="checkbox" id="casual" value="">
                      <label for="casual">氣氛悠閒</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="cosy" value="">
                      <label for="cosy">環境舒適</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="ambiance3" value="">
                      <label for="ambiance3">音樂演奏</label>
                    </div>                                     
                  </div>
                  <p class="checkbox-title">用餐規劃</p>
                  <div class="checkbox-container">                
                    <div class="checkbox-item">
                      <input type="checkbox" id="reservation" value="">
                      <label for="reservation">接受訂位</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="group_friendly" value="">
                      <label for="group_friendly">適合團體</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="family_friendly" value="">
                      <label for="family_friendly">適合家庭聚餐</label>
                    </div>                                     
                  </div>
                  <p class="checkbox-title">基礎設施</p>
                  <div class="checkbox-container">                
                    <div class="checkbox-item">
                      <input type="checkbox" id="toilet" value="">
                      <label for="toilet">洗手間</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="wi-fi" value="">
                      <label for="wi-fi">無線網路</label>
                    </div>                                                        
                  </div>
                  <p class="checkbox-title">付款方式</p>
                  <div class="checkbox-container">                
                    <div class="checkbox-item">
                      <input type="checkbox" id="cash" value="">
                      <label for="cash">現金</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="credit_card" value="">
                      <label for="credit_card">信用卡</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="debit_card" value="">
                      <label for="debit_card">簽帳金融卡</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="mobile_payment" value="">
                      <label for="mobile_payment">行動支付</label>
                    </div>                                                        
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                  <button type="submit" class="btn btn-primary" id="saveSelections">儲存</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <button type="button" class="btn btn-secondary mt-3 search-button" id="search-button" onclick="toSearchPage()">搜尋</button>
      </div>
    <!--已選擇篩選條件-->
    <div class="filter-container">
      <p class="filter-title"><i class="fi fi-sr-filter"></i>已篩選條件：</p>
      <div class="filter-result">
        <div class="condition">停車場</div>
      </div>
    </div>


    <!-- osm 地圖 -->
    <div id="map" class="map">
      <div id="crosshair"></div> <!-- 添加透明十字 -->
        <!-- 使用者定位按鈕 -->
        <button type="button" id="locateButton" onclick="userLocate()">使用您的位置</button>
    </div>


    <div class="tip-line"></div>
    <a class="tip" id="scroll-to-tertiary"> ⇊ 從熱門到個人化推薦，發現美食的無限可能</a>
  </section>

  <section class="tertiary-content" id="tertiary-content">
    <span id="tab-1" class="tab-1">熱門推薦</span>
    <span id="tab-2" class="tab-2">偏好推薦</span>
    <span id="tab-3" class="tab-3">隨機推薦</span>
    <div id="tab">
      <ul>
        <li><a id="tab-button-1" class="title-text-2" data-tab="tab-content-1">熱門推薦</a></li>
        <li><a id="tab-button-2" class="title-text-2" data-tab="tab-content-2">偏好推薦</a></li>
        <li><a id="tab-button-3" class="title-text-2" data-tab="tab-content-3">隨機推薦</a></li>
      </ul>
      <div class="tab-content-1 active" id="tab-content-1">
      </div>
      <div class="tab-content-2" id="tab-content-2"> 
      </div>
      <div class="tab-content-3" id="tab-content-3">
      </div>
    </div>
  </section>

  <!-- ### 頁尾 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>

  <!-- ### 載入 ### -->
  <!-- 載入地圖框架 leaflet.js -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <!-- 載入 leaflet.awesome-markers.min.js -->
  <script src="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.min.js"></script>

  <!-- 載入 Font Awesome Kit -->
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>

  <!-- 載入 Markercluster.js -->
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

  <script src="./scripts/map.js"></script>
  <script src="scripts/ui-interactions.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="scripts/home.js"></script>
  <script src="scripts/new_signup_page_1.js"></script>
  <script src="scripts/new_signup_page_3.js"></script>
  
</body>

</html>
