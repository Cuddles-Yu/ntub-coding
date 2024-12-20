<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/analysis.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/struc/comments.php';
  global $conn, $_PREFER, $_NEUTRAL, $_POSITIVE, $_NEGATIVE;

  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $object = $_POST['q']??'';
    $storeId = $_POST['id']??'';
    $requestType = $_POST['type']??'';

    $type = '中立';
    $states = [$_PREFER, $_NEUTRAL];
    if ($requestType === 'comment-good') {
      $type = '正面';
      $states = [$_POSITIVE];
    } else if ($requestType === 'comment-bad') {
      $type = '負面';
      $states = [$_NEGATIVE];
    }
    $allStates = implode(',', array_map(function($state) use ($conn) {
        return "'" . $conn->real_escape_string($state) . "'";
    }, $states));
    $stmt = $conn->prepare("
      SELECT DISTINCT c.* FROM marks AS m
      INNER JOIN comments AS c ON m.comment_id = c.id AND m.store_id = c.store_id
      WHERE m.store_id = ? AND object = ? AND state IN ($allStates)
    ");
    $stmt->bind_param("is", $storeId, $object);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($comment = $result->fetch_assoc()) {
      $comments[] = $comment;
    }
    $output = generateCommentsByMark($comments, $object, $type);
    echo json_encode(['html' => $output, 'count' => count($comments), 'type' => $type]);
  }