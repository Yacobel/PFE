<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Get the requested role from URL parameter
$new_role = $_GET['role'] ?? '';

// Validate the role
if (!in_array($new_role, ['client', 'executor'])) {
    $_SESSION['error_message'] = "Invalid role specified.";
    header("Location: ../profile.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Update the user's role
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id_user = ?");
    $stmt->execute([$new_role, $user_id]);

    $_SESSION['success_message'] = "Role successfully changed to " . ucfirst($new_role);
    header("Location: ../profile.php");
} catch (PDOException $e) {
    error_log("Role change error: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred while changing your role.";
    header("Location: ../profile.php");
}
exit;
