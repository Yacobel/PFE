<?php
session_start();
require_once 'config/languages.php';
require_once 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: login.php');
    exit();
}
$task_id = isset($_GET['task_id']) ? $_GET['task_id'] : null;
if ($task_id) {
    $stmt = $pdo->prepare("
        SELECT t1.category_id, t1.task_id,
        (SELECT COUNT(*) FROM tasks t2 
         WHERE t2.category_id = t1.category_id 
         AND t2.task_id != t1.task_id 
         AND (t2.status = 'in_progress' OR t2.executor_id IS NOT NULL)
         AND (t2.payment_status IS NULL OR t2.payment_status != 'paid')) as related_count
        FROM tasks t1
        WHERE t1.task_id = ? AND t1.client_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();
    if ($task && $task['related_count'] > 0) {
        $stmt = $pdo->prepare("
            SELECT t.*, c.name as category_name, 
                   u.name as executor_name, u.profile_picture as executor_image,
                   u.email as executor_email,
                   t.status as task_status, t.created_at, t.deadline, t.payment_status,
                   (SELECT COUNT(*) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending') as pending_bids_count,
                   (SELECT COUNT(*) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'cancelled' AND b.executor_id = t.executor_id) as has_cancelled_bid,
                   (SELECT COUNT(*) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'accepted') as has_assignment
            FROM tasks t
            LEFT JOIN categories c ON t.category_id = c.category_id
            LEFT JOIN users u ON t.executor_id = u.id_user
            WHERE t.category_id = ? 
            AND t.task_id != ? 
            AND t.client_id = ? 
            AND (t.payment_status IS NULL OR t.payment_status != 'paid')
            AND (
                t.status != 'posted' OR 
                EXISTS (SELECT 1 FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending') OR
                EXISTS (SELECT 1 FROM bids b WHERE b.task_id = t.task_id AND b.status = 'accepted')
            )
            HAVING has_cancelled_bid = 0 OR has_cancelled_bid IS NULL
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$task['category_id'], $task_id, $_SESSION['user_id']]);
    } else {
        header('Location: dashboard.php');
        exit();
    }
} else {
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as category_name, 
               u.name as executor_name, u.profile_picture as executor_image,
               u.email as executor_email,
               t.status as task_status, t.created_at, t.deadline, t.payment_status,
               (SELECT COUNT(*) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending') as pending_bids_count,
               (SELECT COUNT(*) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'cancelled' AND b.executor_id = t.executor_id) as has_cancelled_bid,
               (SELECT COUNT(*) FROM bids b WHERE b.task_id = t.task_id AND b.status = 'accepted') as has_assignment
        FROM tasks t
        LEFT JOIN categories c ON t.category_id = c.category_id
        LEFT JOIN users u ON t.executor_id = u.id_user
        WHERE t.client_id = ? 
        AND (t.payment_status IS NULL OR t.payment_status != 'paid')
        AND (
            t.status != 'posted' OR 
            EXISTS (SELECT 1 FROM bids b WHERE b.task_id = t.task_id AND b.status = 'pending') OR
            EXISTS (SELECT 1 FROM bids b WHERE b.task_id = t.task_id AND b.status = 'accepted')
        )
        HAVING has_cancelled_bid = 0 OR has_cancelled_bid IS NULL
        ORDER BY c.name, t.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
}
$related_tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __("related_tasks"); ?> - <?php echo __("task_platform"); ?></title>
    <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/related_tasks.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="language-selector">
        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
        <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>
    <div class="container">
        <?php include 'components/header.php'; ?>
        <div class="dashboard-container">
            <div class="assignments-header">
                <h1>
                    <i class="fas fa-link"></i>
                    <?php echo $task_id ? __("related_tasks") : __("all_tasks_by_category"); ?>
                </h1>
                <p><?php echo __("view_tasks_progress"); ?></p>
            </div>
            <?php if (empty($related_tasks)): ?>
                <div class="empty-state">
                    <i class="fas fa-tasks"></i>
                    <h2><?php echo __("no_related_tasks"); ?></h2>
                    <p><?php echo __("no_related_tasks_message"); ?></p>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        <?php echo __("back_to_dashboard"); ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="task-grid">
                    <?php foreach ($related_tasks as $task): ?>
                        <div class="task-card">
                            <div class="card-content">
                                <div class="card-info">
                                    <h3 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h3>
                                    <div class="card-meta">
                                        <span class="card-category">
                                            <i class="fas fa-tag"></i>
                                            <?php echo htmlspecialchars($task['category_name']); ?>
                                        </span>
                                        <?php if ($task['task_status'] === 'posted' && isset($task['pending_bids_count']) && $task['pending_bids_count'] > 0): ?>
                                            <span class="card-status has-bids">
                                                <i class="fas fa-gavel"></i>
                                                <?php echo $task['pending_bids_count']; ?> <?php echo __("bids_pending"); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="card-status status-<?php echo strtolower($task['task_status']); ?>">
                                                <i class="fas fa-circle"></i>
                                                <?php echo __($task['task_status']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-details">
                                    <?php if ($task['executor_name']): ?>
                                        <span>
                                            <i class="fas fa-user-tie"></i>
                                            <?php echo htmlspecialchars($task['executor_name']); ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-envelope"></i>
                                            <?php echo htmlspecialchars($task['executor_email']); ?>
                                        </span>
                                    <?php elseif (isset($task['pending_bids_count']) && $task['pending_bids_count'] > 0): ?>
                                        <span class="pending-bids">
                                            <i class="fas fa-gavel"></i>
                                            <?php echo $task['pending_bids_count']; ?> <?php echo __("pending_bids"); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span>
                                        <i class="fas fa-calendar"></i>
                                        <?php echo __("due"); ?>: <?php echo date('M j, Y', strtotime($task['deadline'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-clock"></i>
                                        <?php echo __("created"); ?>: <?php echo date('M j, Y', strtotime($task['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="card-actions">
                                    <?php if ($task['task_status'] === 'completed'): ?>
                                        <a href="process_payment.php?task_id=<?php echo $task['task_id']; ?>" class="btn btn-success">
                                            <i class="fas fa-credit-card"></i>
                                            <?php echo __("pay_now"); ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="task_details.php?id=<?php echo $task['task_id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-eye"></i>
                                            <?php echo __("view_details"); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($task['executor_id']): ?>
                                        <a href="messages.php?user=<?php echo $task['executor_id']; ?>" class="btn btn-secondary">
                                            <i class="fas fa-message"></i>
                                            <?php echo __("message"); ?>
                                        </a>
                                    <?php elseif (isset($task['pending_bids_count']) && $task['pending_bids_count'] > 0): ?>
                                        <a href="task_details.php?id=<?php echo $task['task_id']; ?>" class="btn btn-secondary bid-btn">
                                            <i class="fas fa-gavel"></i>
                                            <?php echo __("review_bids"); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {});
    </script>
</body>

</html>