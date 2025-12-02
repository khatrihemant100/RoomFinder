<?php
// $file_name = "suumo_13_13104.json"; // ← 使用するJSONファイル名
$file_name = "suumo_14.json"; // ← 使用するJSONファイル名

require_once 'db.php';

// ===========================================
// JSONファイルの読み込み
// ===========================================
$file = "data/" . $file_name;
if (!file_exists($file)) {
    die("❌ ファイルが存在しません: " . $file);
}
$json = file_get_contents($file);
$data = json_decode($json, true);

if (!$data) {
    die("❌ JSONが読み込めませんでした");
}

// ===========================================
// データ挿入
// ===========================================
$stmt = $conn->prepare("
    INSERT INTO properties (user_id, title, location, price, type, description, image_url, status, train_station)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'available', ?)
");

// 型: i=整数, s=文字列
// user_id=仮でnull扱い
foreach ($data as $item) {
    $user_id = 1; // 仮のユーザーID
    $title = $item["title"];
    $location = $item["address"];
    $price = $item["rent"];
    $train_station = $item["access"];
    $type = $item["layout"];

    // description に詳細まとめる
    $description = "アクセス: " . $item["access"] .
        "\n築年数: " . $item["age"] . "年" .
        "\n面積: " . $item["area"] . "㎡" .
        "\n敷金: " . ($item["deposit"] ?? "なし") .
        "\n礼金: " . ($item["key_money"] ?? "なし") .
        "\n管理費: " . ($item["management_fee"] ?? "なし");

    $image = null; // 画像URLがないので仮

    $stmt->bind_param("ississss", $user_id, $title, $location, $price, $type, $description, $image, $train_station);
    $stmt->execute();
}

echo "✅ " . count($data) . "件のデータを挿入しました。\n";

$stmt->close();
$conn->close();
