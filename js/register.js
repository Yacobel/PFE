/**
 * register.js - JavaScript functionality for the registration page
 * Handles password visibility toggling and phone number validation
 */

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const password = document.getElementById('password');
            if (password) {
                if (password.type === 'password') {
                    password.type = 'text';
                } else {
                    password.type = 'password';
                }
            }
        });
    }

    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function() {
            const confirmPassword = document.getElementById('confirmPassword');
            if (confirmPassword) {
                if (confirmPassword.type === 'password') {
                    confirmPassword.type = 'text';
                } else {
                    confirmPassword.type = 'password';
                }
            }
        });
    }

    const phoneCheck = document.getElementById('phoneCheck');
    if (phoneCheck) {
        phoneCheck.addEventListener('change', function() {
            const phoneInput = this.nextElementSibling;
            if (phoneInput) {
                if (this.checked) {
                    phoneInput.setAttribute('required', 'required');
                } else {
                    phoneInput.removeAttribute('required');
                }
            }
        });
    }
});
