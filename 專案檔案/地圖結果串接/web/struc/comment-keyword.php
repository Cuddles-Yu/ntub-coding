<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/struc/comments.php';
  global $conn;

  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = $_POST['q']??'';
    $storeId = $_POST['id']??'0';
    $searchTermWildcard = "%$searchTerm%";
    $stmt = $conn->prepare("
      SELECT * FROM comments
      WHERE store_id = ? AND contents LIKE ?
    ");
    $stmt->bind_param("is", $storeId, $searchTermWildcard);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($comment = $result->fetch_assoc()) {
      $comments[] = $comment;
    }
    $output = generateCommentsByKeyword($comments, $searchTerm);
    echo json_encode(['html' => $output, 'count' => count($comments)]);
  }