<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';

$_ROUND = 1;

$_ENVIRONMENT = '氛圍';
$_PRICE= '售價';
$_PRODUCT = '產品';
$_SERVICE = '服務';

$_POSITIVE = '正面';
$_NEGATIVE = '負面';
$_PREFER = '喜好';
$_NEUTRAL = '中立';
$_TOTAL = '總計';

function getTargets($store_id) {
    global $conn, $_ENVIRONMENT, $_PRICE, $_PRODUCT, $_SERVICE, $_POSITIVE, $_NEGATIVE, $_PREFER, $_NEUTRAL, $_TOTAL;

    // 查詢相關商店的留言，並篩選掉空值
    $sql = 
    "   SELECT c.environment_state, c.price_state, c.product_state, c.service_state
        FROM comments AS c
        LEFT JOIN stores AS s ON s.id = c.store_id
        WHERE 
            c.store_id = ? AND
            s.crawler_state IN ('完成', '超時', '成功') AND 
            (c.environment_state IS NOT NULL OR c.price_state IS NOT NULL OR c.product_state IS NOT NULL OR c.service_state IS NOT NULL)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // 初始化正面和負面數量的計數
    $positive_count = [
        $_ENVIRONMENT => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];
    $negative_count = [
        $_ENVIRONMENT => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];
    $prefer_count = [
        $_ENVIRONMENT => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];
    $neutral_count = [
        $_ENVIRONMENT => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];
    $total_count = [
        $_ENVIRONMENT => 0,
        $_PRICE => 0,
        $_PRODUCT => 0,
        $_SERVICE => 0
    ];

    // 統計每個指標的正面和負面數量
    while ($row = $result->fetch_assoc()) {        
        $states = [
            'environment_state' => $_ENVIRONMENT,
            'price_state' => $_PRICE,
            'product_state' => $_PRODUCT,
            'service_state' => $_SERVICE,
        ];        
        foreach ($states as $state => $constant) {
            if ($row[$state] === $_POSITIVE) {
                $positive_count[$constant]++;
            } elseif ($row[$state] === $_NEGATIVE) {
                $negative_count[$constant]++;
            } elseif ($row[$state] === $_NEUTRAL || $row[$state] === $_PREFER) {
                $neutral_count[$constant]++;
            }
            if (!is_null($row[$state])) {
                $total_count[$constant]++;
            }
        }
    }

    // 返回正面和負面留言的數量
    return [
        $_POSITIVE => $positive_count,
        $_NEGATIVE => $negative_count,
        $_NEUTRAL => $neutral_count,
        $_TOTAL => $total_count
    ];
}

function normalizeWeights($weights) {
    $total_weight = array_sum($weights);
    if ($total_weight == 0) {
        throw new Exception("總權重不能為零");
    }
    foreach ($weights as $key => $weight) {
        $weights[$key] = $weight / $total_weight;
    }
    return $weights;
}

function getProportionScore($category) {
    global $targetsInfo, $_POSITIVE, $_NEGATIVE, $_ROUND;
    $p = $targetsInfo[$_POSITIVE][$category] ?? 0;
    $n = $targetsInfo[$_NEGATIVE][$category] ?? 0;
    $t = $p + $n;
    $proportion = ($t > 0) ? round($p / $t * 100, $_ROUND) : null;
    $score = (!is_null($proportion)) ? number_format($proportion, $_ROUND) . ' 分' : '(無評價)';
    return [        
        'positive' => $p,
        'negative' => $n,
        'proportion' => $proportion,
        'score' => $score
    ];
}


// 計算並回傳指定商店的貝氏平均分數
function getBayesianScore($user_id, $store_id, $conn) {
    global $_ROUND, $_ENVIRONMENT, $_PRICE, $_PRODUCT, $_SERVICE;
    // 取得使用者的權重設定
    $member_sql = "
        SELECT environment_weight, price_weight, product_weight, service_weight
        FROM members
        WHERE id = ?
    ";
    $stmt = $conn->prepare($member_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $member_result = $stmt->get_result();
    $weights = $member_result->fetch_assoc();

    // 檢查是否獲取到權重，如果無法獲取，設置預設權重為1
    if (!$weights) {
        $weights = [
            'environment_weight' => 1,
            'price_weight' => 1,
            'product_weight' => 1,
            'service_weight' => 1
        ];
    }

    // 定義從資料庫取得的權重或預設權重
    $target_weights = [
        $_ENVIRONMENT => $weights['environment_weight'],
        $_PRICE => $weights['price_weight'],
        $_PRODUCT => $weights['product_weight'],
        $_SERVICE => $weights['service_weight']
    ];

    // 正規化權重
    $normalized_weights = normalizeWeights($target_weights);

    // 取得所有商店的四大指標 rating 數據
    $all_stores_sql = 
    "   SELECT store_id, environment_rating, product_rating, service_rating, price_rating, total_withcomments, total_reviews 
        FROM rates
        WHERE comments_analysis = 1
    ";
    $all_result = $conn->query($all_stores_sql);

    $stores = [];
    $store_scores = [];  // 用來存儲所有商店的分數
    $total_stores = 0;

    // 計算所有商店的基礎分數
    if ($all_result->num_rows > 0) {
        while ($row = $all_result->fetch_assoc()) {
            $temp_store_id = $row['store_id'];

            // 計算基礎分數，根據各項指標的正規化權重
            $store_score = 
                $row['environment_rating'] * $normalized_weights[$_ENVIRONMENT] +
                $row['product_rating'] * $normalized_weights[$_PRODUCT] +
                $row['service_rating'] * $normalized_weights[$_SERVICE] +
                $row['price_rating'] * $normalized_weights[$_PRICE];

            // 保存該商店的分數及相關數據
            $stores[$temp_store_id] = [
                'score' => $store_score,
                'total_withcomments' => $row['total_withcomments'],
                'total_reviews' => $row['total_reviews']
            ];

            // 保存所有商店的分數用於後續計算平均分數
            $store_scores[] = $store_score;
            $total_stores++;
        }
    }

    // 確保有商店數據
    if ($total_stores == 0) {
        return "沒有商店數據可用";
    }

    // 計算全域平均分數
    $overall_mean_score = array_sum($store_scores) / $total_stores;

    // 計算 C 值 (基於 total_withcomments 的中位數)
    $total_withcomments = array_column($stores, 'total_withcomments');
    sort($total_withcomments);
    $C = $total_withcomments[intval(count($total_withcomments) / 2)+1];

    // 取得指定 store_id 的數據
    if (!isset($stores[$store_id])) {
        return "???";
    }

    $store_data = $stores[$store_id];
    $store_score = $store_data['score'];
    $total_withcomments = $store_data['total_withcomments'];

    // 檢查 C 值與 total_withcomments 的大小
    if ($C + $total_withcomments == 0) {
        throw new Exception("C 值和 total_withcomments 加總不能為 0");
    }

    // 計算指定商店的貝氏平均分數
    $bayesian_score = (($C * $overall_mean_score) + ($store_score * $total_withcomments)) / ($C + $total_withcomments);

    // 回傳貝氏平均分數
    return number_format(round($bayesian_score, $_ROUND), $_ROUND);
}
