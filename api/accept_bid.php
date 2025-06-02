<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['bid_id']) || !isset($data['task_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bid ID and Task ID are required']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Check if task exists and belongs to the client
    $stmt = $pdo->prepare("
        SELECT t.task_id, t.status, t.client_id
        FROM tasks t
        WHERE t.task_id = ? 
        AND t.client_id = ?
        FOR UPDATE
    ");
    $stmt->execute([$data['task_id'], $_SESSION['user_id']]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception('Task not found or you are not authorized to manage this task');
    }

    if ($task['status'] !== 'posted') {
        throw new Exception('This task is no longer available for bid acceptance');
    }

    // Get the bid and verify it belongs to the task
    // Allow accepting any bid (pending, rejected) as long as it's not cancelled
    $stmt = $pdo->prepare("
        SELECT b.bid_id, b.executor_id, b.bid_amount
        FROM bids b
        WHERE b.bid_id = ? 
        AND b.task_id = ?
        AND b.status != 'cancelled'
        FOR UPDATE
    ");
    $stmt->execute([$data['bid_id'], $data['task_id']]);
    $bid = $stmt->fetch();

    if (!$bid) {
        throw new Exception('Bid not found or is not available for acceptance');
    }

    // Update the bid status to accepted
    $stmt = $pdo->prepare("
        UPDATE bids
        SET status = 'accepted'
        WHERE bid_id = ?
    ");
    $stmt->execute([$data['bid_id']]);

    // Keep other bids as pending instead of rejecting them
    // They will only be rejected when the task is completed
    // This allows the client to choose another bid if the executor cancels the task

    // Update task status and assign executor
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET status = 'in_progress', 
            executor_id = ?,
            accepted_at = NOW() 
        WHERE task_id = ? 
    ");
    $stmt->execute([$bid['executor_id'], $data['task_id']]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Bid accepted successfully. The task has been assigned to the executor.']);
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
