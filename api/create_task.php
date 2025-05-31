<?php
session_start();
require_once '../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session
error_log('Session data: ' . print_r($_SESSION, true));

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    error_log('Auth failed - user_id: ' . isset($_SESSION['user_id']) . ', role: ' . ($_SESSION['role'] ?? 'not set'));
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Debug information
error_log('Received POST data: ' . print_r($_POST, true));
error_log('Received FILES data: ' . print_r($_FILES, true));

// Validate required fields
$required_fields = ['title', 'category_id', 'description', 'budget', 'deadline'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
    ]);
    exit;
}

try {
    // Debug information
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));

    // Handle file upload if present
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }

        if ($_FILES['image']['size'] > $max_size) {
            echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 5MB.']);
            exit;
        }

        $upload_dir = '../uploads/tasks/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('task_') . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = 'uploads/tasks/' . $file_name;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error uploading file']);
            exit;
        }
    }

    // Prepare data for insertion
    $data = [
        'title' => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'category_id' => (int)$_POST['category_id'],
        'budget' => (float)$_POST['budget'],
        'deadline' => $_POST['deadline'],
        'client_id' => (int)$_SESSION['user_id'],
        'location' => isset($_POST['location']) ? trim($_POST['location']) : null,
        'image_path' => $image_path
    ];

    error_log('Attempting to insert with data: ' . print_r($data, true));

    // Insert task into database
    $stmt = $pdo->prepare("
        INSERT INTO tasks (
            title, description, category_id, budget, deadline, 
            client_id, status, created_at, image_url, location
        ) VALUES (
            :title, :description, :category_id, :budget, :deadline,
            :client_id, 'Posted', NOW(), :image_path, :location
        )
    ");

    // Bind parameters explicitly
    $stmt->bindParam(':title', $data['title']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':category_id', $data['category_id']);
    $stmt->bindParam(':budget', $data['budget']);
    $stmt->bindParam(':deadline', $data['deadline']);
    $stmt->bindParam(':client_id', $data['client_id']);
    $stmt->bindParam(':image_path', $data['image_path']);
    $stmt->bindParam(':location', $data['location']);
    
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Task created successfully']);

} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    error_log('SQL State: ' . $e->errorInfo[0]);
    error_log('Error Code: ' . $e->errorInfo[1]);
    error_log('Error Message: ' . $e->errorInfo[2]);
    $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    header('Location: ../my_tasks.php');
    exit;
} catch (Exception $e) {
    error_log('General Error: ' . $e->getMessage());
    error_log('Stack Trace: ' . $e->getTraceAsString());
    $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
    header('Location: ../my_tasks.php');
    exit;
}
?>
