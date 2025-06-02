<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

// Get task ID from URL parameter
$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

// Validate task ID
if ($task_id <= 0) {
    $_SESSION['error_message'] = "Invalid task ID.";
    header("Location: dashboard.php");
    exit;
}

// Get task details
$stmt = $pdo->prepare("
    SELECT t.*, u.id_user as executor_id, u.name as executor_name, u.email as executor_email,
           b.bid_amount, b.status as bid_status
    FROM tasks t
    LEFT JOIN users u ON t.executor_id = u.id_user
    LEFT JOIN bids b ON t.task_id = b.task_id AND b.executor_id = t.executor_id
    WHERE t.task_id = ? AND t.client_id = ? AND t.status = 'completed'
");
$stmt->execute([$task_id, $_SESSION['user_id']]);
$task = $stmt->fetch();

if (!$task) {
    $_SESSION['error_message'] = "Task not found or not eligible for payment.";
    header("Location: dashboard.php");
    exit;
}

// Check if payment has already been made
$stmt = $pdo->prepare("SELECT payment_id FROM payments WHERE task_id = ? AND status = 'completed'");
$stmt->execute([$task_id]);
if ($stmt->rowCount() > 0) {
    $_SESSION['error_message'] = "Payment has already been processed for this task.";
    header("Location: dashboard.php");
    exit;
}

// Set payment amount (use bid amount if available, otherwise use budget)
$payment_amount = $task['bid_amount'] ? $task['bid_amount'] : $task['budget'];

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
        $stmt->execute([$task_id, $_SESSION['user_id'], $task['executor_id'], $payment_amount]);
        
        // Update the accepted bid to 'done' status
        $stmt = $pdo->prepare("
            UPDATE bids
            SET status = 'done'
            WHERE task_id = ?
            AND executor_id = ?
            AND status = 'accepted'
        ");
        $stmt->execute([$task_id, $task['executor_id']]);
        
        // Verify the update was successful
        $stmt = $pdo->prepare("SELECT status FROM bids WHERE task_id = ? AND executor_id = ?");
        $stmt->execute([$task_id, $task['executor_id']]);
        $finalStatus = $stmt->fetchColumn();
        
        if ($finalStatus !== 'done') {
            // Log error if bid status update failed
            error_log("Failed to update bid status to 'done' for task_id: {$task_id}. Current status: $finalStatus");
        }
        
        // Now that the task is completed and paid, reject all other bids
        $stmt = $pdo->prepare("
            UPDATE bids
            SET status = 'rejected'
            WHERE task_id = ?
            AND executor_id != ?
            AND status = 'pending'
        ");
        $stmt->execute([$task_id, $task['executor_id']]);

        // Commit transaction
        $pdo->commit();

        // Set success message
        $_SESSION['success_message'] = "Payment successful! The executor has been paid for completing the task.";
        
        // Redirect back to dashboard
        header("Location: dashboard.php");
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
    // Display payment form
    $pageTitle = "Process Payment";
    include 'components/head.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style/payment.css">
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="container">
        <div class="payment-container">
            <div class="payment-header">
                <h1><i class="fas fa-credit-card"></i> Process Payment</h1>
                <p>Complete your payment to finalize the task</p>
            </div>

            <div class="task-details">
                <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                <div class="detail-row">
                    <i class="fas fa-user"></i>
                    <span>Executor: <?php echo htmlspecialchars($task['executor_name']); ?></span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-envelope"></i>
                    <span>Email: <?php echo htmlspecialchars($task['executor_email']); ?></span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-calendar"></i>
                    <span>Deadline: <?php echo date('M d, Y', strtotime($task['deadline'])); ?></span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-check-circle"></i>
                    <span>Status: <?php echo ucfirst($task['status']); ?></span>
                </div>
                
                <div class="payment-amount">
                    Payment Amount: $<?php echo number_format($payment_amount, 2); ?>
                </div>
            </div>

            <form class="payment-form" method="POST" action="process_payment.php?task_id=<?php echo $task_id; ?>">
                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                
                <div class="form-group">
                    <label for="card_holder">Card Holder Name</label>
                    <input type="text" id="card_holder" name="card_holder" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" class="form-control" placeholder="XXXX XXXX XXXX XXXX" required>
                </div>
                
                <div class="card-row">
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" class="form-control" placeholder="XXX" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-pay">
                    <i class="fas fa-lock"></i> Pay $<?php echo number_format($payment_amount, 2); ?>
                </button>
                
                <a href="dashboard.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </form>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html>
<?php
}
