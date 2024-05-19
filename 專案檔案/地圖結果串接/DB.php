<?php
// 建立 MySQL 資料庫連接
$conn = new mysqli("localhost", "root", "", "mapdb");

// 檢查連接
if ($conn->connect_error) {
    die("無法開啟 MySQL 資料庫連接: " . $conn->connect_error);
}
?>
