<?php
require_once 'dh.php';

$client_id = $_POST['client_id'] ?? '';
if (!$client_id) { echo json_encode(['status'=>'error','message'=>'Missing client_id']); exit; }

$stmt = $pdo->prepare("SELECT * FROM clients WHERE client_id=?");
$stmt->execute([$client_id]);
$client = $stmt->fetch();

if ($client) {
    $pdo->prepare("UPDATE clients SET last_seen=NOW() WHERE client_id=?")->execute([$client_id]);
} else {
    $pdo->prepare("INSERT INTO clients (client_id, created_at, last_seen) VALUES (?, NOW(), NOW())")->execute([$client_id]);
}

echo json_encode(['status'=>'success']);
?>
