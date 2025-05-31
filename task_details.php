<?php
session_start();
require_once 'db.php';

// Check if task ID is provided
if (!isset($_GET['id'])) {
    header("Location: taskes.php?error=" . urlencode("No task ID provided"));
    exit;
}

$task_id = $_GET['id'];

// Validate task ID is numeric
if (!is_numeric($task_id)) {
    header("Location: taskes.php?error=" . urlencode("Invalid task ID format"));
    exit;
}

try {
    // Fetch task details with category name and client information
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as category_name, 
               u1.name as client_name, u1.email as client_email, 
               u1.profile_picture as client_image,
               u1.registration_date as client_member_since,
               u2.name as executor_name, u2.email as executor_email,
               u2.profile_picture as executor_image,
               u2.registration_date as executor_member_since
        FROM tasks t 
        LEFT JOIN categories c ON t.category_id = c.category_id
        LEFT JOIN users u1 ON t.client_id = u1.id_user
        LEFT JOIN users u2 ON t.executor_id = u2.id_user
        WHERE t.task_id = ?
    ");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch();

    if (!$task) {
        header("Location: taskes.php?error=" . urlencode("Task not found"));
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error in task_details.php: " . $e->getMessage());
    header("Location: taskes.php?error=" . urlencode("Database error occurred"));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($task['title']); ?> - Task Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style/task_details.css">
    <link rel="stylesheet" href="style/user_sections.css">
    <link rel="stylesheet" href="./style/footer.css">
    <link rel="stylesheet" href="./style/header.css">
</head>

<body>
    <?php include 'components/header.php'; ?>

    <main class="container">
        <div class="task-hero">
            <h1><?php echo htmlspecialchars($task['title']); ?></h1>
            <div class="task-meta">
                <span class="category-badge">
                    <i class="fas fa-tag"></i>
                    <?php echo htmlspecialchars($task['category_name']); ?>
                </span>
                <span class="status-badge status-<?php echo strtolower($task['status']); ?>">
                    <i class="fas fa-circle"></i>
                    <?php echo ucfirst($task['status']); ?>
                </span>
            </div>
        </div>

        <div class="main-content">
            <div class="content-section">
                <!-- User Info Cards -->
                <div class="card info-card">
                    <!-- Client Section -->
                    <div class="user-section client-section">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>
                            Client Information
                        </h3>
                        <div class="user-details">
                            <div class="avatar">
                                <?php if ($task['client_image']): ?>
                                    <img src="<?php echo htmlspecialchars($task['client_image']); ?>" alt="Client Profile">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div class="user-info">
                                <h4><?php echo htmlspecialchars($task['client_name']); ?></h4>
                                <p><i class="fas fa-envelope"></i><?php echo htmlspecialchars($task['client_email']); ?></p>
                                <p><i class="fas fa-clock"></i>Member since <?php echo date('M Y', strtotime($task['client_member_since'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Executor Section -->
                    <?php if ($task['executor_id']): ?>
                    <div class="user-section executor-section">
                        <h3 class="section-title">
                            <i class="fas fa-user-tie"></i>
                            Executor Information
                        </h3>
                        <div class="user-details">
                            <div class="avatar">
                                <?php if ($task['executor_image']): ?>
                                    <img src="<?php echo htmlspecialchars($task['executor_image']); ?>" alt="Executor Profile">
                                <?php else: ?>
                                    <i class="fas fa-user-tie"></i>
                                <?php endif; ?>
                            </div>
                            <div class="user-info">
                                <h4><?php echo htmlspecialchars($task['executor_name']); ?></h4>
                                <p><i class="fas fa-envelope"></i><?php echo htmlspecialchars($task['executor_email']); ?></p>
                                <p><i class="fas fa-calendar-alt"></i>Member since <?php echo date('F Y', strtotime($task['executor_member_since'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- <div class="task-image-container">
                    <?php if ($task['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($task['image_url']); ?>" alt="Task Image" class="task-main-image">
                    <?php else: ?>
                        <div class="placeholder-image">
                            <i class="fas fa-image"></i>
                            <p>No image available</p>
                        </div>
                    <?php endif; ?>
                </div> -->

                <!-- Task Details Card -->
                <div class="card task-details">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">
                                <i class="fas fa-dollar-sign"></i>
                                <?php echo number_format($task['budget'], 2); ?>
                            </div>
                            <div class="stat-label">Budget</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('M d, Y', strtotime($task['deadline'])); ?>
                            </div>
                            <div class="stat-label">Deadline</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">
                                <i class="fas fa-clock"></i>
                                <?php echo date('M d, Y', strtotime($task['created_at'])); ?>
                            </div>
                            <div class="stat-label">Posted Date</div>
                        </div>
                    </div>

                    <!-- Task Image -->


                    <!-- Task Description -->
                    <div class="description-section">
                        <h3><i class="fas fa-info-circle"></i> Task Description</h3>
                        <div class="description-content">
                            <?php echo nl2br(htmlspecialchars($task['description'])); ?>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <?php if ($task['location']): ?>
                        <div class="location-section">
                            <div class="location-header">
                                <i class="fas fa-map-marker-alt"></i>
                                <h3>Location</h3>
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

            <!-- Action Card -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'executor' && $task['status'] === 'posted'): ?>
                <div class="card action-card">
                    <h3><i class="fas fa-handshake"></i> Interested in this task?</h3>
                    <div class="action-buttons">
                        <button class="btn-accept" onclick="acceptTask(<?php echo $task_id; ?>)">
                            <i class="fas fa-check"></i>
                            Accept Task
                        </button>
                        <?php if ($_SESSION['role'] === 'client' && $task['executor_id']): ?>
                            <a href="related_tasks.php?task_id=<?php echo $task['task_id']; ?>" class="btn-message">
                                <i class="fas fa-link"></i> View Related Tasks
                            </a>
                        <?php endif; ?>
                        <button class="btn-message" onclick="openMessageModal()">
                            <i class="fas fa-comment"></i>
                            Message Client
                        </button>
                        <button class="btn-reject" onclick="rejectTask(<?php echo $task_id; ?>)">
                            <i class="fas fa-times"></i>
                            Not Interested
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Message Modal -->
    <div id="messageModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-comment"></i> Message to Client</h2>
                <button class="modal-close" onclick="closeMessageModal()">×</button>
            </div>
            <form id="messageForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeMessageModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Task Actions
        function acceptTask(taskId) {
            if (confirm('Are you sure you want to accept this task?')) {
                fetch('api/accept_task.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            task_id: taskId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Task accepted successfully!');
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while accepting the task');
                    });
            }
        }

        function rejectTask(taskId) {
            if (confirm('Are you sure you want to reject this task?')) {
                fetch('api/reject_task.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            task_id: taskId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Task rejected successfully');
                            window.location.href = 'taskes.php';
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while rejecting the task');
                    });
            }
        }

        // Modal Functions
        function openMessageModal() {
            document.getElementById('messageModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMessageModal() {
            document.getElementById('messageModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Message Form Submission
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('message').value;

            fetch('api/send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        task_id: <?php echo $task_id; ?>,
                        recipient_id: <?php echo $task['client_id']; ?>,
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Message sent successfully!');
                        closeMessageModal();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending the message');
                });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                closeMessageModal();
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('messageModal').classList.contains('active')) {
                closeMessageModal();
            }
        });
    </script>

    <?php include 'components/footer.php'; ?>
</body>

</html>