<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';  

  $memberWeights = getMemberNormalizedWeight();
  $searchRadius = $_POST['searchRadius'] ?? 1500;
  $keyword = array_key_exists('q', $_POST) ? htmlspecialchars($_POST['q']) : null;
  $mapCenterLat = isset($_POST['mapCenterLat']) ? floatval($_POST['mapCenterLat']) : null;
  $mapCenterLng = isset($_POST['mapCenterLng']) ? floatval($_POST['mapCenterLng']) : null;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (is_null($keyword)) {
      echo json_encode([]);
      return;
    }
    $stores = searchByLocation($keyword, $searchRadius, $mapCenterLat, $mapCenterLng);
    $storeData = [];
    foreach ($stores as $store) {
      $STORE_ID = $store['id'];
      $bayesianScore = getBayesianScore($memberWeights, $STORE_ID);
      $storeData[] = [
        'id' => $store['id'],
        'name' => $store['name'],
        'latitude' => $store['latitude'],
        'longitude' => $store['longitude'],
        'tag' => htmlspecialchars($store['tag']),
        'preview_image' => htmlspecialchars($store['preview_image']),
        'distance' => $store['distance'],
        'score' => $bayesianScore,
        'link' => $store['link'],
        'website' => $store['website'] ?? null,
        'city' => $store['city'],
        'dist' => $store['dist'],
        'details' => $store['details'],
        'mark' => $store['mark'],
        'atmosphere_weight' => $memberWeights[$_ATMOSPHERE]['weight'],
        'product_weight' => $memberWeights[$_PRODUCT]['weight'],
        'service_weight' => $memberWeights[$_SERVICE]['weight'],
        'price_weight' => $memberWeights[$_PRICE]['weight'],
        'limit' => $RESULT_LIMIT
      ];
    }
    usort($storeData, function ($a, $b) {
      return $b['score'] <=> $a['score'];
    });
    $storeData = array_slice($storeData, 0, $RESULT_LIMIT);
    echo json_encode($storeData);
  } else {
    echo json_encode([]);
  }