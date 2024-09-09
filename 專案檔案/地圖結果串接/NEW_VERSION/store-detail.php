<?php
// 引入資料庫連接和查詢函數
require 'queries.php';

// 獲取商家名稱
// 從 URL 獲取 storeId 或 storeName
$storeId = $_GET['id'] ?? null;
$storeName = $_GET['name'] ?? null;

if ($storeId || $storeName) {
  // 構建 SQL 查詢
  $sql = "SELECT s.id, s.name, s.description, s.preview_image, s.website, s.tag, s.link, s.website, s.phone_number, 
  r.avg_ratings, r.total_reviews, l.city, l.details, 
      r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude, 
       s.opening_hours 
      FROM stores AS s
      WHERE crawler_state IN ('成功', '完成', '超時')
      INNER JOIN rates AS r ON s.id = r.store_id
      INNER JOIN locations AS l ON s.id = l.store_id
      WHERE s.id = ? OR s.name = ?"
      ;
  
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $storeId, $storeName);
  $stmt->execute();
  $result = $stmt->get_result();
  $store = $result->fetch_assoc();

  if ($store) {
    echo json_encode($store);
    } else {
        echo json_encode(["error" => "商家未找到"]);
    }
} else {
    echo json_encode(["error" => "无效的请求"]);
}
?>
