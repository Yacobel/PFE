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
    'message' => ''
];

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('You must be logged in to change your password.');
    }

    // Get user ID from session
    $userId = $_SESSION['user_id'];
    
    // Get input data
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        throw new Exception('All fields are required.');
    }

    // Check if new passwords match
    if ($newPassword !== $confirmPassword) {
        throw new Exception('New passwords do not match.');
    }

    // Validate password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newPassword)) {
        throw new Exception('Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.');
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
