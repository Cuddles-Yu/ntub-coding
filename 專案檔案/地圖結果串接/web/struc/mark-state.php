<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/struc/marks.php';

  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $STORE_ID = $_POST['id'] ?? '';
    $target = $_POST['target'] ?? '';
    if ($target === '氛圍') $target = '環境';
    if ($target === '全部') $target = '';
    
    echo json_encode([
      'good' => generateMarks($STORE_ID, '正面', $target),
      'bad' => generateMarks($STORE_ID, '負面', $target),
      'middle' => generateMarks($STORE_ID, '中立', $target),
    ]);
  }