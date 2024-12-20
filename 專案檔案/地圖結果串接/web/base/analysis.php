<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';

  $_ROUND = 1;
  $RESULT_LIMIT = 100;
  $RECOMMEND_LIMIT = 10;
  $MIN_RECOMMEND_COUNT = 10;
  $MIN_KEYWORD_COUNT = 10;

  $_ALL = '全部';
  $_ATMOSPHERE = '餐廳氛圍';
  $_PRICE= '性價比';
  $_PRODUCT = '餐點品質';
  $_SERVICE = '服務態度';

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

  function searchByStoreMark($mark) {
    global $conn, $memberWeights, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE, $RECOMMEND_LIMIT, $MIN_RECOMMEND_COUNT;
    $C = getCValue();
    $atmosphereWeight = $memberWeights[$_ATMOSPHERE]['weight'];
    $productWeight = $memberWeights[$_PRODUCT]['weight'];
    $serviceWeight = $memberWeights[$_SERVICE]['weight'];
    $priceWeight = $memberWeights[$_PRICE]['weight'];
    $stmt = bindPrepare($conn, "
      SELECT
        s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews,
        l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
        ((? * (SELECT AVG(
          COALESCE(environment_rating, 0) * ? +
          COALESCE(product_rating, 0) * ? +
          COALESCE(service_rating, 0) * ? +
          COALESCE(price_rating, 0) * ?
        ) FROM rates WHERE comments_analysis = 1) +
        (r.total_withcomments * (
          COALESCE(r.environment_rating, 0) * ? +
          COALESCE(r.product_rating, 0) * ? +
          COALESCE(r.service_rating, 0) * ? +
          COALESCE(r.price_rating, 0) * ?))
        ) / (? + r.total_withcomments)) AS score
      FROM stores AS s
      INNER JOIN (
        SELECT id FROM stores
        WHERE mark = ? and crawler_state IN ('成功', '完成', '超時')
      ) AS mark_stores ON s.id = mark_stores.id
      INNER JOIN keywords AS k ON s.id = k.store_id
      INNER JOIN rates AS r ON s.id = r.store_id
      INNER JOIN locations AS l ON s.id = l.store_id
      GROUP BY s.id
      ORDER BY score DESC
    ", 'dddddddddds', $C, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C,
      $mark
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = [];
    while ($row = $result->fetch_assoc()) $stores[] = $row;
    $stmt->close();
    return $stores;
  }

  function searchByBrowse() {
    global $conn, $memberWeights, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE, $RECOMMEND_LIMIT, $MIN_RECOMMEND_COUNT;
    $C = getCValue();
    $atmosphereWeight = $memberWeights[$_ATMOSPHERE]['weight'];
    $productWeight = $memberWeights[$_PRODUCT]['weight'];
    $serviceWeight = $memberWeights[$_SERVICE]['weight'];
    $priceWeight = $memberWeights[$_PRICE]['weight'];
    $stmt = bindPrepare($conn, "
      SELECT
        browse_stores.count, s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews,
        l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
        ((? * (SELECT AVG(
          COALESCE(environment_rating, 0) * ? +
          COALESCE(product_rating, 0) * ? +
          COALESCE(service_rating, 0) * ? +
          COALESCE(price_rating, 0) * ?
        ) FROM rates WHERE comments_analysis = 1) +
        (r.total_withcomments * (
          COALESCE(r.environment_rating, 0) * ? +
          COALESCE(r.product_rating, 0) * ? +
          COALESCE(r.service_rating, 0) * ? +
          COALESCE(r.price_rating, 0) * ?))
        ) / (? + r.total_withcomments)) AS score
      FROM stores AS s
      INNER JOIN (
        SELECT target_store, COUNT(*) AS count FROM histories
        WHERE action = '瀏覽'
        GROUP BY target_store
        HAVING count >= ?
        ORDER BY count DESC
      ) AS browse_stores ON s.id = browse_stores.target_store
      INNER JOIN keywords AS k ON s.id = k.store_id
      INNER JOIN rates AS r ON s.id = r.store_id
      INNER JOIN locations AS l ON s.id = l.store_id
      GROUP BY s.id
      ORDER BY score DESC
      LIMIT ?
    ", 'ddddddddddii', $C, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C,
      $MIN_RECOMMEND_COUNT, $RECOMMEND_LIMIT
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = [];
    while ($row = $result->fetch_assoc()) $stores[] = $row;
    $stmt->close();
    return $stores;
  }

  function searchByFavorite() {
    global $conn, $memberWeights, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE, $RECOMMEND_LIMIT, $MIN_RECOMMEND_COUNT;
    $C = getCValue();
    $atmosphereWeight = $memberWeights[$_ATMOSPHERE]['weight'];
    $productWeight = $memberWeights[$_PRODUCT]['weight'];
    $serviceWeight = $memberWeights[$_SERVICE]['weight'];
    $priceWeight = $memberWeights[$_PRICE]['weight'];
    $stmt = bindPrepare($conn, "
      SELECT
        favorite_stores.count, s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews,
        l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
        ((? * (SELECT AVG(
          COALESCE(environment_rating, 0) * ? +
          COALESCE(product_rating, 0) * ? +
          COALESCE(service_rating, 0) * ? +
          COALESCE(price_rating, 0) * ?
        ) FROM rates WHERE comments_analysis = 1) +
        (r.total_withcomments * (
          COALESCE(r.environment_rating, 0) * ? +
          COALESCE(r.product_rating, 0) * ? +
          COALESCE(r.service_rating, 0) * ? +
          COALESCE(r.price_rating, 0) * ?))
        ) / (? + r.total_withcomments)) AS score
      FROM stores AS s
      INNER JOIN (
        SELECT store_id, COUNT(*) AS count FROM favorites
        GROUP BY store_id
        HAVING count >= ?
        ORDER BY count DESC
      ) AS favorite_stores ON s.id = favorite_stores.store_id
      INNER JOIN keywords AS k ON s.id = k.store_id
      INNER JOIN rates AS r ON s.id = r.store_id
      INNER JOIN locations AS l ON s.id = l.store_id
      GROUP BY s.id
      ORDER BY score DESC
      LIMIT ?
    ", 'ddddddddddii', $C, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C,
      $MIN_RECOMMEND_COUNT, $RECOMMEND_LIMIT
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = [];
    while ($row = $result->fetch_assoc()) $stores[] = $row;
    $stmt->close();
    return $stores;
  }

  function searchByRandom() {
    global $conn, $memberWeights, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE, $RECOMMEND_LIMIT;
    $C = getCValue();
    $atmosphereWeight = $memberWeights[$_ATMOSPHERE]['weight'];
    $productWeight = $memberWeights[$_PRODUCT]['weight'];
    $serviceWeight = $memberWeights[$_SERVICE]['weight'];
    $priceWeight = $memberWeights[$_PRICE]['weight'];
    $stmt = bindPrepare($conn, "
      SELECT
        s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews,
        l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
        ((? * (SELECT AVG(
          COALESCE(environment_rating, 0) * ? +
          COALESCE(product_rating, 0) * ? +
          COALESCE(service_rating, 0) * ? +
          COALESCE(price_rating, 0) * ?
        ) FROM rates WHERE comments_analysis = 1) +
        (r.total_withcomments * (
          COALESCE(r.environment_rating, 0) * ? +
          COALESCE(r.product_rating, 0) * ? +
          COALESCE(r.service_rating, 0) * ? +
          COALESCE(r.price_rating, 0) * ?))
        ) / (? + r.total_withcomments)) AS score
      FROM stores AS s
      INNER JOIN (
        SELECT DISTINCT s.id
        FROM stores AS s
        WHERE s.crawler_state IN ('成功', '完成', '超時')
        ORDER BY RAND()
        LIMIT ?
      ) AS random_stores ON s.id = random_stores.id
      INNER JOIN keywords AS k ON s.id = k.store_id
      INNER JOIN rates AS r ON s.id = r.store_id
      INNER JOIN locations AS l ON s.id = l.store_id
      GROUP BY s.id
      ORDER BY score DESC
    ", 'ddddddddddi', $C, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C,
      $RECOMMEND_LIMIT
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = [];
    while ($row = $result->fetch_assoc()) $stores[] = $row;
    $stmt->close();
    return $stores;
  }

  function searchByLocation($keyword, $searchRadius, $city, $dist, $userLat, $userLng){
    global $conn, $memberWeights, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE, $RESULT_LIMIT;
    $C = getCValue();
    $atmosphereWeight = $memberWeights[$_ATMOSPHERE]['weight'];
    $productWeight = $memberWeights[$_PRODUCT]['weight'];
    $serviceWeight = $memberWeights[$_SERVICE]['weight'];
    $priceWeight = $memberWeights[$_PRICE]['weight'];
    $keyword = "%$keyword%";
    if (($userLat&&$userLng)) {
      $stmt = bindPrepare($conn, "
        SELECT DISTINCT
          s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews,
          l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
          (6371000 * acos(cos(radians(?)) * cos(radians(l.latitude)) * cos(radians(l.longitude) - radians(?)) + sin(radians(?)) * sin(radians(l.latitude)))) AS distance,
          ((? * (SELECT AVG(
            COALESCE(environment_rating, 0) * ? +
            COALESCE(product_rating, 0) * ? +
            COALESCE(service_rating, 0) * ? +
            COALESCE(price_rating, 0) * ?
          ) FROM rates WHERE comments_analysis = 1) +
          (r.total_withcomments * (
            COALESCE(r.environment_rating, 0) * ? +
            COALESCE(r.product_rating, 0) * ? +
            COALESCE(r.service_rating, 0) * ? +
            COALESCE(r.price_rating, 0) * ?))
          ) / (? + r.total_withcomments)) AS score
        FROM stores AS s
        INNER JOIN keywords AS k ON s.id = k.store_id
        INNER JOIN rates AS r ON s.id = r.store_id
        INNER JOIN locations AS l ON s.id = l.store_id
        WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND s.crawler_state IN ('成功', '完成', '超時')
        HAVING distance <= ?
        ORDER BY score DESC
        LIMIT ?
      ", 'dddddddddddddsssii', $userLat, $userLng, $userLat, $C,
        $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C,
        $keyword, $keyword, $keyword, $searchRadius, $RESULT_LIMIT
      );
    } elseif ($city) {
      if ($dist) {
        $stmt = bindPrepare($conn, "
          SELECT DISTINCT
            s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews,
            l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
            ((? * (SELECT AVG(
              COALESCE(environment_rating, 0) * ? +
              COALESCE(product_rating, 0) * ? +
              COALESCE(service_rating, 0) * ? +
              COALESCE(price_rating, 0) * ?
            ) FROM rates WHERE comments_analysis = 1) +
            (r.total_withcomments * (
              COALESCE(r.environment_rating, 0) * ? +
              COALESCE(r.product_rating, 0) * ? +
              COALESCE(r.service_rating, 0) * ? +
              COALESCE(r.price_rating, 0) * ?))
            ) / (? + r.total_withcomments)) AS score
          FROM stores AS s
          INNER JOIN keywords AS k ON s.id = k.store_id
          INNER JOIN rates AS r ON s.id = r.store_id
          INNER JOIN locations AS l ON s.id = l.store_id
          WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND l.city = ? AND l.dist = ? AND s.crawler_state IN ('成功', '完成', '超時')
          ORDER BY score DESC
          LIMIT ?
        ", 'ddddddddddsssssi', $C, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight,
          $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C,
          $keyword, $keyword, $keyword, $city, $dist, $RESULT_LIMIT
        );
      } else {
        $stmt = bindPrepare($conn, "
          SELECT DISTINCT
            s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews,
            l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
            ((? * (SELECT AVG(
              COALESCE(environment_rating, 0) * ? +
              COALESCE(product_rating, 0) * ? +
              COALESCE(service_rating, 0) * ? +
              COALESCE(price_rating, 0) * ?
            ) FROM rates WHERE comments_analysis = 1) +
            (r.total_withcomments * (
              COALESCE(r.environment_rating, 0) * ? +
              COALESCE(r.product_rating, 0) * ? +
              COALESCE(r.service_rating, 0) * ? +
              COALESCE(r.price_rating, 0) * ?))
            ) / (? + r.total_withcomments)) AS score
          FROM stores AS s
          INNER JOIN keywords AS k ON s.id = k.store_id
          INNER JOIN rates AS r ON s.id = r.store_id
          INNER JOIN locations AS l ON s.id = l.store_id
          WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND l.city = ? AND s.crawler_state IN ('成功', '完成', '超時')
          ORDER BY score DESC
          LIMIT ?
        ", 'ddddddddddssssi', $C, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight,
          $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C,
          $keyword, $keyword, $keyword, $city, $RESULT_LIMIT
        );
      }
    } else {
      $stmt = bindPrepare($conn, "
        SELECT DISTINCT
          s.id, s.name, s.tag, s.mark, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews,
          l.city, l.dist, l.details, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
          ((? * (SELECT AVG(
            COALESCE(environment_rating, 0) * ? +
            COALESCE(product_rating, 0) * ? +
            COALESCE(service_rating, 0) * ? +
            COALESCE(price_rating, 0) * ?
          ) FROM rates WHERE comments_analysis = 1) +
          (r.total_withcomments * (
            COALESCE(r.environment_rating, 0) * ? +
            COALESCE(r.product_rating, 0) * ? +
            COALESCE(r.service_rating, 0) * ? +
            COALESCE(r.price_rating, 0) * ?))
          ) / (? + r.total_withcomments)) AS score
        FROM stores AS s
        INNER JOIN keywords AS k ON s.id = k.store_id
        INNER JOIN rates AS r ON s.id = r.store_id
        INNER JOIN locations AS l ON s.id = l.store_id
        WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND s.crawler_state IN ('成功', '完成', '超時')
        ORDER BY score DESC
        LIMIT ?
      ", 'ddddddddddsssi', $C, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight,
        $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C,
        $keyword, $keyword, $keyword, $RESULT_LIMIT
      );
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = [];
    while ($row = $result->fetch_assoc()) $stores[] = $row;
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
        s.id, s.name, s.link, s.tag, s.mark, s.preview_image, f.create_time
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

function getCValue() {
  global $conn;
  $stmt = $conn->prepare("
    SELECT total_withcomments FROM rates
    WHERE comments_analysis = 1
  ");
  $stmt->execute();
  $result = $stmt->get_result();
  $values = [];
  while ($row = $result->fetch_assoc()) {
    $values[] = $row['total_withcomments'];
  }
  $stmt->close();
  $count = count($values);
  if ($count === 0) return 0;
  if ($count % 2 === 1) {
    return $values[floor($count/2)];
  } else {
    return ($values[$count/2-1]+$values[$count/2])/2;
  }
}

  function getBayesianScore($memberWeights, $storeId) {
    global $conn, $_ATMOSPHERE, $_PRICE, $_PRODUCT, $_SERVICE, $RESULT_LIMIT, $_ROUND;
    $C = getCValue();
    $atmosphereWeight = $memberWeights[$_ATMOSPHERE]['weight'];
    $productWeight = $memberWeights[$_PRODUCT]['weight'];
    $serviceWeight = $memberWeights[$_SERVICE]['weight'];
    $priceWeight = $memberWeights[$_PRICE]['weight'];
    $stmt = bindPrepare($conn, "
      SELECT DISTINCT
        ((? * (SELECT AVG(
          COALESCE(environment_rating, 0) * ? +
          COALESCE(product_rating, 0) * ? +
          COALESCE(service_rating, 0) * ? +
          COALESCE(price_rating, 0) * ?
        ) FROM rates WHERE comments_analysis = 1) +
        (r.total_withcomments * (
          COALESCE(r.environment_rating, 0) * ? +
          COALESCE(r.product_rating, 0) * ? +
          COALESCE(r.service_rating, 0) * ? +
          COALESCE(r.price_rating, 0) * ?))
        ) / (? + r.total_withcomments)) AS score
      FROM stores AS s
      INNER JOIN rates AS r ON s.id = r.store_id
      WHERE s.id = ?
      ", 'ddddddddddd', $C, $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight,
        $atmosphereWeight, $productWeight, $serviceWeight, $priceWeight, $C, $storeId
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $store = $result->fetch_assoc();
    $stmt->close();
    return number_format(round($store['score'], $_ROUND), $_ROUND)??null;
  }