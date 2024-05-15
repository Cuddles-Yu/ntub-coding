<?php
    // 建立 MySQL 資料庫連接
    $link = mysqli_connect("localhost", "root", "", "mapdb")
        or die("無法開啟 MySQL 資料庫連接!<br/>");
        
?>