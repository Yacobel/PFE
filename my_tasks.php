<?php
require_once 'config/languages.php';
require_once 'config/db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$pageTitle = __("my_tasks");
include 'components/head.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./style/my_tasks.css">
    
</head>

<body>
    <!-- Language Switcher -->
    <div class="language-selector">
        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
        <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>

    <div class="container">
        <?php include 'components/header.php'; ?>

        <div class="tasks-container">
            <div class="tasks-header">
                <h1><?php echo __("my_tasks"); ?></h1>
                <p><?php echo __("manage_tasks"); ?></p>
                <div class="user-actions">
                    <button onclick="openCreateTaskModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <?php echo __("create_new_task"); ?>
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> <?php echo __("back_to_dashboard"); ?>
                    </a>
                </div>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo htmlspecialchars($_SESSION['success_message']); 
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?php 
                        echo htmlspecialchars($_SESSION['error_message']); 
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php
            // Fetch tasks for the current client
            $stmt = $pdo->prepare("
                SELECT t.*, c.name as category_name 
                FROM tasks t 
                LEFT JOIN categories c ON t.category_id = c.category_id 
                WHERE t.client_id = ? 
                AND (t.payment_status IS NULL OR t.payment_status != 'paid')
                ORDER BY t.created_at DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $tasks = $stmt->fetchAll();

            if (count($tasks) > 0) {
                echo '<div class="task-grid">';
                foreach ($tasks as $task) {
            ?>
                    <div class="task-card">
                        <div class="card-image-container">
                            <img src="<?php echo htmlspecialchars($task['image_url']); ?>" alt="<?php echo __("task_image"); ?>" class="card-image">
                            <div class="card-overlay"></div>
                        </div>
                        <div class="card-content">
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h3>
                                <span class="card-category">
                                    <i class="fas fa-tag"></i>
                                    <?php echo htmlspecialchars($task['category_name']); ?>
                                </span>
                                <span class="card-status"><?php echo __($task['status']); ?></span>
                            </div>

                            <div class="card-details">
                                <span><i class="fas fa-dollar-sign"></i> <?php echo __("budget"); ?>: $<?php echo number_format($task['budget'], 2); ?></span>
                                <span><i class="fas fa-calendar"></i> <?php echo __("deadline"); ?>: <?php echo date('M d, Y', strtotime($task['deadline'])); ?></span>
                                <?php if ($task['location']) { ?>
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($task['location']); ?></span>
                                <?php } ?>
                            </div>

                            <div class="card-actions">
                                <?php if ($task['status'] === 'completed'): ?>
                                <!-- Payment button for completed tasks -->
                                <a href="process_payment.php?task_id=<?php echo $task['task_id']; ?>" class="btn btn-success">
                                    <i class="fas fa-credit-card"></i> <?php echo __("pay_executor"); ?>
                                </a>
                                <?php else: ?>
                                <!-- Edit button for non-completed tasks -->
                                <button onclick="openEditTaskModal(<?php echo htmlspecialchars(json_encode([
                                                                        'id' => $task['task_id'],
                                                                        'title' => $task['title'],
                                                                        'description' => $task['description'],
                                                                        'category_id' => $task['category_id'],
                                                                        'budget' => $task['budget'],
                                                                        'deadline' => $task['deadline'],
                                                                        'location' => $task['location'],
                                                                        'image_url' => $task['image_url']
                                                                    ])); ?>)" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i> <?php echo __("edit_task"); ?>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php
                }
                echo '</div>';
            } else {
                ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard"></i>
                    <h2><?php echo __("no_tasks"); ?></h2>
                    <p><?php echo __("no_tasks_message"); ?></p>
                </div>
            <?php
            }
            ?>
        </div>

        <!-- Edit Task Modal -->
        <div id="editTaskModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h2><i class="fas fa-edit"></i> <?php echo __("edit_task"); ?></h2>
                    <button class="modal-close" onclick="closeEditTaskModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit_task_id" name="task_id">

                        <div class="form-group">
                            <label for="edit_title"><?php echo __("task_title"); ?></label>
                            <input type="text" id="edit_title" name="title" class="form-control" placeholder="<?php echo __("enter_task_title"); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_description"><?php echo __("description"); ?></label>
                            <textarea id="edit_description" name="description" class="form-control" rows="4" placeholder="<?php echo __("describe_task"); ?>" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit_category"><?php echo __("category"); ?></label>
                            <select id="edit_category" name="category_id" class="form-control" required>
                                <?php
                                $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
                                foreach ($categories as $category) {
                                    echo "<option value=\"" . $category['category_id'] . "\">" . htmlspecialchars($category['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_budget"><?php echo __("budget"); ?> ($)</label>
                            <input type="number" id="edit_budget" name="budget" class="form-control" min="1" step="0.01" placeholder="<?php echo __("enter_budget"); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_deadline"><?php echo __("deadline"); ?></label>
                            <input type="date" id="edit_deadline" name="deadline" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_location"><?php echo __("location"); ?> (<?php echo __("optional"); ?>)</label>
                            <input type="text" id="edit_location" name="location" class="form-control" placeholder="<?php echo __("enter_location"); ?>">
                        </div>

                        <div class="form-group">
                            <label for="edit_image"><?php echo __("task_image"); ?></label>
                            <div class="image-upload-container">
                                <input type="file" id="edit_image" name="image" class="form-control" accept="image/*">
                                <div id="current_image_preview" class="image-preview">
                                    <span class="placeholder"><?php echo __("current_image_placeholder"); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo __("save_changes"); ?>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="closeEditTaskModal()">
                                <i class="fas fa-times"></i> <?php echo __("cancel"); ?>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteTask(document.getElementById('edit_task_id').value)">
                                <i class="fas fa-trash"></i> <?php echo __("delete_task"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div id="paymentModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h2><i class="fas fa-credit-card"></i> <?php echo __("payment"); ?></h2>
                    <button class="modal-close" onclick="closePaymentModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" method="POST" action="process_payment.php" onsubmit="return redirectToPayment(event)">
                        <input type="hidden" id="payment_task_id" name="task_id">
                        
                        <div class="payment-details">
                            <h3 id="payment_task_title"><?php echo __("task_title"); ?></h3>
                            <div class="payment-amount">
                                <span><?php echo __("amount_to_pay"); ?>:</span>
                                <span id="payment_amount" class="amount">$0.00</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="card_number"><?php echo __("card_number"); ?></label>
                            <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label for="expiry_date"><?php echo __("expiry_date"); ?></label>
                                <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" required>
                            </div>
                            <div class="form-group half">
                                <label for="cvv"><?php echo __("cvv"); ?></label>
                                <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="card_holder"><?php echo __("card_holder"); ?></label>
                            <input type="text" id="card_holder" name="card_holder" class="form-control" placeholder="<?php echo __("card_holder_placeholder"); ?>" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> <?php echo __("confirm_payment"); ?>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">
                                <i class="fas fa-times"></i> <?php echo __("cancel"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Include the Create Task Modal -->
        <?php include 'components/create_task_modal.php'; ?>
    </div>

    <script src="js/main.js"></script>
    <script src="js/my_tasks.js"></script>
</body>

</html>