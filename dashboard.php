<?php
session_start();
require_once 'config/languages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/db.php';

if (isset($_POST['switch_role'])) {
    try {
        $new_role = $_SESSION['role'] === 'client' ? 'executor' : 'client';
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id_user = ?");
        $result = $stmt->execute([$new_role, $_SESSION['user_id']]);

        if ($result) {
            $_SESSION['role'] = $new_role;
            header("Location: dashboard.php");
            exit;
        } else {
            $role_error = __("role_update_error");
        }
    } catch (PDOException $e) {
        error_log("Role update error: " . $e->getMessage());
        $role_error = __("database_error");
    }
}

$pageTitle = __("dashboard");
include 'components/head.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./style/dashboard.css">
    
</head>

<body>
    <div class="container">
        <?php include 'components/header.php'; ?>
        <div class="language-selector">
            <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
            <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
        </div>

        <div class="dashboard-container">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle"></i>
                    <?php
                    echo htmlspecialchars($_SESSION['success_message']);
                    unset($_SESSION['success_message']);
                    ?>
                    <button class="close-btn" onclick="this.parentElement.style.display='none';">×</button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message" id="errorMessage">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php
                    echo htmlspecialchars($_SESSION['error_message']);
                    unset($_SESSION['error_message']);
                    ?>
                    <button class="close-btn" onclick="this.parentElement.style.display='none';">×</button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['welcome'])): ?>
                <div class="welcome-banner" id="welcomeBanner">
                    <div>
                        <h2><?php echo __("welcome_to_platform"); ?>, <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : __("user"); ?>! 👋</h2>
                        
                </div>
            <?php endif; ?>

            <div class="user-info">
                <h1><i class="fas fa-user-circle"></i> <?php echo __("welcome"); ?>, <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : __("user"); ?>!</h1>
                <div class="rool">
                    <p class="user-role">
                        <i class="fas fa-user-tag"></i>
                        <?php echo __("current_role"); ?> <span><?php echo isset($_SESSION['role']) ? ucfirst(htmlspecialchars($_SESSION['role'])) : __("default"); ?></span> <?php echo __("mode"); ?>
                    </p>
                    <div class="role-switch-form">
                        <form method="post">
                            <button type="submit" name="switch_role" class="btn btn-switch">
                                <i class="fas fa-sync"></i> <?php echo __("switch_to"); ?> <?php echo isset($_SESSION['role']) && $_SESSION['role'] === 'client' ? __("executor") : __("client"); ?> <?php echo __("mode"); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <?php if ($_SESSION['role'] === 'client'): ?>
                <div class="main-actions">
                    <button onclick="openCreateTaskModal()" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="action-content">
                            <h3><?php echo __("create_new_task"); ?></h3>
                            <p><?php echo __("post_new_task_message"); ?></p>
                        </div>
                    </button>
                    <a href="my_tasks.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="action-content">
                            <h3><?php echo __("my_tasks"); ?></h3>
                            <p><?php echo __("view_manage_tasks"); ?></p>
                        </div>
                    </a>

                    <a href="pending_bids.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="action-content">
                            <h3><?php echo __("pending_bids"); ?></h3>
                            <p><?php echo __("review_accept_bids"); ?></p>
                        </div>
                    </a>
                    <a href="related_tasks.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="action-content">
                            <h3><?php echo __("related_tasks"); ?></h3>
                            <p><?php echo __("view_similar_tasks"); ?></p>
                        </div>
                    </a>
                    <a href="messages.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="action-content">
                            <h3><?php echo __("messages"); ?></h3>
                            <p><?php echo __("chat_with_executors"); ?></p>
                        </div>
                    </a>
                </div>
            <?php else: ?>
                <div class="main-actions">
                    <!-- Executor mode actions -->
                    <a href="taskes.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="action-content">
                            <h3><?php echo __("available_tasks"); ?></h3>
                            <p><?php echo __("find_apply_tasks"); ?></p>
                        </div>
                    </a>

                    <a href="my_assignments.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="action-content">
                            <h3><?php echo __("my_assignments"); ?></h3>
                            <p><?php echo __("view_active_tasks"); ?></p>
                        </div>
                    </a>

                    <a href="messages.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="action-content">
                            <h3><?php echo __("messages"); ?></h3>
                            <p><?php echo __("chat_with_clients"); ?></p>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Create Task Modal -->
        <div class="modal-overlay" id="createTaskModal">
            <div class="modal-container">
                <div class="modal-header">
                    <h2><i class="fas fa-plus-circle"></i> <?php echo __("create_new_task"); ?></h2>
                    <button class="modal-close" onclick="closeCreateTaskModal()">×</button>
                </div>
                <form action="create_task_process.php" method="POST" id="createTaskForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="taskTitle"><?php echo __("task_title"); ?></label>
                            <input type="text" id="taskTitle" name="title" class="form-control" placeholder="<?php echo __("enter_task_title"); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="taskDescription"><?php echo __("task_description"); ?></label>
                            <textarea id="taskDescription" name="description" class="form-control" placeholder="<?php echo __("describe_task_detail"); ?>" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="category"><?php echo __("select_category"); ?></label>
                            <select id="category" name="category_id" class="form-control" required>
                                <option value=""><?php echo __("select_category_placeholder"); ?></option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
                                while ($category = $stmt->fetch()) {
                                    echo '<option value="' . $category['category_id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="taskImage"><?php echo __("task_image"); ?></label>
                            <div class="image-upload-container">
                                <input type="file" id="taskImage" name="task_image" class="form-control" accept="image/*" required>
                                <div id="imagePreview" class="image-preview">
                                    <img src="" alt="Preview" style="display: none;">
                                    <span class="placeholder"><?php echo __("no_image_selected"); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="taskBudget"><?php echo __("budget"); ?> ($)</label>
                            <input type="number" id="taskBudget" name="budget" class="form-control" placeholder="<?php echo __("enter_budget"); ?>" min="1" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="taskDeadline"><?php echo __("deadline"); ?></label>
                            <input type="datetime-local" id="taskDeadline" name="deadline" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="taskLocation"><?php echo __("location"); ?> (<?php echo __("optional"); ?>)</label>
                            <input type="text" id="taskLocation" name="location" class="form-control" placeholder="<?php echo __("enter_location"); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeCreateTaskModal()"><?php echo __("cancel"); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo __("create_task"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script src="js/dashboard.js"></script>
</body>

</html>