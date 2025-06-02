<?php
session_start();
require_once '../config/db.php';

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

// Get bid amount and proposal text if provided
$bid_amount = isset($data['bid_amount']) ? floatval($data['bid_amount']) : 0;
$proposal_text = isset($data['proposal_text']) ? trim($data['proposal_text']) : '';

try {
    // Start transaction
    $pdo->beginTransaction();

    // Check if task exists and is available
    $stmt = $pdo->prepare("
        SELECT t.task_id, t.status, t.budget, 
               (SELECT COUNT(*) FROM bids WHERE task_id = t.task_id AND executor_id = ?) as has_bid
        FROM tasks t
        WHERE t.task_id = ? 
        FOR UPDATE
    ");
    $stmt->execute([$_SESSION['user_id'], $data['task_id']]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception('Task not found');
    }

    if ($task['status'] !== 'posted') {
        throw new Exception('Task is no longer available for bidding');
    }
    
    // Check if executor has already bid on this task
    if ($task['has_bid'] > 0) {
        throw new Exception('You have already submitted a bid for this task');
    }

    // If no bid amount provided, use the task budget
    if ($bid_amount <= 0) {
        $bid_amount = $task['budget'];
    }

    // Insert bid into bids table
    $stmt = $pdo->prepare("
        INSERT INTO bids (task_id, executor_id, bid_amount, proposal_text, bid_date, status)
        VALUES (?, ?, ?, ?, NOW(), 'pending')
    ");
    $stmt->execute([$data['task_id'], $_SESSION['user_id'], $bid_amount, $proposal_text]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Bid submitted successfully. The client will review your proposal.']);
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
