<?php
// ============================================
// DB接続
// ============================================
require_once __DIR__ . '/db.php';

$sql = "DELETE FROM properties;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

echo "データを削除しました。\n";
?>