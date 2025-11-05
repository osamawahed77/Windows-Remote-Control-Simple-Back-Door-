<?php
require_once 'dh.php';

$command_id = $_POST['command_id'] ?? '';
$stdout = $_POST['stdout'] ?? '';
$stderr = $_POST['stderr'] ?? '';

if (!$command_id) { echo json_encode(['status'=>'error','message'=>'Missing command_id']); exit; }

$stmt = $pdo->prepare("INSERT INTO command_results (command_id, stdout, stderr, created_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$command_id, $stdout, $stderr]);

$pdo->prepare("UPDATE commands SET status='done' WHERE id=?")->execute([$command_id]);

echo json_encode(['status'=>'success']);
?>
