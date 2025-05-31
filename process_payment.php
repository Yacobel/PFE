<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
    $card_number = isset($_POST['card_number']) ? $_POST['card_number'] : '';
    $expiry_date = isset($_POST['expiry_date']) ? $_POST['expiry_date'] : '';
    $cvv = isset($_POST['cvv']) ? $_POST['cvv'] : '';
    $card_holder = isset($_POST['card_holder']) ? $_POST['card_holder'] : '';

    // Validate task ID
    if ($task_id <= 0) {
        $_SESSION['error_message'] = "Invalid task ID.";
        header("Location: my_tasks.php");
        exit;
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Verify the task belongs to the client and is completed
        $stmt = $pdo->prepare("
            SELECT t.*, u.id_user as executor_id, u.name as executor_name, u.email as executor_email
            FROM tasks t
            LEFT JOIN users u ON t.executor_id = u.id_user
            WHERE t.task_id = ? AND t.client_id = ? AND t.status = 'completed'
        ");
        $stmt->execute([$task_id, $_SESSION['user_id']]);
        $task = $stmt->fetch();

        if (!$task) {
            throw new Exception("Task not found or not eligible for payment.");
        }

        // In a real application, you would integrate with a payment gateway here
        // For this demo, we'll simulate a successful payment

        // Update task payment status
        $stmt = $pdo->prepare("
            UPDATE tasks
            SET payment_status = 'paid', payment_date = NOW()
            WHERE task_id = ?
        ");
        $stmt->execute([$task_id]);

        // Record the payment in the payments table
        $stmt = $pdo->prepare("
            INSERT INTO payments (task_id, client_id, executor_id, amount, payment_date, status)
            VALUES (?, ?, ?, ?, NOW(), 'completed')
        ");
        $stmt->execute([$task_id, $_SESSION['user_id'], $task['executor_id'], $task['budget']]);

        // Commit transaction
        $pdo->commit();

        // Set success message
        $_SESSION['success_message'] = "Payment successful! The executor has been paid for completing the task.";
        
        // Redirect back to my tasks page
        header("Location: my_tasks.php");
        exit;
    } catch (Exception $e) {
        // Roll back transaction on error
        $pdo->rollBack();
        
        // Set error message
        $_SESSION['error_message'] = "Payment failed: " . $e->getMessage();
        
        // Redirect back to my tasks page
        header("Location: my_tasks.php");
        exit;
    }
} else {
    // If not POST request, redirect to my tasks page
    header("Location: my_tasks.php");
    exit;
}
