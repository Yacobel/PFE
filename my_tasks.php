<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$pageTitle = "My Tasks";
include 'components/head.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./style/my_tasks.css">
</head>

<body>
    <div class="container">
        <?php include 'components/header.php'; ?>

        <div class="tasks-container">
            <div class="tasks-header">
                <h1>My Tasks</h1>
                <p>Manage and track all your posted tasks</p>
                <div class="user-actions">
                    <button onclick="openCreateTaskModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Task
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <?php
            // Fetch tasks for the current client
            $stmt = $pdo->prepare("
                SELECT t.*, c.name as category_name 
                FROM tasks t 
                LEFT JOIN categories c ON t.category_id = c.category_id 
                WHERE t.client_id = ? 
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
                            <img src="<?php echo htmlspecialchars($task['image_url']); ?>" alt="Task Image" class="card-image">
                            <div class="card-overlay"></div>
                        </div>
                        <div class="card-content">
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h3>
                                <span class="card-category">
                                    <i class="fas fa-tag"></i>
                                    <?php echo htmlspecialchars($task['category_name']); ?>
                                </span>
                                <span class="card-status"><?php echo ucfirst($task['status']); ?></span>
                            </div>

                            <div class="card-details">
                                <span><i class="fas fa-dollar-sign"></i> Budget: $<?php echo number_format($task['budget'], 2); ?></span>
                                <span><i class="fas fa-calendar"></i> Deadline: <?php echo date('M d, Y', strtotime($task['deadline'])); ?></span>
                                <?php if ($task['location']) { ?>
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($task['location']); ?></span>
                                <?php } ?>
                            </div>

                            <div class="card-actions">
                                <?php if ($task['status'] === 'completed'): ?>
                                <!-- Payment button for completed tasks -->
                                <button onclick="openPaymentModal(<?php echo $task['task_id']; ?>, '<?php echo htmlspecialchars($task['title']); ?>', <?php echo $task['budget']; ?>)" class="btn btn-success">
                                    <i class="fas fa-credit-card"></i> Pay Executor
                                </button>
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
                                    <i class="fas fa-edit"></i> Edit Task
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
                    <h2>No Tasks Yet</h2>
                    <p>You haven't created any tasks yet.</p>
                </div>
            <?php
            }
            ?>
        </div>

        <!-- Edit Task Modal -->
        <div id="editTaskModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h2><i class="fas fa-edit"></i> Edit Task</h2>
                    <button class="modal-close" onclick="closeEditTaskModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit_task_id" name="task_id">

                        <div class="form-group">
                            <label for="edit_title">Task Title</label>
                            <input type="text" id="edit_title" name="title" class="form-control" placeholder="Enter a clear title for your task" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea id="edit_description" name="description" class="form-control" rows="4" placeholder="Describe your task in detail..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit_category">Category</label>
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
                            <label for="edit_budget">Budget ($)</label>
                            <input type="number" id="edit_budget" name="budget" class="form-control" min="1" step="0.01" placeholder="Enter your budget" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_deadline">Deadline</label>
                            <input type="date" id="edit_deadline" name="deadline" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_location">Location (Optional)</label>
                            <input type="text" id="edit_location" name="location" class="form-control" placeholder="Enter task location">
                        </div>

                        <div class="form-group">
                            <label for="edit_image">Task Image</label>
                            <div class="image-upload-container">
                                <input type="file" id="edit_image" name="image" class="form-control" accept="image/*">
                                <div id="current_image_preview" class="image-preview">
                                    <span class="placeholder">Current image will be shown here</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="closeEditTaskModal()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteTask(document.getElementById('edit_task_id').value)">
                                <i class="fas fa-trash"></i> Delete Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Include the Create Task Modal -->
        <?php include 'components/create_task_modal.php'; ?>
        
        <!-- Payment Modal -->
        <div id="paymentModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h2><i class="fas fa-credit-card"></i> Payment</h2>
                    <button class="modal-close" onclick="closePaymentModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" method="POST" action="process_payment.php">
                        <input type="hidden" id="payment_task_id" name="task_id">
                        
                        <div class="payment-details">
                            <h3 id="payment_task_title">Task Title</h3>
                            <div class="payment-amount">
                                <span>Amount to pay:</span>
                                <span id="payment_amount" class="amount">$0.00</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" required>
                            </div>
                            <div class="form-group half">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="card_holder">Card Holder Name</label>
                            <input type="text" id="card_holder" name="card_holder" class="form-control" placeholder="John Doe" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Confirm Payment
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Create Task Modal -->
        <?php include 'components/create_task_modal.php'; ?>


    </div>

    <script src="js/main.js"></script>
    <script>
        // Image Preview Function
        function previewImage(input) {
            const preview = input.parentElement.querySelector('.image-preview');
            const img = preview.querySelector('img');
            const placeholder = preview.querySelector('.placeholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                img.src = '';
                img.style.display = 'none';
                placeholder.style.display = 'block';
            }
        }

        // Create Task Modal Functions
        function openCreateTaskModal() {
            const modal = document.getElementById('createTaskModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // Reset form and clear any previous errors
                const form = document.getElementById('createTaskForm');
                if (form) {
                    form.reset();
                    const errors = form.querySelectorAll('.alert-error');
                    errors.forEach(error => error.remove());
                }
            }
        }

        function closeCreateTaskModal() {
            const modal = document.getElementById('createTaskModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        // Form Submission Handler
        // Wait for DOM to be fully loaded before attaching event handlers
        window.addEventListener('load', function() {
            console.log('DOM fully loaded');
            const createTaskForm = document.getElementById('createTaskForm');
            console.log('Form found:', createTaskForm);
            
            if (createTaskForm) {
                createTaskForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Remove any existing error messages
                    const existingErrors = document.querySelectorAll('.alert-error');
                    existingErrors.forEach(error => error.remove());

                    // Show loading state
                    const submitBtn = document.querySelector('button[form="createTaskForm"]');
                    if (!submitBtn) {
                        console.error('Submit button not found');
                        return;
                    }

                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                    submitBtn.disabled = true;

                    try {
                        // Validate form data
                        const formData = new FormData(this);
                        const requiredFields = ['title', 'description', 'category_id', 'budget', 'deadline'];
                        const missingFields = [];

                        for (const field of requiredFields) {
                            if (!formData.get(field) || formData.get(field).trim() === '') {
                                missingFields.push(field);
                            }
                        }

                        if (missingFields.length > 0) {
                            throw new Error('Please fill in all required fields: ' + missingFields.join(', '));
                        }

                        // Check if image is selected
                        const imageInput = document.getElementById('taskImage');
                        if (!imageInput || !imageInput.files || !imageInput.files[0]) {
                            throw new Error('Please select an image for the task');
                        }

                        console.log('Submitting form data:', Object.fromEntries(formData));
                        
                        const response = await fetch('api/create_task.php', {
                            method: 'POST',
                            body: formData
                        });

                        const text = await response.text();
                        console.log('Server response:', text);

                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.error('Failed to parse response:', text);
                            throw new Error('Invalid server response');
                        }

                        if (data.success) {
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Error creating task');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'alert alert-error';
                        errorDiv.textContent = error.message || 'An error occurred. Please try again.';
                        
                        const modalBody = document.querySelector('.modal-body');
                        if (modalBody) {
                            modalBody.insertBefore(errorDiv, modalBody.firstChild);
                        }
                    } finally {
                        // Restore button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                });
            } else {
                console.error('Create task form not found');
            }
            
            // Also attach click handler to the submit button as a backup
            const submitBtn = document.querySelector('button[form="createTaskForm"]');
            if (submitBtn) {
                console.log('Submit button found');
                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Submit button clicked');
                    const form = document.getElementById('createTaskForm');
                    if (form) {
                        // Trigger the form submission
                        const submitEvent = new Event('submit', { cancelable: true });
                        form.dispatchEvent(submitEvent);
                    }
                });
            } else {
                console.error('Submit button not found');
            }
        });

        // Payment Modal Functions
        function openPaymentModal(taskId, taskTitle, budget) {
            document.getElementById('payment_task_id').value = taskId;
            document.getElementById('payment_task_title').textContent = taskTitle;
            document.getElementById('payment_amount').textContent = '$' + budget.toFixed(2);
            
            document.getElementById('paymentModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            
            // Reset form
            document.getElementById('paymentForm').reset();
        }
        
        // Edit Task Modal Functions
        function openEditTaskModal(taskData) {
            document.getElementById('edit_task_id').value = taskData.id;
            document.getElementById('edit_title').value = taskData.title;
            document.getElementById('edit_description').value = taskData.description;
            document.getElementById('edit_category').value = taskData.category_id;
            document.getElementById('edit_budget').value = taskData.budget;
            document.getElementById('edit_deadline').value = taskData.deadline;
            document.getElementById('edit_location').value = taskData.location || '';

            // Show current image preview
            const imagePreview = document.getElementById('current_image_preview');
            if (taskData.image_url) {
                imagePreview.innerHTML = `
                    <img src="${taskData.image_url}" alt="Current task image" style="max-width: 100%; max-height: 200px;">
                `;
            } else {
                imagePreview.innerHTML = `<span class="placeholder">No image currently set</span>`;
            }

            document.getElementById('editTaskModal').classList.add('active');
        }

        function closeEditTaskModal() {
            document.getElementById('editTaskModal').classList.remove('active');
        }

        // Handle form submission
        document.getElementById('editTaskForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('api/update_task.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error updating task: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating task. Please try again.');
                });
        });

        // Preview image when a new one is selected
        document.getElementById('edit_image').addEventListener('change', function(e) {
            const preview = document.getElementById('current_image_preview');
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px;">
                    `;
                }
                reader.readAsDataURL(file);
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                if (event.target.id === 'editTaskModal') {
                    closeEditTaskModal();
                }
            }
        }

        // Add this new function for task deletion
        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
                fetch('api/delete_task.php?id=' + taskId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error deleting task: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting task. Please try again.');
                    });
            }
        }
    </script>
</body>

</html>