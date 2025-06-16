<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header('Content-Type: application/json');

// Start session
session_start();

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'error_type' => ''
];

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $response['error_type'] = 'session';
        throw new Exception('You must be logged in to change your password.');
    }

    // Get user ID from session
    $userId = $_SESSION['user_id'];
    
    // Log received POST data for debugging
    error_log('Received password change request: ' . print_r($_POST, true));
    
    // Get input data with trim to remove any whitespace
    $currentPassword = trim($_POST['current_password'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    // Validate input
    if (empty($currentPassword)) {
        $response['error_type'] = 'validation';
        $response['field'] = 'current_password';
        throw new Exception('Current password is required.');
    }
    
    if (empty($newPassword)) {
        $response['error_type'] = 'validation';
        $response['field'] = 'new_password';
        throw new Exception('New password is required.');
    }
    
    if (empty($confirmPassword)) {
        $response['error_type'] = 'validation';
        $response['field'] = 'confirm_password';
        throw new Exception('Please confirm your new password.');
    }

    // Check if new passwords match
    if ($newPassword !== $confirmPassword) {
        $response['error_type'] = 'validation';
        $response['field'] = 'confirm_password';
        throw new Exception('New passwords do not match.');
    }

    // Validate password length
    if (strlen($newPassword) < 8) {
        $response['error_type'] = 'validation';
        $response['field'] = 'new_password';
        throw new Exception('Password must be at least 8 characters long.');
    }

    // Include database connection
    require_once __DIR__ . '/../config/db.php';

    // Get current password hash from database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('User not found.');
    }

    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        throw new Exception('Current password is incorrect.');
    }

    // Check if new password is different from current
    if (password_verify($newPassword, $user['password'])) {
        throw new Exception('New password must be different from current password.');
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id_user = ?");
    $result = $stmt->execute([$hashedPassword, $userId]);

    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Password changed successfully!';
        
        // Log the password change (optional)
        error_log("User ID $userId changed their password successfully.");
    } else {
        throw new Exception('Failed to update password. Please try again.');
    }

} catch (Exception $e) {
    http_response_code(400);
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
