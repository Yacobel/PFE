<?php
session_start();
require_once 'config/languages.php';
require_once 'config/db.php';
if (!isset($_GET['id'])) {
    header("Location: taskes.php?error=" . urlencode(__("no_task_id")));
    exit;
}
$task_id = $_GET['id'];
if (!is_numeric($task_id)) {
    header("Location: taskes.php?error=" . urlencode(__("invalid_task_id")));
    exit;
}
try {
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as category_name, 
               u1.name as client_name, u1.email as client_email, 
               u1.profile_picture as client_image,
               u1.registration_date as client_member_since,
               u2.name as executor_name, u2.email as executor_email,
               u2.profile_picture as executor_image,
               u2.registration_date as executor_member_since,
               (SELECT b.bid_amount FROM bids b WHERE b.task_id = t.task_id AND b.status = 'accepted' LIMIT 1) as bid_amount
        FROM tasks t 
        LEFT JOIN categories c ON t.category_id = c.category_id
        LEFT JOIN users u1 ON t.client_id = u1.id_user
        LEFT JOIN users u2 ON t.executor_id = u2.id_user
        WHERE t.task_id = ?
    ");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch();
    $bids = [];
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("
            SELECT b.*, u.name as executor_name, u.email as executor_email, 
                   u.profile_picture as executor_image, u.registration_date as executor_member_since
            FROM bids b
            JOIN users u ON b.executor_id = u.id_user
            WHERE b.task_id = ?
            ORDER BY b.bid_date DESC
        ");
        $stmt->execute([$task_id]);
        $bids = $stmt->fetchAll();
    }
    $has_bid = false;
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'executor') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bids WHERE task_id = ? AND executor_id = ?");
        $stmt->execute([$task_id, $_SESSION['user_id']]);
        $has_bid = ($stmt->fetchColumn() > 0);
    }
    if (!$task) {
        header("Location: taskes.php?error=" . urlencode(__("task_not_found")));
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error in task_details.php: " . $e->getMessage());
    header("Location: taskes.php?error=" . urlencode(__("database_error")));
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($task['title']); ?> - <?php echo __("task_details"); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./style/header.css">
    <link rel="stylesheet" href="./style/task_details.css">
</head>

<body>
    <div class="language-selector">
        <a href="?id=<?php echo $task_id; ?>&lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
        <a href="?id=<?php echo $task_id; ?>&lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>
    <main class="container">
        <?php include 'components/header.php'; ?>
        <div class="dashborde-container">
            <div class="task-hero">
                <h1><?php echo htmlspecialchars($task['title']); ?></h1>
                <div class="task-meta">
                    <span class="category-badge">
                        <i class="fas fa-tag"></i>
                        <?php echo htmlspecialchars($task['category_name']); ?>
                    </span>
                    <span class="status-badge status-<?php echo strtolower($task['status']); ?>">
                        <i class="fas fa-circle"></i>
                        <?php echo __($task['status']); ?>
                    </span>
                </div>
            </div>
            <div class="main-content">
                <div class="content-section">
                    <div class="card info-card">
                        <div class="user-section client-section">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i>
                                <?php echo __("client_information"); ?>
                            </h3>
                            <div class="user-details">
                                <div class="avatar">
                                    <?php if ($task['client_image']): ?>
                                        <img src="<?php echo htmlspecialchars($task['client_image']); ?>" alt="<?php echo __("client_profile"); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="user-info">
                                    <h4><?php echo htmlspecialchars($task['client_name']); ?></h4>
                                    <p><i class="fas fa-envelope"></i><?php echo htmlspecialchars($task['client_email']); ?></p>
                                    <p><i class="fas fa-clock"></i><?php echo __("member_since"); ?> <?php echo date('M Y', strtotime($task['client_member_since'])); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php if ($task['executor_id']): ?>
                            <div class="user-section executor-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user-tie"></i>
                                    <?php echo __("executor_information"); ?>
                                </h3>
                                <div class="user-details">
                                    <div class="avatar">
                                        <?php if ($task['executor_image']): ?>
                                            <img src="<?php echo htmlspecialchars($task['executor_image']); ?>" alt="<?php echo __("executor_profile"); ?>">
                                        <?php else: ?>
                                            <i class="fas fa-user-tie"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-info">
                                        <h4><?php echo htmlspecialchars($task['executor_name']); ?></h4>
                                        <p><i class="fas fa-envelope"></i><?php echo htmlspecialchars($task['executor_email']); ?></p>
                                        <p><i class="fas fa-calendar-alt"></i><?php echo __("member_since"); ?> <?php echo date('F Y', strtotime($task['executor_member_since'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card task-details">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value">
                                    <i class="fas fa-dollar-sign"></i>
                                    <?php echo number_format($task['budget'], 2); ?>
                                </div>
                                <div class="stat-label"><?php echo __("budget"); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('M d, Y', strtotime($task['deadline'])); ?>
                                </div>
                                <div class="stat-label"><?php echo __("deadline"); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('M d, Y', strtotime($task['created_at'])); ?>
                                </div>
                                <div class="stat-label"><?php echo __("posted_date"); ?></div>
                            </div>
                        </div>
                        <div class="description-section">
                            <h3><i class="fas fa-info-circle"></i> <?php echo __("task_description"); ?></h3>
                            <div class="description-content">
                                <?php echo nl2br(htmlspecialchars($task['description'])); ?>
                            </div>
                        </div>
                        <?php if ($task['location']): ?>
                            <div class="location-section">
                                <div class="location-header">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <h3><?php echo __("location"); ?></h3>
                                </div>
                                <div class="location-details">
                                    <p><?php echo htmlspecialchars($task['location']); ?></p>
                                </div>
                                <div class="location-map">
                                    <iframe
                                        src="https://maps.google.com/maps?q=<?php echo urlencode($task['location']); ?>&output=embed"
                                        frameborder="0"
                                        style="border:0;"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'executor' && $task['status'] === 'posted'): ?>
                    <div class="card action-card">
                        <h3><i class="fas fa-handshake"></i> <?php echo __("interested_in_task"); ?></h3>
                        <?php if (isset($has_bid) && $has_bid): ?>
                            <div class="bid-submitted-message">
                                <i class="fas fa-check-circle"></i>
                                <p><?php echo __("bid_already_submitted"); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="bid-form">
                                <form id="bidForm">
                                    <input type="hidden" id="task_id" value="<?php echo $task_id; ?>">
                                    <div class="form-group">
                                        <label for="bid_amount"><?php echo __("your_bid_amount"); ?></label>
                                        <input type="number" id="bid_amount" min="1" step="0.01" required
                                            placeholder="<?php echo __("enter_bid_amount"); ?>" value="<?php echo htmlspecialchars($task['budget']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="proposal_text"><?php echo __("proposal_message"); ?> (<?php echo __("optional"); ?>)</label>
                                        <textarea id="proposal_text" placeholder="<?php echo __("describe_why_fit"); ?>" rows="4"></textarea>
                                    </div>
                                    <div class="action-buttons">
                                        <button type="submit" class="btn-primary">
                                            <i class="fas fa-gavel"></i> <?php echo __("submit_bid"); ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'client' && $task['status'] === 'in_progress' && $task['client_id'] == $_SESSION['user_id']): ?>
                    <div class="card payment-card">
                        <h3><i class="fas fa-credit-card"></i> <?php echo __("task_completed"); ?></h3>
                        <p><?php echo __("process_payment_message"); ?></p>
                        <div class="payment-details">
                            <div class="payment-info">
                                <div class="info-item">
                                    <span class="label"><?php echo __("executor"); ?>:</span>
                                    <span class="value"><?php echo htmlspecialchars($task['executor_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label"><?php echo __("amount"); ?>:</span>
                                    <span class="value">$<?php echo number_format($task['bid_amount'] ? $task['bid_amount'] : $task['budget'], 2); ?></span>
                                </div>
                            </div>
                            <button id="paymentButton" class="btn-primary" onclick="processPayment(<?php echo $task_id; ?>)">
                                <i class="fas fa-check-circle"></i> <?php echo __("confirm_completion_pay"); ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'executor' && $task['status'] === 'in_progress' && $task['executor_id'] == $_SESSION['user_id']): ?>
                    <div class="card cancel-card">
                        <h3><i class="fas fa-times-circle"></i> <?php echo __("need_to_cancel"); ?></h3>
                        <p><?php echo __("cancel_task_message"); ?></p>
                        <div class="cancel-form">
                            <div class="form-group">
                                <label for="cancel_reason"><?php echo __("reason_for_cancellation"); ?> (<?php echo __("optional"); ?>)</label>
                                <textarea id="cancel_reason" placeholder="<?php echo __("explain_cancellation"); ?>" rows="3"></textarea>
                            </div>
                            <button id="cancelButton" class="btn-danger" onclick="cancelTask(<?php echo $task_id; ?>)">
                                <i class="fas fa-times"></i> <?php echo __("cancel_task_assignment"); ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id']) && !empty($bids) && $_SESSION['role'] === 'client'): ?>
                    <div class="card bids-card">
                        <h3><i class="fas fa-gavel"></i> <?php echo ($_SESSION['user_id'] == $task['client_id']) ? __("bids_for_your_task") : __("bids_for_task"); ?></h3>
                        <p>
                            <?php if ($_SESSION['user_id'] == $task['client_id']): ?>
                                <?php echo __("review_select_best_offer"); ?>
                            <?php else: ?>
                                <?php echo __("current_bids_submitted"); ?>
                            <?php endif; ?>
                        </p>
                        <div class="bids-list">
                            <?php foreach ($bids as $bid): ?>
                                <div class="bid-item">
                                    <div class="bid-header">
                                        <div class="bidder-info">
                                            <div class="bidder-avatar">
                                                <?php if ($bid['executor_image']): ?>
                                                    <img src="<?php echo htmlspecialchars($bid['executor_image']); ?>" alt="<?php echo __("executor_profile"); ?>">
                                                <?php else: ?>
                                                    <i class="fas fa-user-tie"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h4><?php echo htmlspecialchars($bid['executor_name']); ?></h4>
                                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($bid['executor_email']); ?></p>
                                                <p><i class="fas fa-calendar"></i> <?php echo __("member_since"); ?> <?php echo date('M Y', strtotime($bid['executor_member_since'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="bid-amount">
                                            <span class="amount">$<?php echo number_format($bid['bid_amount'], 2); ?></span>
                                            <span class="bid-date"><?php echo __("bid_on"); ?> <?php echo date('M j, Y', strtotime($bid['bid_date'])); ?></span>
                                        </div>
                                    </div>
                                    <?php if (!empty($bid['proposal_text'])): ?>
                                        <div class="bid-proposal">
                                            <p><?php echo nl2br(htmlspecialchars($bid['proposal_text'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="bid-actions">
                                        <?php if (($bid['status'] === 'pending' && $_SESSION['user_id'] == $task['client_id']) || ($task['status'] === 'posted' && $bid['status'] !== 'cancelled' && $_SESSION['user_id'] == $task['client_id'])): ?>
                                            <?php if ($_SESSION['user_id'] == $task['client_id']): ?>
                                                <button class="btn-primary" onclick="acceptBid(<?php echo $bid['bid_id']; ?>, <?php echo $task['task_id']; ?>)">
                                                    <i class="fas fa-check"></i> <?php echo __("accept_bid"); ?>
                                                </button>
                                                <button class="btn-message" onclick="openMessageModalWithRecipient(<?php echo $bid['executor_id']; ?>)">
                                                    <i class="fas fa-comment"></i> <?php echo __("send_message"); ?>
                                                </button>
                                            <?php else: ?>
                                                <div class="bid-status pending">
                                                    <i class="fas fa-clock"></i> <?php echo __("pending"); ?>
                                                </div>
                                                <?php if ($_SESSION['role'] === 'executor' && $_SESSION['user_id'] == $bid['executor_id']): ?>
                                                    <div class="bid-note"><?php echo __("bid_awaiting_review"); ?></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php elseif ($bid['status'] === 'accepted'): ?>
                                            <div class="bid-status accepted">
                                                <i class="fas fa-check-circle"></i> <?php echo __("accepted"); ?>
                                            </div>
                                        <?php elseif ($bid['status'] === 'done'): ?>
                                            <div class="bid-status done">
                                                <i class="fas fa-check-double"></i> <?php echo __("done"); ?>
                                            </div>
                                        <?php elseif ($bid['status'] === 'rejected' && $task['status'] !== 'posted'): ?>
                                            <div class="bid-status rejected">
                                                <i class="fas fa-times-circle"></i> <?php echo __("rejected"); ?>
                                            </div>
                                        <?php elseif ($bid['status'] === 'cancelled'): ?>
                                            <div class="bid-status cancelled">
                                                <i class="fas fa-ban"></i> <?php echo __("cancelled"); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
    </main>
    <div id="messageModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-comment"></i> <?php echo __("send_message"); ?></h2>
                <button class="modal-close" onclick="closeMessageModal()">×</button>
            </div>
            <form id="messageForm">
                <input type="hidden" id="recipient_id" name="recipient_id" value="<?php echo $task['client_id']; ?>">
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
    <script src="js/task_details.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('task_id')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'task_id';
                hiddenInput.name = 'task_id';
                hiddenInput.value = '<?php echo $task_id; ?>';
                document.body.appendChild(hiddenInput);
            }
        });
    </script>
</body>

</html>