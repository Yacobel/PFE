<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is an executor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'executor') {
    header("Location: login.php");
    exit;
}

$pageTitle = "My Assignments";
include 'components/head.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="./style/my_assignments.css">
</head>

<body>
    <?php include 'components/header.php'; ?>

    <div class="container">
        <div class="assignments-container">
            <div class="assignments-header">
                <h1><i class="fas fa-clipboard-check"></i> My Assignments</h1>
                <p>Manage your accepted tasks and update their status</p>
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
                                    <?php echo ucfirst($task['status']); ?>
                                </span>
                            </div>

                            <div class="assignment-details">
                                <div class="detail-row">
                                    <i class="fas fa-tag"></i>
                                    <span><?php echo htmlspecialchars($task['category_name']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>Budget: $<?php echo number_format($task['budget'], 2); ?></span>
                                </div>
                                <div class="detail-row">
                                    <i class="fas fa-calendar"></i>
                                    <span>Deadline: <?php echo date('M d, Y', strtotime($task['deadline'])); ?></span>
                                </div>

                                <!-- Client Info -->
                                <div class="client-info">
                                    <div class="client-avatar">
                                        <?php if ($task['client_profile']): ?>
                                            <img src="<?php echo htmlspecialchars($task['client_profile']); ?>" alt="Client">
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
                                        <i class="fas fa-check"></i> Mark as Completed
                                    </button>
                                <?php endif; ?>

                                <?php if ($task['status'] === 'assigned'): ?>
                                    <button onclick="updateTaskStatus(<?php echo $task['task_id']; ?>, 'in_progress')" class="btn btn-primary">
                                        <i class="fas fa-play"></i> Start Task
                                    </button>
                                <?php endif; ?>

                                <?php if ($task['status'] !== 'completed' && $task['status'] !== 'cancelled'): ?>
                                    <button onclick="showCancellationModal(<?php echo $task['task_id']; ?>)" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Cancel Task
                                    </button>
                                <?php endif; ?>

                                <a href="task_details.php?id=<?php echo $task['task_id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-assignments">
                    <i class="fas fa-clipboard"></i>
                    <h2>No Assignments Yet</h2>
                    <p>You haven't accepted any tasks yet. Browse available tasks to get started!</p>
                    <a href="taskes.php" class="btn">
                        <i class="fas fa-search"></i> Browse Tasks
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cancellation Modal -->
    <div id="cancellationModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:50%; border-radius:5px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
            <span class="close" style="color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
            <h2 style="margin-top:0;">Cancel Task</h2>
            <p>Please provide a reason for cancelling this task:</p>
            <textarea id="cancellationReason" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:4px; min-height:100px;"></textarea>
            <input type="hidden" id="taskIdToCancel">
            <div style="text-align:right;">
                <button onclick="closeModal()" style="background-color:#ccc; border:none; padding:10px 15px; margin-right:10px; border-radius:4px; cursor:pointer;">Cancel</button>
                <button onclick="confirmCancellation()" style="background-color:#f44336; color:white; border:none; padding:10px 15px; border-radius:4px; cursor:pointer;">Confirm Cancellation</button>
            </div>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("cancellationModal");
        var span = document.getElementsByClassName("close")[0];

        // Show the cancellation modal
        function showCancellationModal(taskId) {
            document.getElementById("taskIdToCancel").value = taskId;
            document.getElementById("cancellationReason").value = "";
            modal.style.display = "block";
        }

        // Close the modal
        function closeModal() {
            modal.style.display = "none";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            closeModal();
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Confirm cancellation and submit the form
        function confirmCancellation() {
            var taskId = document.getElementById("taskIdToCancel").value;
            var reason = document.getElementById("cancellationReason").value;

            fetch('api/update_task_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: taskId,
                    status: 'cancelled',
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while cancelling the task');
            });

            closeModal();
        }

        function updateTaskStatus(taskId, newStatus) {
            if (confirm('Are you sure you want to update this task\'s status?')) {
                fetch('api/update_task_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            task_id: taskId,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the task status');
                    });
            }
        }
    </script>

</body>

</html>