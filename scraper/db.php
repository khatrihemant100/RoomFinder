<?php
require_once __DIR__ . '/env.php';

// ============================================
// データベース接続
// ============================================
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ データベース接続成功\n";
} catch (PDOException $e) {
    die("❌ 接続エラー: " . $e->getMessage());
}
