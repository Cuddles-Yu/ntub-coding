<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  
  $_ROUND = 1;
  $RESULT_LIMIT = 50;

  $_ATMOSPHERE = '氛圍';
  $_PRICE= '售價';
  $_PRODUCT = '產品';
  $_SERVICE = '服務';

  $_POSITIVE = '正面';
  $_NEGATIVE = '負面';
  $_PREFER = '喜好';
  $_NEUTRAL = '中立';
  $_TOTAL = '總計';

  function getTargets($store_id) {
    global $conn, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE, $_POSITIVE, $_NEGATIVE, $_PREFER, $_NEUTRAL, $_TOTAL;

    // 查詢相關商店的留言，並篩選掉空值
    $stmt = bindPrepare($conn,"
      SELECT 
        c.environment_state, c.price_state, c.product_state, c.service_state
      FROM comments AS c
      LEFT JOIN stores AS s ON s.id = c.store_id
      WHERE 
        c.store_id = ? AND
        s.crawler_state IN ('完成', '超時', '成功') AND 
        (c.environment_state IS NOT NULL OR c.price_state IS NOT NULL OR c.product_state IS NOT NULL OR c.service_state IS NOT NULL)
    ", "i", $store_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // 初始化正面和負面數量的計數
    $positive_count = [
        $_ATMOSPHERE => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];
    $negative_count = [
        $_ATMOSPHERE => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];
    $neutral_count = [
        $_ATMOSPHERE => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];
    $total_count = [
        $_ATMOSPHERE => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];

    // 統計每個指標的正面和負面數量
    while ($row = $result->fetch_assoc()) {        
        $states = [
            'environment_state' => $_ATMOSPHERE,
            'price_state' => $_PRICE,
            'product_state' => $_PRODUCT,
            'service_state' => $_SERVICE,
        ];        
        foreach ($states as $state => $constant) {
            if ($row[$state] === $_POSITIVE) {
                $positive_count[$constant]++;
            } elseif ($row[$state] === $_NEGATIVE) {
                $negative_count[$constant]++;
            } elseif ($row[$state] === $_NEUTRAL || $row[$state] === $_PREFER) {
                $neutral_count[$constant]++;
            }
            if (!is_null($row[$state])) {
                $total_count[$constant]++;
            }
        }
    }
    $stmt->close();
    // 返回正面和負面留言的數量
    return [
        $_POSITIVE => $positive_count,
        $_NEGATIVE => $negative_count,
        $_NEUTRAL => $neutral_count,
        $_TOTAL => $total_count
    ];
  }

  function getAddress($storeItem) {
    return $storeItem['city'].$storeItem['dist'].$storeItem['details'];
  }

  function searchByKeyword($keyword){
    global $conn;
    $keyword = "%$keyword%";
    $stmt = bindPrepare($conn,
    " SELECT 
        DISTINCT s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews, 
        l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude
      FROM stores AS s
      INNER JOIN keywords AS k ON s.id = k.store_id
      INNER JOIN rates AS r ON s.id = r.store_id
      INNER JOIN locations AS l ON s.id = l.store_id
      WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND s.crawler_state IN ('成功', '完成', '超時')
      ORDER BY r.avg_ratings DESC, r.total_reviews DESC
    ", "sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    $stores = [];
    while ($row = $result->fetch_assoc()) {
        if (is_numeric($row['latitude']) && is_numeric($row['longitude'])) $stores[] = $row;
    }
    $stmt->close();
    return $stores;
  }

  function searchByLocation($keyword, $userLat, $userLng, $limit){
    global $conn;
    $keyword = "%$keyword%";
    if ($userLat&&$userLng) {
      $stmt = bindPrepare($conn,  
      " SELECT 
        DISTINCT s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews, 
        l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
        (6371000*acos(cos(radians(?))*cos(radians(l.latitude))*cos(radians(l.longitude)-radians(?))+sin(radians(?))*sin(radians(l.latitude)))) AS distance
        FROM stores AS s
        INNER JOIN keywords AS k ON s.id = k.store_id
        INNER JOIN rates AS r ON s.id = r.store_id
        INNER JOIN locations AS l ON s.id = l.store_id
        WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND s.crawler_state IN ('成功', '完成', '超時')
        HAVING distance <= 1500
        ORDER BY distance, r.avg_ratings DESC, r.total_reviews DESC
        LIMIT ?
      ", 'dddsssi', $userLat, $userLng, $userLat, $keyword, $keyword, $keyword, $limit);
    } else {
      $stmt = bindPrepare($conn,
      " SELECT 
        DISTINCT s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews, 
        l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude
        FROM stores AS s
        INNER JOIN keywords AS k ON s.id = k.store_id
        INNER JOIN rates AS r ON s.id = r.store_id
        INNER JOIN locations AS l ON s.id = l.store_id
        WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND s.crawler_state IN ('成功', '完成', '超時')
        ORDER BY r.avg_ratings DESC, r.total_reviews DESC
        LIMIT ?
      ", 'sssi', $keyword, $keyword, $keyword, $limit);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = [];
    while ($row = $result->fetch_assoc()) {
        if (is_numeric($row['latitude']) && is_numeric($row['longitude'])) $stores[] = $row;
    }
    $stmt->close();
    return $stores;
  }

  function isFavorite($storeId) {
    global $conn, $MEMBER_ID;
    if (is_null($MEMBER_ID)) return false;
    $count = 0;
    $stmt = bindPrepare($conn, "
      SELECT COUNT(*) FROM favorites 
      WHERE member_id = ? AND store_id = ?
    ", "ii", $MEMBER_ID, $storeId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count>0;
  }

  function normalizeWeights($weights) {
    $total_weight = 0;
    foreach ($weights as $key => $value) {
      $total_weight += $value['weight'];
    }
    if ($total_weight == 0) throw new Exception("總權重不能為零");
    foreach ($weights as $key => $value) {
      $weights[$key]['weight'] = $value['weight']/$total_weight;
    }
    return $weights;
  }

  function getProportionScore($category) {
    global $targetsInfo, $_POSITIVE, $_NEGATIVE, $_ROUND;
    $p = $targetsInfo[$_POSITIVE][$category] ?? 0;
    $n = $targetsInfo[$_NEGATIVE][$category] ?? 0;
    $t = $p + $n;
    $proportion = ($t > 0) ? round($p / $t * 100, $_ROUND) : null;
    $score = (!is_null($proportion)) ? number_format($proportion, $_ROUND) . ' 分' : '(無評價)';
    return [        
      'positive' => $p,
      'negative' => $n,
      'proportion' => $proportion,
      'score' => $score
    ];
  }

  function getMemberInfo() {
    global $conn, $MEMBER_ID;
    if (is_null($MEMBER_ID)) return null;
    $stmt = bindPrepare($conn, "
      SELECT * FROM members AS m
      LEFT JOIN preferences AS p ON m.id = p.member_id
      WHERE m.id = ?
    ", "i", $MEMBER_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    $stmt->close();
    return $member??null;
  }

  function getFavoriteStores() {
    global $conn, $MEMBER_ID;
    if (is_null($MEMBER_ID)) return [];
    $stmt = bindPrepare($conn, "
      SELECT 
        s.id, s.name, s.tag, s.mark, s.preview_image, f.create_time
      FROM favorites AS f
      INNER JOIN stores AS s ON f.store_id = s.id
      WHERE f.member_id = ?
      ORDER BY f.create_time DESC
    ", "i", $MEMBER_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = [];
    while ($row = $result->fetch_assoc()) {
      $stores[] = $row;
    }
    $stmt->close();
    return $stores;
  }

  function getMemberServiceList() {
    global $conn, $MEMBER_ID, $serviceMap;
    if (is_null($MEMBER_ID)) return [];
    $services = [];
    $stmt = bindPrepare($conn," 
      SELECT 
        parking, wheelchair_accessible, vegetarian, healthy, kids_friendly, pets_friendly, 
        gender_friendly, delivery, takeaway, dine_in, breakfast, brunch, lunch, dinner, reservation, 
        group_friendly, family_friendly, toilet, wifi, cash, credit_card, debit_card, mobile_payment
      FROM preferences
      WHERE member_id = ?
    ", "i", $MEMBER_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    if ($row) {
      foreach ($serviceMap as $field => $label) {
        if (isset($row[$field]) && $row[$field] == 1) $services[] = $label;
      }
    }
    return $services;
  }

  function getMemberNormalizedWeight() {
    global $conn, $MEMBER_ID, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE;
    $atmosphere_weight = 1;
    $price_weight = 1;
    $product_weight = 1;
    $service_weight = 1;
    if (!is_null($MEMBER_ID)) {
      $stmt = bindPrepare($conn,
      " SELECT atmosphere_weight, price_weight, product_weight, service_weight
        FROM preferences
        WHERE member_id = ?
      ", "i", $MEMBER_ID);
      $stmt->execute();
      $stmt->bind_result($atmosphere_weight, $price_weight, $product_weight, $service_weight);
      $stmt->fetch();
      $stmt->close();
    }    
    $normalized_weights = normalizeWeights([
      $_ATMOSPHERE => ['weight' => $atmosphere_weight, 'color' => '#562B08'],
      $_PRODUCT => ['weight' => $product_weight, 'color' => '#7B8F60'],
      $_SERVICE => ['weight' => $service_weight, 'color' => '#5053AF'],
      $_PRICE => ['weight' => $price_weight, 'color' => '#C19237'],
    ]);
    uasort($normalized_weights, function ($a, $b) {
      return $b['weight'] <=> $a['weight'];
    });
    return $normalized_weights;
  }


  // 計算並回傳指定商店的貝氏平均分數
  function getBayesianScore($memberWeights, $storeId) {
    global $conn, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE, $_ROUND;
    $all_stores_sql = 
    "   SELECT store_id, environment_rating, product_rating, service_rating, price_rating, total_withcomments, total_reviews 
        FROM rates
        WHERE comments_analysis = 1
    ";
    $all_result = $conn->query($all_stores_sql);

    $stores = [];
    $store_scores = [];
    $total_stores = 0;

    if ($all_result->num_rows > 0) {
      while ($row = $all_result->fetch_assoc()) {
        $temp_store_id = $row['store_id'];
        $store_score = 
          $row['environment_rating']*$memberWeights[$_ATMOSPHERE]['weight'] +
          $row['product_rating']*$memberWeights[$_PRODUCT]['weight'] +
          $row['service_rating']*$memberWeights[$_SERVICE]['weight'] +
          $row['price_rating']*$memberWeights[$_PRICE]['weight'];
        $stores[$temp_store_id] = [
            'score' => $store_score,
            'total_withcomments' => $row['total_withcomments'],
            'total_reviews' => $row['total_reviews']
        ];
        $store_scores[] = $store_score;
        $total_stores++;
      }
    }

    if ($total_stores == 0) return "沒有商店數據可用";

    $overall_mean_score = array_sum($store_scores) / $total_stores;
    $total_withcomments = array_column($stores, 'total_withcomments');
    sort($total_withcomments);
    $C = $total_withcomments[intval(count($total_withcomments) / 2)+1];

    if (!isset($stores[$storeId])) return "???";

    $store_data = $stores[$storeId];
    $store_score = $store_data['score'];
    $total_withcomments = $store_data['total_withcomments'];
    $bayesian_score = (($C * $overall_mean_score) + ($store_score * $total_withcomments)) / ($C + $total_withcomments);
    return number_format(round($bayesian_score, $_ROUND), $_ROUND);
  }