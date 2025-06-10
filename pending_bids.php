<?php
session_start();
require_once 'config/languages.php';
require_once 'config/db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: login.php');
    exit();
}

try {
    // Get tasks with pending bids
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as category_name, 
               (SELECT COUNT(*) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending') as pending_bids_count,
               (SELECT MIN(bid_amount) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending') as min_bid_amount,
               (SELECT MAX(bid_amount) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending') as max_bid_amount,
               (SELECT MAX(bid_date) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending') as latest_bid_date
        FROM tasks t
        LEFT JOIN categories c ON t.category_id = c.category_id
        WHERE t.client_id = ?
        AND t.status = 'posted'
        AND EXISTS (SELECT 1 FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending')
        ORDER BY latest_bid_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $tasks_with_bids = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __("pending_bids"); ?> - <?php echo __("task_platform"); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/pending_bids.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
</head>

<body>
    <!-- Language Switcher -->
    <div class="language-selector">
        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
        <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>

    <div class="container">
        <?php include 'components/header.php'; ?>
        <div class="dashboard-container">
            <div class="pending-bids-header">
                <div>
                    <h1><i class="fas fa-gavel"></i> <?php echo __("pending_bids"); ?></h1>
                    <p><?php echo __("review_bids"); ?></p>
                </div>
                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> <?php echo __("back_to_dashboard"); ?>
                </a>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php elseif (empty($tasks_with_bids)): ?>
                <div class="empty-state">
                    <i class="fas fa-gavel"></i>
                    <h2><?php echo __("no_pending_bids"); ?></h2>
                    <p><?php echo __("no_pending_bids_message"); ?></p>
                </div>
            <?php else: ?>
                <div class="task-grid">
                    <?php foreach ($tasks_with_bids as $task): ?>
                        <div class="task-card">
                            <div class="card-content">
                                <div class="bid-card-header">
                                    <h3 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h3>
                                    <span class="card-status has-bids">
                                        <i class="fas fa-gavel"></i>
                                        <span class="bid-count-badge"><?php echo $task['pending_bids_count']; ?></span>
                                    </span>
                                </div>
                                
                                <div class="card-meta">
                                    <span class="card-category">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($task['category_name']); ?>
                                    </span>
                                    <div class="bid-price-range">
                                        <?php if ($task['min_bid_amount'] == $task['max_bid_amount']): ?>
                                            <i class="fas fa-dollar-sign"></i> $<?php echo number_format($task['min_bid_amount'], 2); ?>
                                        <?php else: ?>
                                            <i class="fas fa-dollar-sign"></i> $<?php echo number_format($task['min_bid_amount'], 2); ?> - $<?php echo number_format($task['max_bid_amount'], 2); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="latest-bid">
                                        <i class="fas fa-clock"></i> <?php echo __("latest_bid"); ?>: <?php echo date('M j, Y, g:i a', strtotime($task['latest_bid_date'])); ?>
                                    </div>
                                </div>

                                <div class="card-details">
                                    <span>
                                        <i class="fas fa-calendar"></i>
                                        <?php echo __("due"); ?>: <?php echo date('M j, Y', strtotime($task['deadline'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-money-bill-wave"></i>
                                        <?php echo __("budget"); ?>: $<?php echo number_format($task['budget'], 2); ?>
                                    </span>
                                </div>

                                <div class="card-actions">
                                    <a href="task_executors.php?id=<?php echo $task['task_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-eye"></i>
                                        <?php echo __("review_bids"); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
