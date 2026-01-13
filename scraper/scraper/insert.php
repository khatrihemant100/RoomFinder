<?php
// ============================================
// DB接続
// ============================================
require_once __DIR__ . '/db.php';

// ============================================
// JSONファイル読み込み
// ============================================
$jsonFile = __DIR__ . '/data/suumo_kanagawa.json';

if (!file_exists($jsonFile)) {
    die("❌ JSONファイルが見つかりません: $jsonFile");
}

$jsonData = file_get_contents($jsonFile);
$properties = json_decode($jsonData, true);

if ($properties === null) {
    die("❌ JSONの読み込みに失敗しました。");
}

// ============================================
// データ挿入
// ============================================
$sql = "INSERT INTO properties
(user_id, title, location, train_station, price, status, description, image_url, type)
VALUES
(:user_id, :title, :location, :train_station, :price, :status, :description, :image_url, :type)";

$stmt = $pdo->prepare($sql);

$count = 0;
foreach ($properties as $p) {
    $stmt->execute([
        ':user_id' => 1,
        ':title' => $p['title'] ?? null,
        ':location' => $p['address'] ?? null,
        ':train_station' => $p['access'] ?? null,
        ':price' => $p['rent'] ?? 0,
        ':status' => "",
        ':description' => "",
        ':image_url' => "",
        ':type' => $p['layout'] ?? null,
    ]);
    $count++;
}

echo "✅ {$count} 件のデータを挿入しました。\n";
?>
