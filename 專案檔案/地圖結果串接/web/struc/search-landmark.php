<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// 獲取商家數據
$storeData = $input['data'] ?? [];

header('Content-Type: application/json');

if (!empty($storeData)) {
    $landmarkData = [];
    
    foreach ($storeData as $store) {
        // 生成地標所需的資料
        $landmarkData[] = [
            'id' => $store['id'],
            'name' => htmlspecialchars($store['name']),
            'latitude' => $store['latitude'],
            'longitude' => $store['longitude'],
            'tag' => htmlspecialchars($store['tag']),
            'preview_image' => htmlspecialchars($store['preview_image']),
            'distance' => $store['distance'],
            'score' => $store['score']
        ];
    }

    // 將地標資料返回給前端
    echo json_encode($landmarkData);
    exit;
} else {
    echo json_encode([]);
    exit;
}
