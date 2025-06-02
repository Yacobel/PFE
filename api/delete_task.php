<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if task ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Task ID is required']);
    exit;
}

try {
    // First, verify that the task belongs to the current user and get the image URL
    $stmt = $pdo->prepare("SELECT client_id, image_url FROM tasks WHERE task_id = ?");
    $stmt->execute([$_GET['id']]);
    $task = $stmt->fetch();

    if (!$task || $task['client_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this task']);
        exit;
    }

    // Delete the task's image if it exists and is not the default image
    if ($task['image_url'] && $task['image_url'] != 'uploads/default-task.jpg' && file_exists('../' . $task['image_url'])) {
        unlink('../' . $task['image_url']);
    }

    // Delete the task from the database
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = ? AND client_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);

    echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
