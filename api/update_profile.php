<?php
session_start();
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the request data
file_put_contents('update_profile_debug.log', "[" . date('Y-m-d H:i:s') . "] Starting profile update\n", FILE_APPEND);
file_put_contents('update_profile_debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents('update_profile_debug.log', "FILES data: " . print_r($_FILES, true) . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];
$response = ['success' => false, 'message' => ''];

try {
    // Validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($name) || empty($email)) {
        throw new Exception('Name and email are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT id_user FROM users WHERE email = ? AND id_user != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        throw new Exception('Email already in use by another account');
    }

    // Handle file upload
    $profilePicture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Only JPG, PNG, and GIF files are allowed');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('File size exceeds 5MB limit');
        }

        $uploadDir = 'uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid('profile_') . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $profilePicture = $filePath;

            // Delete old profile picture if it exists
            $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id_user = ?");
            $stmt->execute([$userId]);
            $oldPicture = $stmt->fetchColumn();
            
            if ($oldPicture && file_exists($oldPicture) && strpos($oldPicture, 'default-avatar') === false) {
                unlink($oldPicture);
            }
        }
    }

    // Update user data with only existing columns
    $params = [
        'name' => $name,
        'email' => $email,
        'id' => $userId
    ];

    $sql = "UPDATE users SET 
            name = :name, 
            email = :email";

    if ($profilePicture) {
        $sql .= ", profile_picture = :profile_picture";
        $params['profile_picture'] = $profilePicture;
    }

    $sql .= " WHERE id_user = :id";

    // Log the SQL query and parameters
    file_put_contents('update_profile_debug.log', "SQL Query: $sql\n", FILE_APPEND);
    file_put_contents('update_profile_debug.log', "Parameters: " . print_r($params, true) . "\n", FILE_APPEND);

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    // Log the result of the update
    file_put_contents('update_profile_debug.log', "Update result: " . ($result ? 'success' : 'failed') . "\n", FILE_APPEND);
    if (!$result) {
        $errorInfo = $stmt->errorInfo();
        file_put_contents('update_profile_debug.log', "Database error: " . print_r($errorInfo, true) . "\n", FILE_APPEND);
    }

    if ($result) {
        $response = [
            'success' => true,
            'message' => 'Profile updated successfully',
            'redirect' => 'profile.php'
        ];
    } else {
        throw new Exception('Failed to update profile');
    }

} catch (Exception $e) {
    http_response_code(400);
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
