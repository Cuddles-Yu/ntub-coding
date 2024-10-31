<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/analysis.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/queries.php';

  function generateMarks($storeId, $state, $target, $limit) {
    global $_PREFER, $_NEUTRAL, $_POSITIVE, $_NEGATIVE;
    if ($state === $_POSITIVE) {
      $data = ['name' => 'good', 'results' => getMarks($storeId, $_POSITIVE, $target, $limit)];
    } elseif ($state === $_NEGATIVE) {
      $data = ['name' => 'bad', 'results' => getMarks($storeId, $_NEGATIVE, $target, $limit)];
    } elseif ($state === $_NEUTRAL) {
      $data = ['name' => 'middle', 'results' => getMarks($storeId, [$_PREFER, $_NEUTRAL], $target, $limit)];
    }
    $output = '';
    if (empty($data['results']['marks'])) {
      $output .= '(不包含'.$state.'評論標記)';
    }
    foreach($data['results']['marks'] as $index => $keyword) {
      $output .=  '<div class="keywords">';
      $output .=  '<button type="button" class="comment-' . htmlspecialchars($data['name']) . '" onclick="searchCommentsByTarget(this)">';
      $output .=  '<w class="object">' . htmlspecialchars($keyword['object']) . '</w>';
      $output .=  '<w class="count">(' . htmlspecialchars($keyword['count']) . ')</w>';
      $output .=  '</button>';
      $output .=  '</div>';
    }
    if ($data['results']['showMore']) {
      $output .= '<div class="keywords">';
      $output .= '<button type="button" class="comment-more" onclick="generateMark(\''.$data['name'].'\',0)">';
      $output .= '<w class="object">顯示更多...</w>';
      $output .= '</button>';
      $output .= '</div>';
    } else {
      if (!$data['results']['endResult']) {
        $output .= '<div class="keywords">';
        $output .= '<button type="button" class="comment-more" onclick="generateMark(\''.$data['name'].'\',1)">';
        $output .= '<w class="object">顯示較少...</w>';
        $output .= '</button>';
        $output .= '</div>';
      };
    }
    return $output;
  }

  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storeId = $_POST['id']??null;
    $target = $_POST['target']??null;
    $limit = $_POST['limit']==='1'?true:false;
    if ($target === '全部') $target = '';
    echo json_encode([
      'good' => generateMarks($storeId, $_POSITIVE, $target, $limit),
      'bad' => generateMarks($storeId, $_NEGATIVE, $target, $limit),
      'middle' => generateMarks($storeId, $_NEUTRAL, $target, $limit),
    ]);
  }