<?php
session_start();

// Check if user is logged in before trying to access database
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'db.php';

// Handle role switching
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
            $role_error = "Error updating role. Please try again.";
        }
    } catch (PDOException $e) {
        error_log("Role update error: " . $e->getMessage());
        $role_error = "A database error occurred. Please try again later.";
    }
}

$pageTitle = "Dashboard";
include 'components/head.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./style/dashboard.css">
</head>

<body>
    <div class="container">
        <?php include 'components/header.php'; ?>

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
                        <h2>Welcome to Task Platform, <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?>! 👋</h2>
                        <p>Your account has been created successfully. You can now start using the platform.</p>
                    </div>
                    <button class="close-btn" onclick="document.getElementById('welcomeBanner').style.display='none';">×</button>
                </div>
            <?php endif; ?>

            <div class="user-info">
                <h1><i class="fas fa-user-circle"></i> Welcome, <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?>!</h1>
                <p class="user-role">
                    <i class="fas fa-user-tag"></i>
                    You are currently in <span><?php echo isset($_SESSION['role']) ? ucfirst(htmlspecialchars($_SESSION['role'])) : 'Default'; ?></span> mode
                </p>

                <div class="role-switch-form">
                    <form method="post">
                        <button type="submit" name="switch_role" class="btn btn-switch">
                            <i class="fas fa-sync"></i> Switch to <?php echo isset($_SESSION['role']) && $_SESSION['role'] === 'client' ? 'Executor' : 'Client'; ?> Mode
                        </button>
                    </form>
                </div>
            </div>

            <?php if ($_SESSION['role'] === 'client'): ?>
                <div class="main-actions">
                    <a href="my_tasks.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="action-content">
                            <h3>My Tasks</h3>
                            <p>View and manage your posted tasks</p>
                        </div>
                    </a>
                    
                    <a href="pending_bids.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="action-content">
                            <h3>Pending Bids</h3>
                            <p>Review and accept bids from executors</p>
                        </div>
                    </a>

                    <a href="messages.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="action-content">
                            <h3>Messages</h3>
                            <p>Chat with task executors</p>
                        </div>
                    </a>

                    <button onclick="openCreateTaskModal()" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="action-content">
                            <h3>Create New Task</h3>
                            <p>Post a new task for executors</p>
                        </div>
                    </button>

                    <a href="related_tasks.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="action-content">
                            <h3>Related Tasks</h3>
                            <p>View tasks with similar categories</p>
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
                            <h3>Available Tasks</h3>
                            <p>Find and apply for tasks</p>
                        </div>
                    </a>

                    <a href="my_assignments.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="action-content">
                            <h3>My Assignments</h3>
                            <p>View your active tasks</p>
                        </div>
                    </a>

                    <a href="messages.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="action-content">
                            <h3>Messages</h3>
                            <p>Chat with task clients</p>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Create Task Modal -->
        <div class="modal-overlay" id="createTaskModal">
            <div class="modal-container">
                <div class="modal-header">
                    <h2><i class="fas fa-plus-circle"></i> Create New Task</h2>
                    <button class="modal-close" onclick="closeCreateTaskModal()">×</button>
                </div>
                <form action="create_task_process.php" method="POST" id="createTaskForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="taskTitle">Task Title</label>
                            <input type="text" id="taskTitle" name="title" class="form-control" placeholder="Enter a clear title for your task" required>
                        </div>

                        <div class="form-group">
                            <label for="taskDescription">Task Description</label>
                            <textarea id="taskDescription" name="description" class="form-control" placeholder="Describe your task in detail..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="category">Select Category</label>
                            <select id="category" name="category_id" class="form-control" required>
                                <option value="">Select a category</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
                                while ($category = $stmt->fetch()) {
                                    echo '<option value="' . $category['category_id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="taskImage">Task Image</label>
                            <div class="image-upload-container">
                                <input type="file" id="taskImage" name="task_image" class="form-control" accept="image/*" required>
                                <div id="imagePreview" class="image-preview">
                                    <img src="" alt="Preview" style="display: none;">
                                    <span class="placeholder">No image selected</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="taskBudget">Budget ($)</label>
                            <input type="number" id="taskBudget" name="budget" class="form-control" placeholder="Enter your budget" min="1" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="taskDeadline">Deadline</label>
                            <input type="datetime-local" id="taskDeadline" name="deadline" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="taskLocation">Location (Optional)</label>
                            <input type="text" id="taskLocation" name="location" class="form-control" placeholder="Enter task location">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeCreateTaskModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script src="js/dashboard.js"></script>
</body>

</html>