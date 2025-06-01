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
    
    // Fetch bids for this task
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
    
    // Check if current user (executor) has already bid on this task
    $has_bid = false;
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'executor') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bids WHERE task_id = ? AND executor_id = ?");
        $stmt->execute([$task_id, $_SESSION['user_id']]);
        $has_bid = ($stmt->fetchColumn() > 0);
    }

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
    <link rel="stylesheet" href="./style/bids.css">
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

            <!-- Action Card for Executors -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'executor' && $task['status'] === 'posted'): ?>
                <div class="card action-card">
                    <h3><i class="fas fa-handshake"></i> Interested in this task?</h3>
                    
                    <?php if (isset($has_bid) && $has_bid): ?>
                        <div class="bid-submitted-message">
                            <i class="fas fa-check-circle"></i>
                            <p>You have already submitted a bid for this task. The client will review your proposal.</p>
                        </div>
                    <?php else: ?>
                        <div class="bid-form">
                            <form id="bidForm">
                                <input type="hidden" id="task_id" value="<?php echo $task_id; ?>">
                                
                                <div class="form-group">
                                    <label for="bid_amount">Your Bid Amount ($)</label>
                                    <input type="number" id="bid_amount" min="1" step="0.01" required 
                                           placeholder="Enter your bid amount" value="<?php echo htmlspecialchars($task['budget']); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="proposal_text">Proposal Message (Optional)</label>
                                    <textarea id="proposal_text" placeholder="Describe why you're a good fit for this task..." rows="4"></textarea>
                                </div>
                                
                                <div class="action-buttons">
                                    <button type="submit" class="btn-primary">
                                        <i class="fas fa-gavel"></i> Submit Bid
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Payment Card for Clients -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'client' && $task['status'] === 'in_progress' && $task['client_id'] == $_SESSION['user_id']): ?>
                <div class="card payment-card">
                    <h3><i class="fas fa-credit-card"></i> Task Completed?</h3>
                    <p>If the executor has completed this task to your satisfaction, you can process the payment.</p>
                    
                    <div class="payment-details">
                        <div class="payment-info">
                            <div class="info-item">
                                <span class="label">Executor:</span>
                                <span class="value"><?php echo htmlspecialchars($task['executor_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Amount:</span>
                                <span class="value">$<?php echo number_format($task['bid_amount'] ? $task['bid_amount'] : $task['budget'], 2); ?></span>
                            </div>
                        </div>
                        
                        <button id="paymentButton" class="btn-primary" onclick="processPayment(<?php echo $task_id; ?>)">
                            <i class="fas fa-check-circle"></i> Confirm Completion & Pay
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Cancel Task Card for Executors -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'executor' && $task['status'] === 'in_progress' && $task['executor_id'] == $_SESSION['user_id']): ?>
                <div class="card cancel-card">
                    <h3><i class="fas fa-times-circle"></i> Need to Cancel?</h3>
                    <p>If you can't complete this task, you can cancel your assignment. The client will be able to accept other bids.</p>
                    
                    <div class="cancel-form">
                        <div class="form-group">
                            <label for="cancel_reason">Reason for Cancellation (Optional)</label>
                            <textarea id="cancel_reason" placeholder="Please explain why you need to cancel this task..." rows="3"></textarea>
                        </div>
                        
                        <button id="cancelButton" class="btn-danger" onclick="cancelTask(<?php echo $task_id; ?>)">
                            <i class="fas fa-times"></i> Cancel Task Assignment
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Bids Section - Only visible to clients -->
            <?php if (isset($_SESSION['user_id']) && !empty($bids) && $_SESSION['role'] === 'client'): ?>
                <div class="card bids-card">
                    <h3><i class="fas fa-gavel"></i> <?php echo ($_SESSION['user_id'] == $task['client_id']) ? 'Bids for Your Task' : 'Bids for This Task'; ?></h3>
                    <p>
                        <?php if ($_SESSION['user_id'] == $task['client_id']): ?>
                            Review and select the best offer for your task
                        <?php else: ?>
                            Current bids submitted for this task
                        <?php endif; ?>
                    </p>
                    
                    <div class="bids-list">
                        <?php foreach ($bids as $bid): ?>
                            <div class="bid-item">
                                <div class="bid-header">
                                    <div class="bidder-info">
                                        <div class="bidder-avatar">
                                            <?php if ($bid['executor_image']): ?>
                                                <img src="<?php echo htmlspecialchars($bid['executor_image']); ?>" alt="Executor Profile">
                                            <?php else: ?>
                                                <i class="fas fa-user-tie"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h4><?php echo htmlspecialchars($bid['executor_name']); ?></h4>
                                            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($bid['executor_email']); ?></p>
                                            <p><i class="fas fa-calendar"></i> Member since <?php echo date('M Y', strtotime($bid['executor_member_since'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="bid-amount">
                                        <span class="amount">$<?php echo number_format($bid['bid_amount'], 2); ?></span>
                                        <span class="bid-date">Bid on <?php echo date('M j, Y', strtotime($bid['bid_date'])); ?></span>
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
                                                <i class="fas fa-check"></i> Accept Bid
                                            </button>
                                            <button class="btn-message" onclick="openMessageModalWithRecipient(<?php echo $bid['executor_id']; ?>)">
                                                <i class="fas fa-comment"></i> Message
                                            </button>
                                        <?php else: ?>
                                            <div class="bid-status pending">
                                                <i class="fas fa-clock"></i> Pending
                                            </div>
                                            <?php if ($_SESSION['role'] === 'executor' && $_SESSION['user_id'] == $bid['executor_id']): ?>
                                                <div class="bid-note">Your bid is awaiting client review</div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php elseif ($bid['status'] === 'accepted'): ?>
                                        <div class="bid-status accepted">
                                            <i class="fas fa-check-circle"></i> Accepted
                                        </div>
                                    <?php elseif ($bid['status'] === 'done'): ?>
                                        <div class="bid-status done">
                                            <i class="fas fa-check-double"></i> Done
                                        </div>
                                    <?php elseif ($bid['status'] === 'rejected' && $task['status'] !== 'posted'): ?>
                                        <div class="bid-status rejected">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </div>
                                    <?php elseif ($bid['status'] === 'cancelled'): ?>
                                        <div class="bid-status cancelled">
                                            <i class="fas fa-ban"></i> Cancelled
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Action Card for Executors -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'executor' && $task['status'] === 'posted'): ?>
                <div class="card action-card">
                    <h3><i class="fas fa-gavel"></i> Submit a Bid</h3>
                    
                    <?php if ($has_bid): ?>
                        <div class="bid-submitted-message">
                            <i class="fas fa-check-circle"></i>
                            <p>You have already submitted a bid for this task. The client will review your proposal.</p>
                        </div>
                    <?php else: ?>
                        <p>Interested in this task? Submit your bid now!</p>
                        <form id="bidForm" class="bid-form">
                            <div class="form-group">
                                <label for="bidAmount">Your Bid Amount ($)</label>
                                <input type="number" id="bidAmount" name="bidAmount" min="1" step="0.01" value="<?php echo $task['budget']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="proposalText">Proposal (Optional)</label>
                                <textarea id="proposalText" name="proposalText" placeholder="Describe why you're the best person for this task..."></textarea>
                            </div>
                            <div class="action-buttons">
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Bid
                                </button>
                                <button type="button" class="btn-message" onclick="openMessageModal()">
                                    <i class="fas fa-comment"></i> Message Client
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Message Modal -->
    <div id="messageModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-comment"></i> Send Message</h2>
                <button class="modal-close" onclick="closeMessageModal()">×</button>
            </div>
            <form id="messageForm">
                <input type="hidden" id="recipient_id" name="recipient_id" value="<?php echo $task['client_id']; ?>">
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
            // Set the recipient to the client by default
            document.getElementById('recipient_id').value = <?php echo $task['client_id']; ?>;
        }
        
        function openMessageModalWithRecipient(recipientId) {
            document.getElementById('messageModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            // Set the specific recipient
            document.getElementById('recipient_id').value = recipientId;
        }

        function closeMessageModal() {
            document.getElementById('messageModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Bid Form Submission
        if (document.getElementById('bidForm')) {
            document.getElementById('bidForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const taskId = document.getElementById('task_id').value;
                const bidAmount = document.getElementById('bid_amount').value;
                const proposalText = document.getElementById('proposal_text').value;
                
                fetch('api/accept_task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        bid_amount: bidAmount,
                        proposal_text: proposalText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Your bid has been submitted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting your bid');
                });
            });
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
        
        // Process Payment Function
        function processPayment(taskId) {
            if (confirm('Are you sure you want to confirm task completion and process payment? This action cannot be undone.')) {
                fetch('api/process_payment.php', {
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
                        // Show payment success modal
                        alert('Payment processed successfully! The task has been marked as completed.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing the payment');
                });
            }
        }
        
        // Cancel Task Function
        function cancelTask(taskId) {
            if (confirm('Are you sure you want to cancel your assignment for this task? This will allow the client to select another executor.')) {
                const reason = document.getElementById('cancel_reason').value;
                
                fetch('api/cancel_task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        reason: reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Task assignment cancelled successfully. The client can now select another executor.');
                        window.location.href = 'my_assignments.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while cancelling the task');
                });
            }
        }
    </script>

    <?php include 'components/footer.php'; ?>
</body>

</html>