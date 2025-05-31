<?php
session_start();
require_once '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['task_id']) || !isset($data['recipient_id']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate message content
if (empty(trim($data['message']))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit;
}

try {
    // Check if task exists
    $stmt = $pdo->prepare("SELECT client_id, executor_id FROM tasks WHERE task_id = ?");
    $stmt->execute([$data['task_id']]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception('Task not found');
    }

    // Verify that the sender and recipient are involved in the task
    $sender_id = $_SESSION['user_id'];
    $recipient_id = $data['recipient_id'];

    if ($sender_id !== $task['client_id'] && $sender_id !== $task['executor_id']) {
        throw new Exception('You are not authorized to send messages for this task');
    }

    if ($recipient_id !== $task['client_id'] && $recipient_id !== $task['executor_id']) {
        throw new Exception('Invalid recipient');
    }

    // Insert message
    $stmt = $pdo->prepare("
        INSERT INTO messages (
            task_id,
            sender_id,
            recipient_id,
            message,
            created_at
        ) VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $data['task_id'],
        $sender_id,
        $recipient_id,
        trim($data['message'])
    ]);

    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
