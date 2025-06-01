<?php
session_start();
require_once 'db.php';

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Bids - Task Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/related_tasks.css">
    <link rel="stylesheet" href="style/related_tasks_bids.css">
    <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/footer.css">
    <style>
        .pending-bids-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .bid-count-badge {
            background-color: #ff9800;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        
        .bid-price-range {
            font-weight: 500;
            color: #4a6cf7;
            margin-top: 0.5rem;
        }
        
        .latest-bid {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .bid-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
    </style>
</head>

<body>
    <?php include 'components/header.php'; ?>

    <div class="container">
        <div class="dashboard-container">
            <div class="pending-bids-header">
                <div>
                    <h1><i class="fas fa-gavel"></i> Pending Bids</h1>
                    <p>Review and accept bids for your tasks</p>
                </div>
                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php elseif (empty($tasks_with_bids)): ?>
                <div class="empty-state">
                    <i class="fas fa-gavel"></i>
                    <h2>No Pending Bids</h2>
                    <p>You don't have any tasks with pending bids at the moment.</p>
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
                                        <i class="fas fa-clock"></i> Latest bid: <?php echo date('M j, Y, g:i a', strtotime($task['latest_bid_date'])); ?>
                                    </div>
                                </div>

                                <div class="card-details">
                                    <span>
                                        <i class="fas fa-calendar"></i>
                                        Due: <?php echo date('M j, Y', strtotime($task['deadline'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-money-bill-wave"></i>
                                        Budget: $<?php echo number_format($task['budget'], 2); ?>
                                    </span>
                                </div>

                                <div class="card-actions">
                                    <a href="task_executors.php?id=<?php echo $task['task_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-eye"></i>
                                        Review Bids
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
</body>

</html>
