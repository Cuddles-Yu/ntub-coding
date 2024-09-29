<?php
  function normalizeDistance($distance) {
    if ($distance < 1000.0) {
        return number_format($distance, 1) . ' 公尺';
    } else {
        return number_format($distance / 1000, 1) . ' 公里';
    }
  }