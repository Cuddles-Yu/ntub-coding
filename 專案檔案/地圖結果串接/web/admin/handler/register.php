<?php  
  require_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
  global $conn;
  
  function generateAuthKey($length) {
    #排除容易混淆的字元
    $characters = 'abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $authKey = generateAuthKey(40);
    $sql = 
    " INSERT INTO administrators
      VALUE ('$name', '$hashedPassword', '$authKey', DEFAULT)
    ";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute()) {
        echo "<p>管理員註冊成功</p>";
        echo "<div><button onclick='returnToRegister()'>繼續註冊帳號</button></div>";
        echo "<div><button onclick='copyAuthKey()'>複製 AUTH KEY</button></div>";
    } else {
        echo "註冊失敗: " . $conn->error;
    }
    $stmt->close();
}
?>
  
<script>
  function returnToRegister() {
    window.location.href = '../register?auth=<?=$authKey?>';
  }
  function copyAuthKey() {
    var tempInput = document.createElement("textarea");
    tempInput.value = '<?=$authKey?>';
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999);  // 適應移動裝置
    document.execCommand("copy");
    document.body.removeChild(tempInput);
  }
</script>