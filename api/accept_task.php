<?php
session_start();
require_once '../db.php';

// Check if user is logged in and is an executor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'executor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['task_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Task ID is required']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Check if task exists and is available
    $stmt = $pdo->prepare("
        SELECT status 
        FROM tasks 
        WHERE task_id = ? 
        FOR UPDATE
    ");
    $stmt->execute([$data['task_id']]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception('Task not found');
    }

    if ($task['status'] !== 'posted') {
        throw new Exception('Task is no longer available');
    }

    // Update task status and assign executor
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET status = 'in_progress', 
            executor_id = ?,
            accepted_at = NOW() 
        WHERE task_id = ? 
        AND status = 'posted'
    ");
    $stmt->execute([$_SESSION['user_id'], $data['task_id']]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Task accepted successfully']);
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
