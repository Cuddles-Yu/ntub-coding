<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';

  global $conn;
  $keyword = array_key_exists('q', $_POST) ? htmlspecialchars($_POST['q']) : null;
  $mapCenterLat = isset($_POST['mapCenterLat']) ? floatval($_POST['mapCenterLat']) : null;
  $mapCenterLng = isset($_POST['mapCenterLng']) ? floatval($_POST['mapCenterLng']) : null;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stores = searchByLocation($keyword, $mapCenterLat, $mapCenterLng, $RESULT_LIMIT);
    $data = [];
    foreach ($stores as $store) {
      $data[] = [
        'id' => $store['id'],
        'name' => $store['name'],
        'latitude' => $store['latitude'],
        'longitude' => $store['longitude'],
        'tag' => $store['tag'],
        'preview_image' => $store['preview_image'],
        'distance' => $store['distance'],
        'score' => getBayesianScore(getMemberNormalizedWeight(), $store['id'])
      ];
    }
    echo json_encode($data);
    exit;
  }