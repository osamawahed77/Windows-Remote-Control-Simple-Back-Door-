<?php
require_once 'dh.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'] ?? '';
    $command = $_POST['command'] ?? '';

    if (empty($client_id) || empty($command)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO commands (client_id, command_text, status, created_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->execute([$client_id, $command]);
        echo json_encode(['status' => 'success', 'message' => 'Command sent successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status'=>'error','message'=>'Invalid request']);
}
?>
