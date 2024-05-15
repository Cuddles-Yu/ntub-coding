<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>連線</title>
</head>
<body>
<?php
  include 'DB.php';
  $sql = "SELECT * FROM comments"; //指定SQL查詢字串
  echo "SQL 查詢字串: $sql <br/>";

  //送出查詢的SQL指令
  if ($result = mysqli_query($link, $sql)) {
     echo "<b>商家留言關鍵字:</b><br/>";//顯示查詢結果
     while ($row = mysqli_fetch_assoc($result)) {
        echo $row["store_name"] . "-" . $row["contents"] . "<br/>";
     }
     mysqli_free_result($result); //釋放占用記憶體
  } else {
      echo "查詢失敗: " . mysqli_error($link);
  }
  mysqli_close($link); //關閉資料庫連接
?>
</body>
</html>
