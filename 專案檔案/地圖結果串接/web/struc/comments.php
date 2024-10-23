<?php
  function generateCommentsByKeyword($comments, $searchTerm) {
    $output = '';
    foreach ($comments as $comment) {
      $output .= '<div class="comment-item normal-side" data-rating="'.htmlspecialchars($comment['rating']).'" data-index="' . htmlspecialchars($comment['id']) . '">';
      $output .= '<div class="comment-information">';
      $output .= '<img class="avatar" src="images/' . ($comment['contributor_level'] == 0 ? 'icon-member.png' : 'icon-contributor.png') . '">';
      $output .= '<div class="star-group">';
      for ($i = 0; $i < 5; $i++) {
        $output .= '<img class="star" src="images/' . ($i < $comment['rating'] ? 'icon-star-yellow.png' : 'icon-star-white.png') . '">';
      }
      $output .= '</div>';
      $output .= '<p class="time">時間：' . htmlspecialchars($comment['time']) . '</p>';
      $output .= '</div>';
      $output .= '<div class="comment">';
      $highlightedText = str_replace($searchTerm, '<em class="comment-highlight">' . htmlspecialchars($searchTerm) . '</em>', htmlspecialchars($comment['contents']));
      $output .= '<p class="comment-text">' . $highlightedText . '</p>';
      $output .= '</div>';
      $output .= '</div>';
    }
    return $output;
  }

  function generateCommentsByMark($comments, $term, $type) {
    $output = '';
    $side = 'middle';
    if ($type === '正面') {
      $side = 'good';
    } elseif ($type === '負面') {
      $side = 'bad';
    }
    foreach ($comments as $comment) {
      $output .= '<div class="comment-item '.$side.'-side" data-rating="'.htmlspecialchars($comment['rating']).'" data-index="' . htmlspecialchars($comment['id']) . '">';
      $output .= '<div class="comment-information">';
      $output .= '<img class="avatar" src="images/' . ($comment['contributor_level'] == 0 ? 'icon-member.png' : 'icon-contributor.png') . '">';
      $output .= '<div class="star-group">';
      for ($i = 0; $i < 5; $i++) {
        $output .= '<img class="star" src="images/' . ($i < $comment['rating'] ? 'icon-star-yellow.png' : 'icon-star-white.png') . '">';
      }
      $output .= '</div>';
      $output .= '<p class="time">時間：' . htmlspecialchars($comment['time']) . '</p>';
      $output .= '</div>';
      $output .= '<div class="comment">';
      $highlightedText = str_replace($term, '<em class="comment-highlight">' . htmlspecialchars($term) . '</em>', htmlspecialchars($comment['contents']));
      $output .= '<p class="comment-text">' . $highlightedText . '</p>';
      $output .= '</div>';
      $output .= '</div>';
    }
    return $output;
  }