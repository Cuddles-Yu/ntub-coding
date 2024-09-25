<?php
  function colored_echo($color, $title, $text) {
      echo "<w style='color:$color;'>[$title] </w>$text";
  }
  $auth_key = isset($_GET['auth']) ? $_GET['auth'] : null;

  $allowed_keys = [
    'mnkrZSaAQPhmqfASJzdg6dizCarbLsJGpTnWb7bp' => '余奕博',
    'cmZC675Bs2ATyxH69yT2bZDzPD3JVAuSbqPkMpBQ' => '邱綺琳',
    'pFhqky4Kjkw93buGe2uhKVUZH9Hs87QUXNr9zgMP' => '陳彥瑾',
    'Rw7MtGsLfBgum9pFw9GBQi2ACNqiJLx6SbQ2vuzh' => '鄧惠中',
];

  if (array_key_exists($auth_key, $allowed_keys)) {
      $admin_name = $allowed_keys[$auth_key];
      $output = shell_exec('python.exe ./struc/git_pull.py 2>&1');
      echo "已驗證的授權 -> $admin_name<br><br>$output";
  } else {
      colored_echo('red', 'INVALID', 'Unauthorized access');
  }