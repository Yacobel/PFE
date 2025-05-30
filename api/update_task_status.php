<?php
session_start();
require_once '../db.php';

// Check if user is logged in and is an executor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'executor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['task_id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$task_id = $data['task_id'];
$new_status = $data['status'];

// Validate status
$valid_statuses = ['assigned', 'in_progress', 'completed', 'cancelled'];
if (!in_array($new_status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Check if the task belongs to the executor
    $stmt = $pdo->prepare("SELECT executor_id FROM tasks WHERE task_id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch();

    if (!$task || $task['executor_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You are not authorized to update this task']);
        exit;
    }

    // Update task status
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
    $stmt->execute([$new_status, $task_id]);

    // If task is completed, update completion date in task_assignments
    if ($new_status === 'completed') {
        $stmt = $pdo->prepare("
            UPDATE task_assignments 
            SET status = 'completed', completion_date = NOW() 
            WHERE task_id = ?
        ");
        $stmt->execute([$task_id]);
    }
    // If task is cancelled, update status in task_assignments and make task available again
    elseif ($new_status === 'cancelled') {
        // Begin transaction to ensure all updates are completed together
        $pdo->beginTransaction();
        
        try {
            // Update task_assignments status to cancelled
            $stmt = $pdo->prepare("
                UPDATE task_assignments 
                SET status = 'cancelled'
                WHERE task_id = ?
            ");
            $stmt->execute([$task_id]);
            
            // Reset the task status to 'posted' and clear executor_id to make it available again
            $stmt = $pdo->prepare("
                UPDATE tasks 
                SET status = 'posted', executor_id = NULL
                WHERE task_id = ?
            ");
            $stmt->execute([$task_id]);
            
            // Commit the transaction
            $pdo->commit();
        } catch (PDOException $e) {
            // Roll back the transaction if something failed
            $pdo->rollBack();
            throw $e;
        }
    }
    // If task is in progress, update status in task_assignments
    elseif ($new_status === 'in_progress') {
        $stmt = $pdo->prepare("
            UPDATE task_assignments 
            SET status = 'in_progress'
            WHERE task_id = ?
        ");
        $stmt->execute([$task_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
