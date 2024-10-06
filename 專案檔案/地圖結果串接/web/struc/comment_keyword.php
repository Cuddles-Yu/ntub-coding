<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;  

  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = isset($_POST['q']) ? $_POST['q'] : '';
    $storeId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $searchTermWildcard = "%$searchTerm%";
    $stmt = $conn->prepare(query:
    " SELECT * FROM comments
      WHERE store_id = ? AND contents LIKE ?
    ");
    $stmt->bind_param("is", $storeId, $searchTermWildcard);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    $output = '';
    while ($comment = $result->fetch_assoc()) {
      $comments[] = $comment;
      $output .= '<div class="comment-item" data-rating="'.htmlspecialchars($comment['rating']).'" data-index="'.htmlspecialchars($comment['id']).'">';
      $output .= '<div class="comment-information">';
      $output .= '<img class="avatar" src="images/' . ($comment['contributor_level'] == 0 ? 'icon-member.jpg' : 'icon-contributor.jpg') . '">';
      $output .= '<div class="star-group">';
      for ($i = 0; $i < 5; $i++) {
          $output .= '<img class="star" src="images/' . ($i < $comment['rating'] ? 'icon-star-yellow.png' : 'icon-star-white.png') . '">';
      }
      $output .= '</div>';
      $output .= '<p class="time">時間：' . htmlspecialchars($comment['time']) . '</p>';
      $output .= '</div>';
      $output .= '<div class="comment">';
      $output .= '<p class="comment-text">' . str_replace($searchTerm, '<em class="comment-highlight">' . htmlspecialchars($searchTerm) . '</em>', htmlspecialchars($comment['contents'])) . '</p>';
      $output .= '</div></div>';
    }
    echo json_encode(['html' => $output, 'count' => count($comments)]);
  }