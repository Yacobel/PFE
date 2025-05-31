<?php
$categories_query = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categories_query->fetchAll();
?>

<!-- Edit Task Modal -->
<div class="modal-overlay" id="editTaskModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Task</h2>
            <button class="modal-close" onclick="closeEditTaskModal()">×</button>
        </div>
        <form id="editTaskForm" enctype="multipart/form-data">
            <input type="hidden" id="edit_task_id" name="task_id">

            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_title">Task Title</label>
                    <input type="text" id="edit_title" name="title" class="form-control" placeholder="Enter a clear title for your task" required>
                </div>

                <div class="form-group">
                    <label for="edit_description">Task Description</label>
                    <textarea id="edit_description" name="description" class="form-control" placeholder="Describe your task in detail..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_category">Select Category</label>
                    <select id="edit_category" name="category_id" class="form-control" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_image">Task Image</label>
                    <div class="image-upload-container">
                        <input type="file" id="edit_image" name="image" class="form-control" accept="image/*">
                        <div id="current_image_preview" class="image-preview">
                            <img src="" alt="Preview" style="display: none;">
                            <span class="placeholder">Current image will be shown here</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_budget">Budget ($)</label>
                    <input type="number" id="edit_budget" name="budget" class="form-control" placeholder="Enter your budget" min="1" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="edit_deadline">Deadline</label>
                    <input type="date" id="edit_deadline" name="deadline" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_location">Location (Optional)</label>
                    <input type="text" id="edit_location" name="location" class="form-control" placeholder="Enter task location">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="confirmDeleteTask()">Delete Task</button>
                <div class="modal-footer-right">
                    <button type="button" class="btn btn-secondary" onclick="closeEditTaskModal()">Cancel</button>
                    <button type="submit" class="btn">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteConfirmModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-trash-alt"></i> Confirm Delete</h2>
            <button class="modal-close" onclick="closeDeleteConfirmModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to delete this task? This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeDeleteConfirmModal()" class="btn btn-secondary">Cancel</button>
            <button onclick="deleteTask()" class="btn btn-danger">Delete Task</button>
        </div>
    </div>
</div>

<style>
    .modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modal-footer-right {
        display: flex;
        gap: 0.5rem;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #dc3545;
    }

    .alert i {
        font-size: 1.5rem;
    }
</style>