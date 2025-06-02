<?php
require_once __DIR__ . '/../config/db.php';
?>
<!-- Create Task Modal -->
<div class="modal-overlay" id="createTaskModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Create New Task</h2>
            <button class="modal-close" onclick="closeCreateTaskModal()">×</button>
        </div>
        <div class="modal-body">
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?php 
                        echo htmlspecialchars($_SESSION['error_message']); 
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>
            <form method="POST" id="createTaskForm" action="api/create_task.php" enctype="multipart/form-data" novalidate>
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
                        // Fetch categories from database
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
                        <input type="file" id="taskImage" name="image" class="form-control" accept="image/*" required onchange="previewImage(this)">
                        <div id="imagePreview" class="image-preview">
                            <img src="" alt="Preview" style="display: none;">
                            <span class="placeholder">No image selected</span>
                        </div>
                    </div>
                    <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
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
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeCreateTaskModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary" form="createTaskForm" onclick="document.getElementById('createTaskForm').submit();">
                <i class="fas fa-plus"></i> Create Task
            </button>
        </div>
    </div>
</div>