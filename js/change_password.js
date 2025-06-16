document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('changePasswordForm');
    if (!form) return;
    
    const currentPassword = document.getElementById('currentPassword');
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const messageDiv = document.getElementById('changePasswordMessage');
    const submitBtn = form.querySelector('button[type="submit"]');
    const modal = document.getElementById('changePasswordModal');

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-target'));
            if (!target) return;
            
            const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
            target.setAttribute('type', type);
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        });
    });

    // Validate form
    function validateForm() {
        let isValid = true;
        
        // Reset previous errors
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
        messageDiv.classList.add('d-none');
        
        // Validate current password
        if (!currentPassword.value.trim()) {
            document.getElementById('currentPasswordError').textContent = 'Current password is required';
            currentPassword.classList.add('is-invalid');
            isValid = false;
        }
        
        // Validate new password
        if (newPassword.value.length < 8) {
            document.getElementById('newPasswordError').textContent = 'Password must be at least 8 characters long';
            newPassword.classList.add('is-invalid');
            isValid = false;
        }
        
        // Validate confirm password
        if (newPassword.value !== confirmPassword.value) {
            document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
            confirmPassword.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            return;
        }

        // Show loading state
        const originalButtonText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Changing...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'An error occurred');
            }

            if (data.success) {
                // Show success message
                showMessage(data.message || 'Password changed successfully!', 'success');
                form.reset();
                
                // Close modal after delay
                setTimeout(() => {
                    if (window.jQuery && modal) {
                        $(modal).modal('hide');
                    } else if (modal) {
                        modal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                    }
                }, 1500);
            } else {
                // Show server-side validation errors
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, message]) => {
                        const errorElement = document.getElementById(`${field}Error`);
                        if (errorElement) {
                            errorElement.textContent = Array.isArray(message) ? message[0] : message;
                            const input = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
                            if (input) input.classList.add('is-invalid');
                        }
                    });
                } else {
                    showMessage(data.message || 'An error occurred. Please try again.', 'danger');
                }
                
                // Scroll to first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage(error.message || 'An error occurred. Please try again.', 'danger');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalButtonText;
        }
    });

    // Show message function
    function showMessage(message, type) {
        messageDiv.textContent = message;
        messageDiv.className = `alert alert-${type}`;
        messageDiv.classList.remove('d-none');
    }

    // Reset form when modal is closed
    if (window.jQuery && modal) {
        $(modal).on('hidden.bs.modal', function() {
            form.reset();
            messageDiv.classList.add('d-none');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        });
    }
});
