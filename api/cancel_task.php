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

    // Check if task exists and is assigned to this executor
    $stmt = $pdo->prepare("
        SELECT t.task_id, t.status, t.client_id, t.executor_id
        FROM tasks t
        WHERE t.task_id = ? 
        AND t.executor_id = ?
        FOR UPDATE
    ");
    $stmt->execute([$data['task_id'], $_SESSION['user_id']]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception('Task not found or you are not assigned to this task');
    }

    if ($task['status'] !== 'in_progress') {
        throw new Exception('Only in-progress tasks can be cancelled');
    }

    // Get the bid ID for this task and executor
    $stmt = $pdo->prepare("
        SELECT bid_id 
        FROM bids 
        WHERE task_id = ? 
        AND executor_id = ?
    ");
    $stmt->execute([$data['task_id'], $_SESSION['user_id']]);
    $bid = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bid) {
        throw new Exception('No bid found for this task');
    }
    
    // Update the bid status to cancelled directly without modifying the schema
    $stmt = $pdo->prepare("UPDATE bids SET status = 'cancelled' WHERE bid_id = ?");
    $stmt->execute([$bid['bid_id']]);
    
    // Verify the update was successful
    $stmt = $pdo->prepare("SELECT status FROM bids WHERE bid_id = ?");
    $stmt->execute([$bid['bid_id']]);
    $finalStatus = $stmt->fetchColumn();
    
    if ($finalStatus !== 'cancelled') {
        // If update failed, log the error and throw exception
        error_log("Failed to update bid status to cancelled for bid_id: {$bid['bid_id']}. Current status: $finalStatus");
        throw new Exception("Failed to update bid status to cancelled. Current status: $finalStatus");
    }
    
    error_log("Successfully updated bid status to cancelled for bid_id: {$bid['bid_id']}");
    
    // Reset ALL other bids for this task back to pending, regardless of current status
    $stmt = $pdo->prepare("
        UPDATE bids
        SET status = 'pending'
        WHERE task_id = ? 
        AND executor_id != ?
    ");
    $stmt->execute([$data['task_id'], $_SESSION['user_id']]);

    // Update task status back to posted and remove executor assignment
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET status = 'posted', 
            executor_id = NULL
        WHERE task_id = ? 
    ");
    $stmt->execute([$data['task_id']]);

    // Add cancellation reason if provided
    $reason = isset($data['reason']) ? trim($data['reason']) : 'No reason provided';
    
    // Check if task_cancellations table exists before inserting
    $tableExists = false;
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'task_cancellations'");
        $tableExists = $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error checking for task_cancellations table: " . $e->getMessage());
    }
    
    if ($tableExists) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO task_cancellations (task_id, executor_id, cancellation_date, reason)
                VALUES (?, ?, NOW(), ?)
            ");
            $stmt->execute([$data['task_id'], $_SESSION['user_id'], $reason]);
            error_log("Added cancellation record to task_cancellations table");
        } catch (Exception $e) {
            // Log error but continue with the process
            error_log("Error inserting into task_cancellations: " . $e->getMessage());
        }
    } else {
        // Log warning but continue with the process
        error_log("Warning: task_cancellations table does not exist. Cancellation reason not saved.");
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Task has been cancelled successfully. The client can now accept other bids.'
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
