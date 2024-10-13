<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';

  $STORE_ID = $_GET['storeId'] ?? null;

  if (is_null($STORE_ID)) {
      echo json_encode(['error' => '無效的店家 ID']);
      exit;
  }

  // 查詢資料
  function getStoreData($storeId) {
      global $conn;
      $stmt = bindPrepare($conn, 
      " SELECT 
          s.id AS store_id,
          s.name AS store_name,
          s.link AS store_link,
          s.preview_image AS store_preview_image,
          l.latitude AS location_latitude,
          l.longitude AS location_longitude, 
          s.tag AS tag,
          r.real_rating AS rate_real_rating,      
          r.total_samples AS sample_ratings,      
          r.total_withcomments AS total_withcomments,
          r.avg_ratings AS avg_ratings
        FROM stores AS s
        JOIN locations AS l ON s.id = l.store_id
        JOIN rates AS r ON s.id = r.store_id
        WHERE s.id = ? AND crawler_state IN ('成功', '完成', '超時');
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();

      $data = array();
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              $data[] = array(
                  'id' => $row['store_id'],
                  'name' => $row['store_name'],
                  'link' => $row['store_link'],
                  'preview_image' => $row['store_preview_image'],
                  'latitude' => floatval($row['location_latitude']), 
                  'longitude' => floatval($row['location_longitude']),
                  'tag' => $row['tag'],
                  'rating' => floatval($row['avg_ratings']),
                  'sample_ratings' =>intval($row['sample_ratings'] ),
                  'total_withcomments' =>intval($row['total_withcomments'] ),
              );
          }
      } else {
          echo json_encode(['error' => '找不到店家資訊']);
          exit;
      }
      $conn->close();
      return $data;
  }
  
  header('Content-Type: application/json');
  echo json_encode(getStoreData($STORE_ID));
