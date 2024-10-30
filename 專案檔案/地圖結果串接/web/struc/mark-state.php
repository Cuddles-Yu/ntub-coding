<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/analysis.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/queries.php';

  function generateMarks($storeId, $state, $target) {
    global $_PREFER, $_NEUTRAL, $_POSITIVE, $_NEGATIVE;
    $categories = [
        $_POSITIVE => ['name' => 'good', 'marks' => getMarks($storeId, $_POSITIVE, $target)],
        $_NEGATIVE => ['name' => 'bad', 'marks' => getMarks($storeId, $_NEGATIVE, $target)],
        $_NEUTRAL => ['name' => 'middle', 'marks' => getMarks($storeId, [$_PREFER, $_NEUTRAL], $target)],
    ];
    $data = $categories[$state];
    $output = '';
    if (empty($data['marks'])) {
      $output .= '(不包含'.$state.'評論標記)';
    }
    foreach($data['marks'] as $index => $keyword) {
      $output .=  '<div class="keywords">';
      $output .=  '<button type="button" class="comment-' . htmlspecialchars($data['name']) . '" onclick="searchCommentsByTarget(this)">';
      $output .=  '<w class="object">' . htmlspecialchars($keyword['object']) . '</w>';
      $output .=  '<w class="count">(' . htmlspecialchars($keyword['count']) . ')</w>';
      $output .=  '</button>';
      $output .=  '</div>';
    }
    return $output;
  }

  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storeId = $_POST['id'] ?? null;
    $target = $_POST['target'] ?? null;
    if ($target === '全部') $target = '';
    echo json_encode([
      'good' => generateMarks($storeId, '正面', $target),
      'bad' => generateMarks($storeId, '負面', $target),
      'middle' => generateMarks($storeId, '中立', $target),
    ]);
  }