<!-- Change Password Modal -->
<div class="modal-overlay" id="changePasswordModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-key"></i> <?php echo __('change_password'); ?></h2>
            <button type="button" class="modal-close" id="closeChangePasswordModal" aria-label="<?php echo __('close'); ?>">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="changePasswordMessage" class="alert d-none"></div>
            
            <form id="changePasswordForm" method="POST" action="api/change_password.php" novalidate>
                <div class="form-group">
                    <label for="currentPassword"><?php echo __('current_password'); ?></label>
                    <div class="input-group">
                        <input type="password" id="currentPassword" name="current_password" class="form-control" 
                               placeholder="<?php echo __('enter_current_password'); ?>" required
                               autocomplete="current-password">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#currentPassword"
                                    aria-label="<?php echo __('toggle_password_visibility'); ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="error-message text-danger small mt-1" id="currentPasswordError"></div>
                </div>
                
                <div class="form-group">
                    <label for="newPassword"><?php echo __('new_password'); ?></label>
                    <div class="input-group">
                        <input type="password" id="newPassword" name="new_password" class="form-control" 
                               placeholder="<?php echo __('enter_new_password'); ?>" required
                               minlength="8"
                               autocomplete="new-password">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#newPassword"
                                    aria-label="<?php echo __('toggle_password_visibility'); ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">
                        <div class="password-requirement">
                            <i class="fas fa-info-circle"></i> Password must be at least 8 characters long
                        </div>
                    </small>
                    <div class="error-message text-danger small mt-1" id="newPasswordError"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword"><?php echo __('confirm_new_password'); ?></label>
                    <div class="input-group">
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" 
                               placeholder="<?php echo __('confirm_new_password'); ?>" required
                               autocomplete="new-password">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#confirmPassword"
                                    aria-label="<?php echo __('toggle_password_visibility'); ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="error-message text-danger small mt-1" id="confirmPasswordError"></div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelChangePassword">
                        <i class="fas fa-times"></i> <?php echo __('cancel'); ?>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo __('save_changes'); ?>
                    </button>
                </div>
            </form>
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
    const messageText = messageDiv.querySelector('.message');
    const originalBtnText = submitBtn.innerHTML;
    
    // Clear previous messages and errors
    messageText.textContent = '';
    messageDiv.className = 'alert d-none';
    document.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('has-error');
    });
    
    // Get form values
    const currentPassword = document.getElementById('currentPassword').value.trim();
    const newPassword = document.getElementById('newPassword').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();
    
    // Client-side validation
    let isValid = true;
    
    if (!currentPassword) {
        showFieldError('currentPassword', '<?php echo __('current_password_required'); ?>');
        isValid = false;
    }
    
    if (!newPassword) {
        showFieldError('newPassword', '<?php echo __('new_password_required'); ?>');
        isValid = false;
    } else if (newPassword.length < 8) {
        showFieldError('newPassword', '<?php echo __('password_min_length'); ?>');
        isValid = false;
    } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(newPassword)) {
        showFieldError('newPassword', '<?php echo __('password_complexity'); ?>');
        isValid = false;
    }
    
    if (newPassword !== confirmPassword) {
        showFieldError('confirmPassword', '<?php echo __('passwords_do_not_match'); ?>');
        isValid = false;
    }
    
    if (!isValid) {
        return false;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php echo __('updating'); ?>...';
    
    // Submit form via AJAX
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        const data = await response.json();
        
        if (!response.ok) {
            // Handle HTTP errors (4xx, 5xx)
            const error = new Error(data.message || '<?php echo __('failed_to_change_password'); ?>');
            error.response = data;
            error.status = response.status;
            throw error;
        }
        
        return data;
    })
    .then(data => {
        if (data.success) {
            // Success case
            showMessage('success', data.message || '<?php echo __('password_changed_successfully'); ?>');
            
            // Clear form and close modal after 1.5 seconds
            form.reset();
            setTimeout(() => {
                closeChangePasswordModal();
                // Show a success message that will be visible after modal closes
                showSuccessMessage(data.message || '<?php echo __('password_changed_successfully'); ?>');
            }, 1500);
        } else {
            // API returned success: false
            const error = new Error(data.message || '<?php echo __('failed_to_change_password'); ?>');
            error.response = data;
            throw error;
        }
    })
    .catch(error => {
        console.error('Password change error:', error);
        
        // Handle different types of errors
        if (error.response) {
            // Server responded with an error status code
            const field = error.response.field;
            const errorType = error.response.error_type;
            const errorMessage = error.response.message || '<?php echo __('failed_to_change_password_check_input'); ?>';
            
            // Show error message
            showMessage('error', errorMessage);
            
            // Highlight the problematic field if specified
            if (field) {
                showFieldError(field, errorMessage);
            }
            
            // Handle session timeout
            if (errorType === 'session') {
                // Redirect to login after a delay
                setTimeout(() => {
                    window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
                }, 2000);
            }
        } else if (error.message === 'Failed to fetch') {
            // Network error
            showMessage('error', '<?php echo __('network_error_check_connection'); ?>');
        } else {
            // Other errors
            showMessage('error', error.message || '<?php echo __('unexpected_error_occurred'); ?>');
        }
    })
    .finally(() => {
        // Always re-enable the submit button and restore its text
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
    
    return false; // Prevent form submission
}

// Helper function to show message in the modal
function showMessage(type, message) {
    const messageDiv = document.getElementById('changePasswordMessage');
    const messageText = messageDiv.querySelector('.message');
    const icon = messageDiv.querySelector('i');
    
    // Update message and icon
    messageText.textContent = message;
    messageDiv.className = `alert alert-${type}`;
    
    // Update icon based on message type
    icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    // Show the message
    messageDiv.classList.remove('d-none');
    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Helper function to show success message after modal closes
function showSuccessMessage(message) {
    const successAlert = document.createElement('div');
    successAlert.className = 'alert alert-success fixed-alert';
    successAlert.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(successAlert);
    
    // Remove the success message after 3 seconds
    setTimeout(() => {
        successAlert.remove();
    }, 3000);
}

// Helper function to show error for a specific field
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    const formGroup = field.closest('.form-group');
    if (formGroup) {
        // Add error class to highlight the field
        formGroup.classList.add('has-error');
        
        // Show error message
        let errorElement = formGroup.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message text-danger';
            formGroup.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }
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

    // Handle form submission
    const form = document.getElementById('changePasswordForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitChangePassword();
        });
    }

    // Setup password toggle buttons
    setupPasswordToggle();
});

// Make functions globally available
window.openChangePasswordModal = openChangePasswordModal;
window.closeChangePasswordModal = closeChangePasswordModal;
</script>
