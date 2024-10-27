<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  global $conn;

  if (isset($_GET['id'])) {
      $store_id = $_GET['id'];
      $sql = "SELECT image FROM stores WHERE id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $store_id);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($image);
      $stmt->fetch();
      if ($stmt->num_rows > 0 && $image != null) {
          header("Content-Type: image/");
          echo $image;
      } else {
          http_response_code(404);
          echo "Image not found.";
      }
      $stmt->close();
  }