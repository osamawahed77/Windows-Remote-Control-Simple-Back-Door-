<?php
require_once 'dh.php';

$client_id = $_GET['client_id'] ?? '';
if (!$client_id) { echo json_encode(['status'=>'error','message'=>'Missing client_id']); exit; }

$stmt = $pdo->prepare("SELECT * FROM commands WHERE client_id=? AND status='pending' ORDER BY created_at ASC LIMIT 1");
$stmt->execute([$client_id]);
$command = $stmt->fetch();

if ($command) {
    $pdo->prepare("UPDATE commands SET status='in_progress' WHERE id=?")->execute([$command['id']]);
    echo json_encode(['status'=>'success','command'=>$command]);
} else {
    echo json_encode(['status'=>'no_command']);
}
?>
