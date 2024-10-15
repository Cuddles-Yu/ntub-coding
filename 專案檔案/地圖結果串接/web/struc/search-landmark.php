<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$storeData = $input['data'] ?? [];

header('Content-Type: application/json');

if (!empty($storeData)) {
  $landmarkData = [];
  foreach ($storeData as $store) {
    $landmarkData[] = [
      'id' => $store['id'],
      'name' => htmlspecialchars($store['name']),
      'latitude' => $store['latitude'],
      'longitude' => $store['longitude'],
      'tag' => htmlspecialchars($store['tag']),
      'preview_image' => htmlspecialchars($store['preview_image']),
      'score' => $store['score']
    ];
  }
  echo json_encode($landmarkData);
  exit;
} else {
  echo json_encode([]);
  exit;
}