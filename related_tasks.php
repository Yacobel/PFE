<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: login.php');
    exit();
}

// Get task ID if provided, otherwise show all tasks
$task_id = isset($_GET['task_id']) ? $_GET['task_id'] : null;

if ($task_id) {
    // Get the categories of the current task
    $stmt = $pdo->prepare("
        SELECT t1.category_id, t1.task_id,
        (SELECT COUNT(*) FROM tasks t2 
         WHERE t2.category_id = t1.category_id 
         AND t2.task_id != t1.task_id 
         AND t2.status = 'pending') as related_count
        FROM tasks t1
        WHERE t1.task_id = ? AND t1.client_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();

    if ($task && $task['related_count'] > 0) {
        // Get related tasks with the same category
        $stmt = $pdo->prepare("
            SELECT t.*, c.name as category_name, 
                   u.name as executor_name, u.profile_picture as executor_image,
                   u.email as executor_email,
                   t.status as task_status, t.created_at, t.deadline
            FROM tasks t
            LEFT JOIN categories c ON t.category_id = c.category_id
            LEFT JOIN users u ON t.executor_id = u.id_user
            WHERE t.category_id = ? 
            AND t.task_id != ? 
            AND t.client_id = ?
            AND t.executor_id IS NOT NULL
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$task['category_id'], $task_id, $_SESSION['user_id']]);
    } else {
        // No related tasks found
        header('Location: dashboard.php');
        exit();
    }
} else {
    // Get only tasks that have related tasks in the same category
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as category_name, 
               u.name as executor_name, u.profile_picture as executor_image,
               u.email as executor_email,
               t.status as task_status, t.created_at, t.deadline
        FROM tasks t
        LEFT JOIN categories c ON t.category_id = c.category_id
        LEFT JOIN users u ON t.executor_id = u.id_user
        WHERE t.client_id = ?
        AND t.executor_id IS NOT NULL
        ORDER BY c.name, t.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
}

$related_tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Related Tasks</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/related_tasks.css">
    <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'components/header.php'; ?>

    <div class="container">
        <div class="dashboard-container">
            <div class="assignments-header">
                <h1>
                    <i class="fas fa-link"></i>
                    <?php echo $task_id ? 'Related Tasks' : 'All Tasks by Category'; ?>
                </h1>
                <p>View tasks with similar categories and their progress</p>
            </div>

            <?php if (empty($related_tasks)): ?>
                <div class="empty-state">
                    <i class="fas fa-tasks"></i>
                    <h2>No Related Tasks Found</h2>
                    <p>There are no other tasks in this category yet.</p>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
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
                                        <span class="card-status status-<?php echo strtolower($task['task_status']); ?>">
                                            <i class="fas fa-circle"></i>
                                            <?php echo ucfirst($task['task_status']); ?>
                                        </span>
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
                                    <?php endif; ?>
                                    <span>
                                        <i class="fas fa-calendar"></i>
                                        Due: <?php echo date('M j, Y', strtotime($task['deadline'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-clock"></i>
                                        Created: <?php echo date('M j, Y', strtotime($task['created_at'])); ?>
                                    </span>
                                </div>

                                <div class="card-actions">
                                    <a href="task_details.php?id=<?php echo $task['task_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                    <?php if ($task['executor_id']): ?>
                                    <a href="messages.php?user=<?php echo $task['executor_id']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-message"></i>
                                        Message
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

    <?php include 'components/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any JavaScript functionality here if needed
        });
    </script>
</body>

</html>