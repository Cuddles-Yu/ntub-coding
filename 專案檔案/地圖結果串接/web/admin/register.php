<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理員註冊 - 評星宇宙</title>
    <link rel="stylesheet" href="/styles/admin-register.css">
</head>
<body>
  <?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
    global $conn;

    #取得所有授權的auth_key
    $stmt = $conn->prepare('SELECT auth_key FROM administrators');
    $stmt->execute();
    $results = $stmt->get_result();
    $authKeys = [];
    while ($row = $results->fetch_assoc()) {
        $authKeys[] = $row['auth_key'];
    }

    $providedAuthKey = isset($_GET['auth']) ? $_GET['auth'] : '';
    if (!$providedAuthKey || !in_array($providedAuthKey, $authKeys)) {
        die("未授權的訪問。請提供有效的 auth_key。");
    }
  ?>
  <div class="container">
      <h2>管理員註冊</h2>
      <form action="./handler/register" method="POST">
          <label for="name">名稱：</label>
          <input type="text" id="name" name="name" required>

          <label for="password">密碼：</label>
          <input type="password" id="password" name="password" required>

          <button type="submit">註冊</button>
      </form>
  </div>
</body>
</html>
