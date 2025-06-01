<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: login.php');
    exit();
}

// Check if task ID is provided
if (!isset($_GET['id'])) {
    header("Location: pending_bids.php?error=" . urlencode("No task ID provided"));
    exit;
}

$task_id = $_GET['id'];

// Validate task ID is numeric
if (!is_numeric($task_id)) {
    header("Location: pending_bids.php?error=" . urlencode("Invalid task ID format"));
    exit;
}

try {
    // Fetch basic task information
    $stmt = $pdo->prepare("
        SELECT t.task_id, t.title, t.budget, t.status, c.name as category_name
        FROM tasks t 
        LEFT JOIN categories c ON t.category_id = c.category_id
        WHERE t.task_id = ? AND t.client_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();
    
    if (!$task) {
        header("Location: pending_bids.php?error=" . urlencode("Task not found or you don't have permission to view it"));
        exit;
    }
    
    // Fetch bids with executor information
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
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Executors - Task Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/task_details.css">
    <link rel="stylesheet" href="style/bids.css">
    <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/footer.css">
    <style>
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .task-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .task-info span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            background-color: #f5f5f5;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .executor-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .executor-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .executor-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .executor-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .executor-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .executor-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .executor-avatar i {
            font-size: 2rem;
            color: #777;
        }
        
        .executor-details h3 {
            margin: 0 0 0.5rem;
            font-size: 1.25rem;
        }
        
        .executor-details p {
            margin: 0 0 0.25rem;
            font-size: 0.9rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .executor-stats {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .stat-item {
            padding: 0.5rem 1rem;
            background-color: #f5f5f5;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .rating i {
            color: #ffc107;
        }
        
        .bid-details {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .bid-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4a6cf7;
            margin-bottom: 0.5rem;
        }
        
        .bid-proposal {
            margin: 1rem 0;
            padding: 1rem;
            background-color: #f9f9f9;
            border-radius: 6px;
            border-left: 3px solid #4a6cf7;
        }
        
        .executor-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include 'components/header.php'; ?>

    <main class="container">
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>
            <div class="task-header">
                <div>
                    <h1><?php echo htmlspecialchars($task['title']); ?></h1>
                    <div class="task-info">
                        <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($task['category_name']); ?></span>
                        <span><i class="fas fa-dollar-sign"></i> Budget: $<?php echo number_format($task['budget'], 2); ?></span>
                        <span><i class="fas fa-gavel"></i> <?php echo count($bids); ?> Bid<?php echo count($bids) != 1 ? 's' : ''; ?></span>
                    </div>
                </div>
                <a href="pending_bids.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Pending Bids
                </a>
            </div>

            <?php if (empty($bids)): ?>
                <div class="empty-state">
                    <i class="fas fa-gavel"></i>
                    <h2>No Bids Yet</h2>
                    <p>There are no bids for this task yet. Check back later.</p>
                </div>
            <?php else: ?>
                <h2><i class="fas fa-user-tie"></i> Executors Who Bid on Your Task</h2>
                <p>Review executor profiles and select the best fit for your task</p>
                
                <div class="executors-list">
                    <?php foreach ($bids as $bid): ?>
                        <div class="executor-card">
                            <div class="executor-header">
                                <div class="executor-info">
                                    <div class="executor-avatar">
                                        <?php if ($bid['profile_picture']): ?>
                                            <img src="<?php echo htmlspecialchars($bid['profile_picture']); ?>" alt="Executor Profile">
                                        <?php else: ?>
                                            <i class="fas fa-user-tie"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="executor-details">
                                        <h3><?php echo htmlspecialchars($bid['name']); ?></h3>
                                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($bid['email']); ?></p>
                                        <p><i class="fas fa-calendar"></i> Member since <?php echo date('M Y', strtotime($bid['registration_date'])); ?></p>
                                        
                                        <div class="executor-stats">
                                            <div class="stat-item">
                                                <i class="fas fa-check-circle"></i>
                                                <span><?php echo $bid['completed_tasks']; ?> Completed Tasks</span>
                                            </div>
                                            <?php if ($bid['avg_rating']): ?>
                                                <div class="stat-item">
                                                    <div class="rating">
                                                        <i class="fas fa-star"></i>
                                                        <span><?php echo number_format($bid['avg_rating'], 1); ?></span>
                                                    </div>
                                                    <span>Rating</span>
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
                                    <h4><i class="fas fa-quote-left"></i> Proposal</h4>
                                    <p><?php echo nl2br(htmlspecialchars($bid['proposal_text'])); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="executor-actions">
                                <?php if ($bid['status'] === 'pending'): ?>
                                    <button class="btn-primary" onclick="acceptBid(<?php echo $bid['bid_id']; ?>, <?php echo $task_id; ?>)">
                                        <i class="fas fa-check"></i> Accept Bid
                                    </button>
                                <?php elseif ($bid['status'] === 'accepted'): ?>
                                    <div class="bid-status accepted">
                                        <i class="fas fa-check-circle"></i> Accepted
                                    </div>
                                <?php elseif ($bid['status'] === 'rejected'): ?>
                                    <div class="bid-status rejected">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </div>
                                <?php endif; ?>
                                
                                <button class="btn-message" onclick="openMessageModal(<?php echo $bid['id_user']; ?>)">
                                    <i class="fas fa-comment"></i> Message Executor
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <!-- Message Modal -->
    <div id="messageModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-comment"></i> Send Message</h2>
                <button class="modal-close" onclick="closeMessageModal()">×</button>
            </div>
            <form id="messageForm">
                <input type="hidden" id="recipient_id" name="recipient_id" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" placeholder="Type your message here..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeMessageModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script>
        // Accept Bid
        function acceptBid(bidId, taskId) {
            if (confirm('Are you sure you want to accept this bid? This will assign the task to this executor.')) {
                fetch('api/accept_bid.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        bid_id: bidId,
                        task_id: taskId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Bid accepted successfully! The task has been assigned to the executor.');
                        window.location.href = 'related_tasks.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while accepting the bid');
                });
            }
        }

        // Message Modal Functions
        function openMessageModal(recipientId) {
            document.getElementById('messageModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            document.getElementById('recipient_id').value = recipientId;
        }

        function closeMessageModal() {
            document.getElementById('messageModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Message Form Submission
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('message').value;
            const recipientId = document.getElementById('recipient_id').value;

            fetch('api/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: <?php echo $task_id; ?>,
                    recipient_id: recipientId,
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
</body>

</html>
