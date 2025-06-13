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
            <h2><i class="fas fa-user-edit"></i> <?php echo __('edit_profile'); ?></h2>
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
            
            <form id="editProfileForm" method="POST" action="api/update_profile.php" enctype="multipart/form-data" novalidate>
                <div class="form-group">
                    <label for="name"><?php echo __('name'); ?></label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" 
                           placeholder="<?php echo __('enter_your_full_name'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email"><?php echo __('email'); ?></label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                           placeholder="<?php echo __('enter_your_email'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="bio"><?php echo __('biography'); ?></label>
                    <textarea id="bio" name="bio" class="form-control" 
                              rows="4" placeholder="<?php echo __('tell_us_about_yourself'); ?>"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="profile_picture"><?php echo __('profile_picture'); ?></label>
                    <div class="image-upload-container">
                        <input type="file" id="profile_picture" name="profile_picture" class="form-control" 
                               accept="image/*" onchange="previewProfileImage(this)">
                        <div id="profileImagePreview" class="image-preview">
                            <?php 
                            if (!empty($user['profile_picture'])) {
                                $preview_pic = $user['profile_picture'];
                                if (strpos($preview_pic, 'http') !== 0 && strpos($preview_pic, '/') !== 0) {
                                    $preview_pic = '/' . ltrim($preview_pic, '/');
                                }
                                echo '<img src="' . htmlspecialchars($preview_pic) . '?t=' . time() . '" alt="' . __('current_profile') . '" onerror="this.onerror=null; this.parentElement.innerHTML=\'<span class=\'placeholder\'>' . __('no_image_selected') . '</span>\'">';
                            } else {
                                echo '<span class="placeholder">' . __('no_image_selected') . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                    <small class="text-muted"><?php echo __('accepted_formats'); ?>: JPG, PNG, GIF. <?php echo __('max_size'); ?>: 5MB</small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeEditProfileModal()">
                <i class="fas fa-times"></i> <?php echo __('cancel'); ?>
            </button>
            <button type="button" class="btn btn-primary" id="saveProfileBtn" onclick="saveProfile()">
                <i class="fas fa-save"></i> <?php echo __('save_changes'); ?>
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

// Save profile function
function saveProfile() {
    const form = document.getElementById('editProfileForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('saveProfileBtn');
    const originalBtnText = saveBtn.innerHTML;
    
    // Show loading state
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    // Clear previous messages
    const messageDiv = form.querySelector('.alert');
    if (messageDiv) {
        messageDiv.remove();
    }
    
    // Submit form via AJAX
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Profile updated successfully!', 'success');
            // Update profile picture in the main page if it was changed
            if (data.profile_picture) {
                const profileImg = document.querySelector('.profile-picture img');
                if (profileImg) {
                    profileImg.src = data.profile_picture + '?t=' + new Date().getTime();
                }
            }
            // Close modal after 1.5 seconds
            setTimeout(closeEditProfileModal, 1500);
        } else {
            showMessage(data.message || 'Failed to update profile. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalBtnText;
    });
}

// Show message in modal
function showMessage(message, type) {
    let messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type}`;
    messageDiv.textContent = message;
    
    const form = document.getElementById('editProfileForm');
    form.prepend(messageDiv);
    
    // Scroll to top to show the message
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
