<?php
session_start();
require_once 'config/languages.php';
require_once 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: login.php');
    exit();
}
if (!isset($_GET['id'])) {
    header("Location: pending_bids.php?error=" . urlencode(__("no_task_id")));
    exit;
}
$task_id = $_GET['id'];
if (!is_numeric($task_id)) {
    header("Location: pending_bids.php?error=" . urlencode(__("invalid_task_id")));
    exit;
}
try {
    $stmt = $pdo->prepare("
        SELECT t.task_id, t.title, t.budget, t.status, c.name as category_name
        FROM tasks t 
        LEFT JOIN categories c ON t.category_id = c.category_id
        WHERE t.task_id = ? AND t.client_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();
    if (!$task) {
        header("Location: pending_bids.php?error=" . urlencode(__("task_not_found")));
        exit;
    }
    $stmt = $pdo->prepare("
        SELECT b.*, 
               u.id_user, u.name, u.email, u.profile_picture, u.registration_date,
               (SELECT COUNT(*) FROM tasks t WHERE t.executor_id = u.id_user AND t.status = 'completed') as completed_tasks,
               (SELECT AVG(rating) FROM reviews r 
                JOIN tasks t ON r.task_id = t.task_id 
                WHERE t.executor_id = u.id_user) as avg_rating
        FROM bids b
        JOIN users u ON b.executor_id = u.id_user
        WHERE b.task_id = ?
        ORDER BY b.bid_date DESC
    ");
    $stmt->execute([$task_id]);
    $bids = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = __("database_error");
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __("review_executors"); ?> - <?php echo __("task_platform"); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/task_executors.css">
</head>

<body>
    <div class="language-selector">
        <a href="?id=<?php echo $task_id; ?>&lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
        <a href="?id=<?php echo $task_id; ?>&lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>
    <main class="container">
        <?php include 'components/header.php'; ?>
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>
            <div class="dashboard-container">
                <div class="task-header">
                    <div>
                        <h1><?php echo htmlspecialchars($task['title']); ?></h1>
                        <div class="task-info">
                            <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($task['category_name']); ?></span>
                            <span><i class="fas fa-dollar-sign"></i> <?php echo __("budget"); ?>: $<?php echo number_format($task['budget'], 2); ?></span>
                            <span><i class="fas fa-gavel"></i> <?php echo count($bids); ?> <?php echo __("bids"); ?></span>
                        </div>
                    </div>
                    <a href="pending_bids.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> <?php echo __("back_to_pending_bids"); ?>
                    </a>
                </div>
                <?php if (empty($bids)): ?>
                    <div class="empty-state">
                        <i class="fas fa-gavel"></i>
                        <h2><?php echo __("no_bids_yet"); ?></h2>
                        <p><?php echo __("no_bids_message"); ?></p>
                    </div>
                <?php else: ?>
                    <h2><i class="fas fa-user-tie"></i> <?php echo __("executors_who_bid"); ?></h2>
                    <p><?php echo __("review_executors_message"); ?></p>
                    <div class="executors-list">
                        <?php foreach ($bids as $bid): ?>
                            <div class="executor-card">
                                <div class="executor-header">
                                    <div class="executor-info">
                                        <div class="executor-avatar">
                                            <?php if ($bid['profile_picture']): ?>
                                                <img src="<?php echo htmlspecialchars($bid['profile_picture']); ?>" alt="<?php echo __("executor_profile"); ?>">
                                            <?php else: ?>
                                                <i class="fas fa-user-tie"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="executor-details">
                                            <h3><?php echo htmlspecialchars($bid['name']); ?></h3>
                                            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($bid['email']); ?></p>
                                            <p><i class="fas fa-calendar"></i> <?php echo __("member_since"); ?> <?php echo date('M Y', strtotime($bid['registration_date'])); ?></p>
                                            <div class="executor-stats">
                                                <div class="stat-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span><?php echo $bid['completed_tasks']; ?> <?php echo __("completed_tasks"); ?></span>
                                                </div>
                                                <?php if ($bid['avg_rating']): ?>
                                                    <div class="stat-item">
                                                        <div class="rating">
                                                            <i class="fas fa-star"></i>
                                                            <span><?php echo number_format($bid['avg_rating'], 1); ?></span>
                                                        </div>
                                                        <span><?php echo __("rating"); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bid-amount">
                                        $<?php echo number_format($bid['bid_amount'], 2); ?>
                                    </div>
                                </div>
                                <?php if (!empty($bid['proposal_text'])): ?>
                                    <div class="bid-proposal">
                                        <h4><i class="fas fa-quote-left"></i> <?php echo __("proposal"); ?></h4>
                                        <p><?php echo nl2br(htmlspecialchars($bid['proposal_text'])); ?></p>
                                    </div>
                                <?php endif; ?>
                                <div class="executor-actions">
                                    <?php if ($bid['status'] === 'pending'): ?>
                                        <button class="btn-primary" onclick="acceptBid(<?php echo $bid['bid_id']; ?>, <?php echo $task_id; ?>)">
                                            <i class="fas fa-check"></i> <?php echo __("accept_bid"); ?>
                                        </button>
                                    <?php elseif ($bid['status'] === 'accepted'): ?>
                                        <div class="bid-status accepted">
                                            <i class="fas fa-check-circle"></i> <?php echo __("accepted"); ?>
                                        </div>
                                    <?php elseif ($bid['status'] === 'rejected'): ?>
                                        <div class="bid-status rejected">
                                            <i class="fas fa-times-circle"></i> <?php echo __("rejected"); ?>
                                        </div>
                                    <?php endif; ?>
                                    <button class="btn-message" onclick="openMessageModal(<?php echo $bid['id_user']; ?>)">
                                        <i class="fas fa-comment"></i> <?php echo __("message_executor"); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
    </main>
    <div id="messageModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-comment"></i> <?php echo __("send_message"); ?></h2>
                <button class="modal-close" onclick="closeMessageModal()">×</button>
            </div>
            <form id="messageForm">
                <input type="hidden" id="recipient_id" name="recipient_id" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="message"><?php echo __("your_message"); ?></label>
                        <textarea id="message" name="message" placeholder="<?php echo __("type_message_here"); ?>" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeMessageModal()"><?php echo __("cancel"); ?></button>
                    <button type="submit" class="btn-primary"><?php echo __("send_message"); ?></button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script src="js/main.js"></script>
    <script src="js/task_executors.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!document.querySelector('input[name="task_id"]')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'task_id';
                hiddenInput.value = '<?php echo $task_id; ?>';
                document.body.appendChild(hiddenInput);
            }
        });
    </script>
</body>

</html>