<?php
// Enable error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../php_errors.log');

// Set headers
header('Content-Type: application/json');

// Simple debug logging
function log_debug($message) {
    $log_file = __DIR__ . '/../../debug.log';
    $timestamp = date('[Y-m-d H:i:s] ');
    $log_message = $timestamp . $message . "\n";
    
    // Log to file
    file_put_contents($log_file, $log_message, FILE_APPEND);
    
    // Also log to PHP's error log
    error_log($message);
}

// Start session after setting error handling
session_start();

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Include database connection
    require_once __DIR__ . '/../config/db.php';
    
    // Log request data
    log_debug('=== New Request ===');
    log_debug('Request Method: ' . $_SERVER['REQUEST_METHOD']);
    log_debug('POST Data: ' . print_r($_POST, true));
    log_debug('FILES Data: ' . print_r($_FILES, true));

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed', 405);
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not authenticated', 401);
    }

    $userId = $_SESSION['user_id'];
    
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

    // Handle profile picture upload if present
    $relative_path = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        
        log_debug("File upload detected. File info: " . print_r($file, true));
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate file type and extension
        if (empty($ext) || !in_array($ext, $allowed)) {
            throw new Exception("Invalid file type: " . $ext . ". Allowed types: " . implode(', ', $allowed));
        }
        
        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            throw new Exception('File size exceeds 5MB limit');
        }
        
        // Set up upload directory - using absolute path for better reliability
        $base_dir = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] . '/PFE');
        $upload_dir = $base_dir . '/uploads/profiles/';
        
        // Debug directory info
        log_debug("Document root: " . $_SERVER['DOCUMENT_ROOT']);
        log_debug("Base directory: " . $base_dir);
        log_debug("Upload directory: " . $upload_dir);
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            log_debug("Creating upload directory: " . $upload_dir);
            if (!mkdir($upload_dir, 0777, true)) {
                $error = error_get_last();
                throw new Exception('Failed to create upload directory: ' . ($error['message'] ?? 'Unknown error'));
            }
            chmod($upload_dir, 0777);
            log_debug("Created upload directory with permissions 0777");
        }
        
        // Verify directory is writable
        if (!is_writable($upload_dir)) {
            $perms = substr(sprintf('%o', fileperms($upload_dir)), -4);
            throw new Exception('Upload directory is not writable. Permissions: ' . $perms . ' Path: ' . $upload_dir);
        }
        
        // Generate a unique filename
        $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
        $relative_path = '/PFE/uploads/profiles/' . $filename;  // Web-accessible path
        $absolute_path = $upload_dir . $filename;  // Full server path
        
        // Clean up paths
        $absolute_path = str_replace('//', '/', $absolute_path);
        
        // Debug information
        log_debug("File upload details:");
        log_debug("- Temporary file: " . $file['tmp_name'] . ' (exists: ' . (file_exists($file['tmp_name']) ? 'yes' : 'no') . ')');
        log_debug("- Target file: " . $absolute_path);
        log_debug("- Web path: " . $relative_path);
        log_debug("- Directory exists: " . (is_dir($upload_dir) ? 'Yes' : 'No'));
        log_debug("- Directory writable: " . (is_writable($upload_dir) ? 'Yes' : 'No'));
        log_debug("- Directory permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4));
        
        log_debug("Attempting to move uploaded file to: " . $absolute_path);
        
        // Verify the file is an actual image
        $check = @getimagesize($file['tmp_name']);
        if ($check === false) {
            throw new Exception('File is not a valid image');
        }
        
        // Move the uploaded file with error handling
        if (!@move_uploaded_file($file['tmp_name'], $absolute_path)) {
            $error = error_get_last();
            $error_msg = 'Failed to save uploaded file: ' . ($error['message'] ?? 'Unknown error');
            error_log('File upload error: ' . $error_msg);
            error_log('Temporary file: ' . $file['tmp_name']);
            error_log('Target path: ' . $absolute_path);
            error_log('File exists: ' . (file_exists($file['tmp_name']) ? 'Yes' : 'No'));
            error_log('Is uploaded: ' . (is_uploaded_file($file['tmp_name']) ? 'Yes' : 'No'));
            throw new Exception($error_msg);
        }
        
        // Verify the file was actually saved
        if (!file_exists($absolute_path)) {
            throw new Exception('File was not saved correctly. Please try again.');
        }
        
        // Set file permissions
        chmod($absolute_path, 0644);
        
        log_debug("File moved successfully to: " . $absolute_path);
        
        // Delete old profile picture if it exists and is not the default avatar
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id_user = ?");
        $stmt->execute([$userId]);
        $old_picture = $stmt->fetchColumn();
        
        if ($old_picture && file_exists($_SERVER['DOCUMENT_ROOT'] . $old_picture) && 
            strpos($old_picture, 'default-avatar') === false) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $old_picture);
            log_debug("Deleted old profile picture: " . $old_picture);
        }
    }

    // Update user data in the database
    // Get bio from POST data
    $bio = trim($_POST['bio'] ?? '');
    
    $sql = "UPDATE users SET name = ?, email = ?, bio = ?";
    $params = [$name, $email, $bio];
    
    // Add profile picture to update if available
    if ($relative_path !== null) {
        $sql .= ", profile_picture = ?";
        $params[] = $relative_path;
    }
    
    $sql .= " WHERE id_user = ?";
    $params[] = $userId;
    
    // Execute the update query
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    log_debug("Update result: " . ($result ? 'success' : 'failed'));
    
    if (!$result) {
        $error = $stmt->errorInfo();
        log_debug("Database error: " . print_r($error, true));
        throw new Exception('Failed to update profile');
    }
    
    // Get updated user data
    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id_user = ?");
    $stmt->execute([$userId]);
    $updatedUser = $stmt->fetch();
    
    // Ensure the profile picture path is correct
    $profilePicture = '';
    if (!empty($updatedUser['profile_picture'])) {
        $profilePicture = $updatedUser['profile_picture'];
        // Make sure the path is web-accessible
        if (strpos($profilePicture, 'http') !== 0 && $profilePicture[0] !== '/') {
            $profilePicture = '/' . ltrim($profilePicture, '/');
        }
    }
    
    $response = [
        'success' => true,
        'message' => 'Profile updated successfully',
        'profile_picture' => $profilePicture,
        'redirect' => 'profile.php'
    ];
} catch (PDOException $e) {
    // Handle database errors specifically
    $code = is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
        ? (int)$e->getCode() 
        : 500; // Default to 500 if code is not a valid HTTP status
    
    http_response_code($code);
    
    $response = [
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage(),
        'error_code' => $e->getCode(),
        'error_type' => 'database'
    ];
    
    log_debug("Database Error: " . $e->getMessage() . 
              " in " . $e->getFile() . " on line " . $e->getLine() . 
              "\nSQL Error Info: " . print_r(isset($stmt) ? $stmt->errorInfo() : 'No statement', true));
              
} catch (Exception $e) {
    // Handle all other exceptions
    $code = is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
        ? (int)$e->getCode() 
        : 500; // Default to 500 if code is not a valid HTTP status
    
    http_response_code($code);
    
    $response = [
        'success' => false, 
        'message' => $e->getMessage(),
        'error_type' => 'general'
    ];
    
    log_debug("Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}

// Ensure no previous output
if (ob_get_level()) {
    ob_end_clean();
}

// Set JSON content type header
header('Content-Type: application/json; charset=utf-8');

// Encode the response
$jsonResponse = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// Check for JSON encoding errors
if ($jsonResponse === false) {
    $response = [
        'success' => false,
        'message' => 'Failed to encode response: ' . json_last_error_msg()
    ];
    $jsonResponse = json_encode($response);
}

// Debug log the response
log_debug('Sending JSON response: ' . $jsonResponse);

// Output the JSON response and exit
echo $jsonResponse;
exit(0);
