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
    function submitChangePassword() {
        const form = document.getElementById('changePasswordForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('changePasswordBtn');
        const messageDiv = document.getElementById('changePasswordMessage');
        const messageText = messageDiv.querySelector('.message');
        const originalBtnText = submitBtn.innerHTML;
        messageText.textContent = '';
        messageDiv.className = 'alert d-none';
        document.querySelectorAll('.form-group').forEach(group => {
            group.classList.remove('has-error');
        });
        const currentPassword = document.getElementById('currentPassword').value.trim();
        const newPassword = document.getElementById('newPassword').value.trim();
        const confirmPassword = document.getElementById('confirmPassword').value.trim();
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
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php echo __('updating'); ?>...';
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
                    const error = new Error(data.message || '<?php echo __('failed_to_change_password'); ?>');
                    error.response = data;
                    error.status = response.status;
                    throw error;
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message || '<?php echo __('password_changed_successfully'); ?>');
                    form.reset();
                    setTimeout(() => {
                        closeChangePasswordModal();
                        showSuccessMessage(data.message || '<?php echo __('password_changed_successfully'); ?>');
                    }, 1500);
                } else {
                    const error = new Error(data.message || '<?php echo __('failed_to_change_password'); ?>');
                    error.response = data;
                    throw error;
                }
            })
            .catch(error => {
                console.error('Password change error:', error);
                if (error.response) {
                    const field = error.response.field;
                    const errorType = error.response.error_type;
                    const errorMessage = error.response.message || '<?php echo __('failed_to_change_password_check_input'); ?>';
                    showMessage('error', errorMessage);
                    if (field) {
                        showFieldError(field, errorMessage);
                    }
                    if (errorType === 'session') {
                        setTimeout(() => {
                            window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
                        }, 2000);
                    }
                } else if (error.message === 'Failed to fetch') {
                    showMessage('error', '<?php echo __('network_error_check_connection'); ?>');
                } else {
                    showMessage('error', error.message || '<?php echo __('unexpected_error_occurred'); ?>');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        return false;
    }

    function showMessage(type, message) {
        const messageDiv = document.getElementById('changePasswordMessage');
        const messageText = messageDiv.querySelector('.message');
        const icon = messageDiv.querySelector('i');
        messageText.textContent = message;
        messageDiv.className = `alert alert-${type}`;
        icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        messageDiv.classList.remove('d-none');
        messageDiv.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    function showSuccessMessage(message) {
        const successAlert = document.createElement('div');
        successAlert.className = 'alert alert-success fixed-alert';
        successAlert.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
        document.body.appendChild(successAlert);
        setTimeout(() => {
            successAlert.remove();
        }, 3000);
    }

    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            formGroup.classList.add('has-error');
            let errorElement = formGroup.querySelector('.error-message');
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'error-message text-danger';
                formGroup.appendChild(errorElement);
            }
            errorElement.textContent = message;
        }
    }

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
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('changePasswordModal');
            if (event.target === modal) {
                closeChangePasswordModal();
            }
        });
        document.addEventListener('keydown', function(event) {
            const modal = document.getElementById('changePasswordModal');
            if (event.key === 'Escape' && modal && modal.style.display === 'flex') {
                closeChangePasswordModal();
            }
        });
        const form = document.getElementById('changePasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitChangePassword();
            });
        }
        setupPasswordToggle();
    });
    window.openChangePasswordModal = openChangePasswordModal;
    window.closeChangePasswordModal = closeChangePasswordModal;
</script>