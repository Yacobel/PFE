<?php
session_start();
require_once '../db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
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

    // Check if task exists, belongs to the client, and is in_progress
    $stmt = $pdo->prepare("
        SELECT t.task_id, t.status, t.client_id, t.executor_id, t.budget, 
               b.bid_amount, u.name as executor_name, u.email as executor_email
        FROM tasks t
        LEFT JOIN bids b ON t.task_id = b.task_id AND b.executor_id = t.executor_id AND b.status = 'accepted'
        LEFT JOIN users u ON t.executor_id = u.id_user
        WHERE t.task_id = ? 
        AND t.client_id = ?
        FOR UPDATE
    ");
    $stmt->execute([$data['task_id'], $_SESSION['user_id']]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception('Task not found or you are not authorized to make payment for this task');
    }

    if ($task['status'] !== 'in_progress') {
        throw new Exception('This task is not in progress. Payment can only be made for in-progress tasks.');
    }

    if (!$task['executor_id']) {
        throw new Exception('No executor is assigned to this task');
    }

    // In a real system, you would integrate with a payment gateway here
    // For this demo, we'll simulate a successful payment

    // Create payment record
    $payment_amount = $task['bid_amount'] ? $task['bid_amount'] : $task['budget'];
    
    $stmt = $pdo->prepare("
        INSERT INTO payments (task_id, client_id, executor_id, amount, payment_date, status)
        VALUES (?, ?, ?, ?, NOW(), 'completed')
    ");
    $stmt->execute([
        $data['task_id'], 
        $_SESSION['user_id'], 
        $task['executor_id'], 
        $payment_amount
    ]);
    $payment_id = $pdo->lastInsertId();

    // Update task status to completed
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET status = 'completed', 
            completed_at = NOW() 
        WHERE task_id = ? 
    ");
    $stmt->execute([$data['task_id']]);
    
    // Update the accepted bid to 'done' status - use a more specific query
    $stmt = $pdo->prepare("
        UPDATE bids
        SET status = 'done'
        WHERE task_id = ?
        AND executor_id = ?
    ");
    $stmt->execute([$data['task_id'], $task['executor_id']]);
    
    // Now that the task is completed, reject all other bids
    $stmt = $pdo->prepare("
        UPDATE bids
        SET status = 'rejected'
        WHERE task_id = ?
        AND executor_id != ?
        AND status = 'pending'
    ");
    $stmt->execute([$data['task_id'], $task['executor_id']]);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Payment processed successfully. The task has been marked as completed.',
        'payment_id' => $payment_id,
        'amount' => $payment_amount,
        'executor_name' => $task['executor_name'],
        'executor_email' => $task['executor_email']
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
