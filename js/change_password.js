document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('changePasswordForm');
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const messageDiv = document.getElementById('changePasswordMessage');
    const submitBtn = document.getElementById('changePasswordBtn');
    const spinner = submitBtn.querySelector('.spinner-border');

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-target'));
            const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
            target.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });

    // Password match validation
    function validatePasswords() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords don't match");
            confirmPassword.classList.add('is-invalid');
            return false;
        } else {
            confirmPassword.setCustomValidity('');
            confirmPassword.classList.remove('is-invalid');
            return true;
        }
    }

    newPassword.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate passwords match
        if (!validatePasswords()) {
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        messageDiv.classList.add('d-none');

        // Prepare form data
        const formData = new FormData(form);

        // Send AJAX request
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
                showMessage(data.message || 'Password changed successfully!', 'success');
                form.reset();
                // Close modal after 2 seconds
                setTimeout(() => {
                    $('#changePasswordModal').modal('hide');
                }, 2000);
            } else {
                showMessage(data.message || 'An error occurred. Please try again.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred. Please try again.', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    });

    // Show message function
    function showMessage(message, type) {
        messageDiv.textContent = message;
        messageDiv.className = `alert alert-${type}`;
        messageDiv.classList.remove('d-none');
    }

    // Reset form when modal is closed
    $('#changePasswordModal').on('hidden.bs.modal', function () {
        form.reset();
        messageDiv.classList.add('d-none');
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    });
});
