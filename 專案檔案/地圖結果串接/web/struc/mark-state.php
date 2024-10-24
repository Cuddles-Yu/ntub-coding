<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/struc/marks.php';

  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storeId = $_POST['id'] ?? '';
    $target = $_POST['target'] ?? '';
    if ($target === '氛圍') $target = '環境';
    if ($target === '全部') $target = '';
    
    echo json_encode([
      'good' => generateMarks($storeId, '正面', $target),
      'bad' => generateMarks($storeId, '負面', $target),
      'middle' => generateMarks($storeId, '中立', $target),
    ]);
  }