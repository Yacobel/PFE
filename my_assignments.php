<?php
session_start();
require_once 'config/languages.php';
require_once 'config/db.php';

// Check if user is logged in and is an executor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'executor') {
    header("Location: login.php");
    exit;
}

$pageTitle = __("my_assignments");
include 'components/head.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./style/my_assignments.css">
    <!-- <style>
        .language-selector {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .language-selector a {
            text-decoration: none;
            color: #333;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 3px;
            font-weight: 500;
        }
        .language-selector a.active {
            background: #4ce595;
            color: white;
        }
        [dir="rtl"] .language-selector {
            left: 20px;
            right: auto;
        }
    </style> -->
</head>

<body>
    <!-- Language Switcher -->
    <div class="language-selector">
        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
        <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>

    <main class="container">
        <?php include 'components/header.php'; ?>
        <div class="dashborde-container">
            <div class="assignments-container">
                <div class="assignments-header">
                    <h1><i class="fas fa-clipboard-check"></i> <?php echo __("my_assignments"); ?></h1>
                    <p><?php echo __("manage_assignments"); ?></p>
                </div>

                <?php
                // Fetch tasks assigned to the executor
                $stmt = $pdo->prepare("
                    SELECT t.*, c.name as category_name, 
                           u.name as client_name, u.email as client_email,
                           u.profile_picture as client_profile
                    FROM tasks t 
                    LEFT JOIN categories c ON t.category_id = c.category_id
                    LEFT JOIN users u ON t.client_id = u.id_user
                    WHERE t.executor_id = ? AND t.status != 'posted'
                    ORDER BY t.accepted_at DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $assignments = $stmt->fetchAll();

                if (count($assignments) > 0): ?>
                    <div class="assignments-grid">
                        <?php foreach ($assignments as $task): ?>
                            <div class="assignment-card">
                                <div class="assignment-header">
                                    <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                                    <span class="status-badge status-<?php echo strtolower($task['status']); ?>">
                                        <?php echo __($task['status']); ?>
                                    </span>
                                </div>

                                <div class="assignment-details">
                                    <div class="detail-row">
                                        <i class="fas fa-tag"></i>
                                        <span><?php echo htmlspecialchars($task['category_name']); ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span><?php echo __("budget"); ?>: $<?php echo number_format($task['budget'], 2); ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo __("deadline"); ?>: <?php echo date('M d, Y', strtotime($task['deadline'])); ?></span>
                                    </div>

                                    <!-- Client Info -->
                                    <div class="client-info">
                                        <div class="client-avatar">
                                            <?php if ($task['client_profile']): ?>
                                                <img src="<?php echo htmlspecialchars($task['client_profile']); ?>" alt="<?php echo __("client"); ?>">
                                            <?php else: ?>
                                                <i class="fas fa-user"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="client-details">
                                            <p class="client-name"><?php echo htmlspecialchars($task['client_name']); ?></p>
                                            <p class="client-email"><?php echo htmlspecialchars($task['client_email']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="assignment-actions">
                                    <?php if ($task['status'] === 'in_progress'): ?>
                                        <button onclick="updateTaskStatus(<?php echo $task['task_id']; ?>, 'completed')" class="btn btn-success">
                                            <i class="fas fa-check"></i> <?php echo __("mark_completed"); ?>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($task['status'] === 'assigned'): ?>
                                        <button onclick="updateTaskStatus(<?php echo $task['task_id']; ?>, 'in_progress')" class="btn btn-primary">
                                            <i class="fas fa-play"></i> <?php echo __("start_task"); ?>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($task['status'] !== 'completed' && $task['status'] !== 'cancelled'): ?>
                                        <button onclick="showCancellationModal(<?php echo $task['task_id']; ?>)" class="btn btn-danger">
                                            <i class="fas fa-times"></i> <?php echo __("cancel_task"); ?>
                                        </button>
                                    <?php endif; ?>

                                    <a href="task_details.php?id=<?php echo $task['task_id']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-eye"></i> <?php echo __("view_details"); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-assignments">
                        <i class="fas fa-clipboard"></i>
                        <h2><?php echo __("no_assignments"); ?></h2>
                        <p><?php echo __("no_assignments_message"); ?></p>
                        <a href="taskes.php" class="btn">
                            <i class="fas fa-search"></i> <?php echo __("browse_tasks"); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Cancellation Modal -->
    <div id="cancellationModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:50%; border-radius:5px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
            <span class="close" style="color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
            <h2 style="margin-top:0;"><?php echo __("cancel_task"); ?></h2>
            <p><?php echo __("cancel_task_reason"); ?></p>
            <textarea id="cancellationReason" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:4px; min-height:100px;"></textarea>
            <input type="hidden" id="taskIdToCancel">
            <div style="text-align:right;">
                <button onclick="closeModal()" style="background-color:#ccc; border:none; padding:10px 15px; margin-right:10px; border-radius:4px; cursor:pointer;"><?php echo __("cancel"); ?></button>
                <button onclick="confirmCancellation()" style="background-color:#f44336; color:white; border:none; padding:10px 15px; border-radius:4px; cursor:pointer;"><?php echo __("confirm_cancellation"); ?></button>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script src="js/my_assignments.js"></script>
</body>
</html>