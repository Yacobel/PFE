<?php
session_start();
require_once '../db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if all required fields are present
if (
    !isset($_POST['task_id']) || !isset($_POST['title']) || !isset($_POST['description']) ||
    !isset($_POST['category_id']) || !isset($_POST['budget']) || !isset($_POST['deadline'])
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // First, verify that the task belongs to the current user
    $stmt = $pdo->prepare("SELECT client_id, image_url FROM tasks WHERE task_id = ?");
    $stmt->execute([$_POST['task_id']]);
    $task = $stmt->fetch();

    if (!$task || $task['client_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this task']);
        exit;
    }

    // Handle image upload if present
    $image_url = $task['image_url']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "../uploads/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Check if image file is a actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            throw new Exception("File is not an image.");
        }

        // Check file size (5MB max)
        if ($_FILES["image"]["size"] > 5000000) {
            throw new Exception("File is too large. Maximum size is 5MB.");
        }

        // Allow certain file formats
        if (!in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if exists and different from default
            if ($task['image_url'] && $task['image_url'] != 'uploads/default-task.jpg' && file_exists('../' . $task['image_url'])) {
                unlink('../' . $task['image_url']);
            }
            $image_url = "uploads/" . $new_filename;
        } else {
            throw new Exception("Failed to upload image.");
        }
    }

    // Update task in database
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET title = ?, description = ?, category_id = ?, budget = ?, 
            deadline = ?, location = ?, image_url = ?
        WHERE task_id = ? AND client_id = ?
    ");

    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['category_id'],
        $_POST['budget'],
        $_POST['deadline'],
        $_POST['location'] ?? null,
        $image_url,
        $_POST['task_id'],
        $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
