<?php
require_once 'db.php';

$file = 'docs/roomfinder.sql';

$sql = file_get_contents($file);
if ($conn->multi_query($sql) === TRUE) {
    echo "✅ テーブルが正常に作成されました。\n";
} else {
    echo "❌ エラー: " . $conn->error . "\n";
}
$conn->close();