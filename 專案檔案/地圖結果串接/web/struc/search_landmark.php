<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';

  function searchStores($keyword, $userLat, $userLng)
  {
      global $conn;
      $keyword = "%$keyword%";
      if ($userLat&&$userLng) {
        $stmt = bindPrepare($conn,  
        " SELECT 
          DISTINCT s.id, s.name, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews, 
          l.city, l.dist, l.details, s.tag, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude,
          (6371000*acos(cos(radians(?))*cos(radians(l.latitude))*cos(radians(l.longitude)-radians(?))+sin(radians(?))*sin(radians(l.latitude)))) AS distance
          FROM stores AS s
          INNER JOIN keywords AS k ON s.id = k.store_id
          INNER JOIN rates AS r ON s.id = r.store_id
          INNER JOIN locations AS l ON s.id = l.store_id
          WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND s.crawler_state IN ('成功', '完成', '超時')
          HAVING distance <= 1500
          ORDER BY distance, r.avg_ratings DESC, r.total_reviews DESC
        ", 'dddsss', $userLat, $userLng, $userLat, $keyword, $keyword, $keyword);
      } else {
        $stmt = bindPrepare($conn,
        " SELECT 
          DISTINCT s.id, s.name, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews, 
          l.city, l.dist, l.details, s.tag, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude
          FROM stores AS s
          INNER JOIN keywords AS k ON s.id = k.store_id
          INNER JOIN rates AS r ON s.id = r.store_id
          INNER JOIN locations AS l ON s.id = l.store_id
          WHERE (s.name LIKE ? OR s.tag LIKE ? OR k.word LIKE ?) AND s.crawler_state IN ('成功', '完成', '超時')
          ORDER BY r.avg_ratings DESC, r.total_reviews DESC
        ", 'sss', $keyword, $keyword, $keyword);
      }
      $stmt->execute();
      $result = $stmt->get_result();
      $stores = [];
      while ($row = $result->fetch_assoc()) {
          // 確保經緯度數據有效
          if (is_numeric($row['latitude']) && is_numeric($row['longitude'])) {
              $stores[] = $row;
          }
      }
      return $stores;
  }

  global $conn;
  $keyword = array_key_exists('q', $_POST) ? htmlspecialchars($_POST['q']) : null;
  $mapCenterLat = isset($_POST['mapCenterLat']) ? floatval($_POST['mapCenterLat']) : null;
  $mapCenterLng = isset($_POST['mapCenterLng']) ? floatval($_POST['mapCenterLng']) : null;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (is_null($keyword)) {
        echo json_encode(['error' => '請提供搜尋關鍵字']);
        exit;
    }
    $stores = searchStores($keyword, $mapCenterLat, $mapCenterLng);

    $data = [];
    foreach ($stores as $store) {
        $data[] = [
            'id' => $store['id'],
            'name' => $store['name'],
            'latitude' => $store['latitude'],
            'longitude' => $store['longitude'],
            'tag' => $store['tag'],
            'preview_image' => $store['preview_image']
        ];
    }
    echo json_encode($data);
    exit;
  }

  // 如果沒有提供搜尋關鍵字，返回所有商家資料
  function getAllStores()
  {
      global $conn;
      $stmt = $conn->prepare(query: 
      " SELECT 
          s.id AS store_id, s.name AS store_name, s.link AS store_link, s.preview_image AS store_preview_image,
          l.latitude AS location_latitude, l.longitude AS location_longitude, s.tag AS tag, r.real_rating AS rate_real_rating,
          r.total_samples AS sample_ratings, r.total_withcomments AS total_withcomments, r.avg_ratings AS avg_ratings
        FROM stores AS s
        LEFT JOIN locations AS l ON s.id = l.store_id
        LEFT JOIN rates AS r ON s.id = r.store_id
        WHERE s.crawler_state IN ('成功', '完成', '超時')
      ");
      #無注入
      $stmt->execute();
      $result = $stmt->get_result();

      $data = [];
      while ($row = $result->fetch_assoc()) {
          $data[] = [
              'id' => $row['store_id'],
              'name' => $row['store_name'],
              'link' => $row['store_link'],
              'preview_image' => $row['store_preview_image'],
              'latitude' => floatval($row['location_latitude']),
              'longitude' => floatval($row['location_longitude']),
              'tag' => $row['tag'],
              'rating' => floatval($row['avg_ratings']),
              'sample_ratings' => intval($row['sample_ratings']),
              'total_withcomments' => intval($row['total_withcomments'])
          ];
      }
      return $data;
  }
  if ($_SERVER["REQUEST_METHOD"] == "GET") {
      $data = getAllStores();
      echo json_encode($data);
      exit;
  }