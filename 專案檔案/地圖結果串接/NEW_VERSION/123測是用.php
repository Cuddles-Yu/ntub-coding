<?php
require_once 'DB.php';
require_once 'queries.php';

// 查詢資料
function getStoreData() {
    global $conn;

    $sql = "
        SELECT 
            stores.name AS store_name,
            stores.preview_image AS store_preview_image,
            locations.latitude AS location_latitude,
            locations.longitude AS location_longitude, 
            rates.real_rating AS rate_real_rating,
            rates.total_samples AS sample_ratings,
            rates.total_reviews AS total_reviews

        FROM 
            stores
        JOIN 
            locations ON stores.id = locations.store_id
        JOIN 
            rates ON stores.id = rates.store_id;
    ";

    $result = $conn->query($sql);
    if ($result === false) {
        die("SQL Error: " . $conn->error);
    }

    $data = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = array(
                'name' => $row['store_name'],
                'preview_image' => $row['store_preview_image'],
                'Latlng' => array(floatval($row['location_longitude']), floatval($row['location_latitude'])), //經度再緯度
                'rating' => floatval($row['rate_real_rating']), //(綜合)評分，目前暫時以真實評分代替
                'sample_ratings' => $row['sample_ratings'], //評論樣本數
                'total_reviews' => $row['total_reviews'], //評論總數
            );
        }
    } else {
        echo json_encode([]);
        exit();
    }

    $conn->close();
    return $data;
}

// 將數據轉換為 JSON 格式並輸出
header('Content-Type: application/json');
echo json_encode(getStoreData());
?>
