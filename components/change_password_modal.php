<!-- Change Password Modal -->
<div class="modal-overlay" id="changePasswordModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-key"></i> Change Password</h2>
            <button class="modal-close" onclick="closeChangePasswordModal()">×</button>
        </div>
        <div class="modal-body">
            <div id="changePasswordMessage" class="alert d-none"></div>
            
            <form id="changePasswordForm" method="POST" action="api/change_password.php" novalidate>
                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <div class="input-group">
                        <input type="password" id="currentPassword" name="current_password" class="form-control" 
                               placeholder="Enter your current password" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <div class="input-group">
                        <input type="password" id="newPassword" name="new_password" class="form-control" 
                               placeholder="Enter your new password" required
                               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">
                        Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <div class="input-group">
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" 
                               placeholder="Confirm your new password" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div id="passwordMatch" class="invalid-feedback">
                        Passwords do not match.
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeChangePasswordModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary" id="changePasswordBtn" onclick="submitChangePassword()">
                <i class="fas fa-key"></i> Change Password
            </button>
        </div>
    </div>
</div>

<script>
// Submit change password form
function submitChangePassword() {
    const form = document.getElementById('changePasswordForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('changePasswordBtn');
    const messageDiv = document.getElementById('changePasswordMessage');
    const originalBtnText = submitBtn.innerHTML;
    
    // Validate passwords match
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        messageDiv.textContent = 'Passwords do not match.';
        messageDiv.className = 'alert alert-error';
        messageDiv.classList.remove('d-none');
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    messageDiv.className = 'alert d-none';
    
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
            messageDiv.textContent = data.message || 'Password changed successfully!';
            messageDiv.className = 'alert alert-success';
            // Clear form and close modal after 1.5 seconds
            form.reset();
            setTimeout(closeChangePasswordModal, 1500);
        } else {
            messageDiv.textContent = data.message || 'Failed to change password. Please try again.';
            messageDiv.className = 'alert alert-error';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.textContent = 'An error occurred. Please try again.';
        messageDiv.className = 'alert alert-error';
    })
    .finally(() => {
        messageDiv.classList.remove('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        // Scroll to show the message
        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
}

// Toggle password visibility
function setupPasswordToggle() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
}

// Modal functions
function openChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.style.display = "flex";
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
        setupPasswordToggle();
    }
}

function closeChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.classList.remove("active");
        setTimeout(() => {
            modal.style.display = "none";
            document.getElementById('changePasswordForm').reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('changePasswordMessage').classList.add('d-none');
        }, 300);
        document.body.style.overflow = "auto";
    }
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('changePasswordModal');
        if (event.target === modal) {
            closeChangePasswordModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        const modal = document.getElementById('changePasswordModal');
        if (event.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeChangePasswordModal();
        }
    });
});
</script>
