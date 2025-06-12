<?php
require_once __DIR__ . '/../config/db.php';

// Fetch user data if not already available
if (!isset($user) && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>
<!-- Edit Profile Modal -->
<div class="modal-overlay" id="editProfileModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
            <button class="modal-close" onclick="closeEditProfileModal()">×</button>
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
            
            <form id="editProfileForm" enctype="multipart/form-data" onsubmit="return false;">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    <div class="image-upload-container">
                        <input type="file" id="profile_picture" name="profile_picture" class="form-control" 
                               accept="image/*" onchange="previewProfileImage(this)">
                        <div id="profileImagePreview" class="image-preview">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Current Profile Picture">
                            <?php else: ?>
                                <span class="placeholder">No image selected</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeEditProfileModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<script>
// Profile image preview
function previewProfileImage(input) {
    const preview = document.querySelector("#profileImagePreview");
    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        preview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview">`;
    }

    if (file) {
        reader.readAsDataURL(file);
    }
}

// Modal functions
function openEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    if (modal) {
        modal.style.display = "flex";
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
    }
}

function closeEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    if (modal) {
        modal.classList.remove("active");
        setTimeout(() => {
            modal.style.display = "none";
        }, 300);
        document.body.style.overflow = "auto";
    }
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('editProfileModal');
    if (event.target === modal) {
        closeEditProfileModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('editProfileModal');
    if (event.key === 'Escape' && modal && modal.style.display === 'flex') {
        closeEditProfileModal();
    }
});
</script>
