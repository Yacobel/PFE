<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    session_start();
    $pageTitle = "Register";
    include 'components/head.php';
    ?>
    <style>
        .error-message {
            color: #ff5252;
            background-color: rgba(255, 82, 82, 0.1);
            border: 1px solid rgba(255, 82, 82, 0.3);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .success-message {
            color: #4ce595;
            background-color: rgba(76, 229, 149, 0.1);
            border: 1px solid rgba(76, 229, 149, 0.3);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Particle effect overlay -->
        <div class="particles"></div>

        <!-- Include Header Component -->
        <?php include 'components/header.php'; ?>

        <!-- Main Content -->
        <main>
            <div class="main-content">
                <!-- Left Column - Registration Form -->
                <div class="signup-container">
                    <h1>Create Account</h1>
                    <p class="welcome-text">
                        Join Task Platform and start posting or completing tasks today!
                    </p>

                    <?php
                    // Display error messages if any
                    if (isset($_GET['error'])) {
                        echo '<div class="error-message">';

                        switch ($_GET['error']) {
                            case 'email_exists':
                                echo 'This email address is already registered. Please use another email or log in.';
                                break;
                            case 'validation':
                                echo 'Please check your input and try again.';
                                break;
                            case 'system':
                                echo 'A system error occurred. Please try again later.';
                                break;
                            case 'insert_failed':
                                echo 'Registration failed. Please try again later.';
                                break;
                            default:
                                echo 'An error occurred during registration. Please try again.';
                        }

                        echo '</div>';
                    }

                    // Display session errors if any
                    if (isset($_SESSION['register_errors']) && !empty($_SESSION['register_errors'])) {
                        echo '<div class="error-message">';
                        foreach ($_SESSION['register_errors'] as $error) {
                            echo $error . '<br>';
                        }
                        echo '</div>';
                        // Clear the errors
                        unset($_SESSION['register_errors']);
                    }

                    // Display success message if registration is successful
                    if (isset($_GET['success'])) {
                        echo '<div class="success-message">Registration successful! You can now log in.</div>';
                    }
                    ?>

                    <!-- Google Sign Up Button -->
                    <button class="btn-google">
                        <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                        </svg>
                        Sign Up With Google
                    </button>

                    <!-- Divider -->
                    <div class="divider">
                        <div class="divider-line"></div>
                        <span class="divider-text">Or</span>
                        <div class="divider-line"></div>
                    </div>

                    <!-- Registration Form -->
                    <form action="register_process.php" method="post" class="signup-form">
                        <div class="form-row">
                            <input type="text" name="firstName" placeholder="First Name" class="form-input half" required>
                            <input type="text" name="lastName" placeholder="Last Name" class="form-input half" required>
                        </div>
                        <input type="email" name="email" placeholder="Enter Your Email" class="form-input" required>
                        <div class="phone-input">
                            <input type="checkbox" id="phoneCheck" class="phone-checkbox">
                            <input type="tel" name="phoneNumber" placeholder="Enter Your Phone Number" class="form-input">
                        </div>
                        <div class="password-input">
                            <input type="password" id="password" name="password" placeholder="Enter Your Password" class="form-input" required>
                            <button type="button" id="togglePassword" class="password-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="password-input">
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Your Password" class="form-input" required>
                            <button type="button" id="toggleConfirmPassword" class="password-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="password-requirements">
                            <p>A minimum of 8 characters.</p>
                            <p>At least one number</p>
                            <p>At least one uppercase letter</p>
                        </div>
                        <button type="submit" class="btn btn-signup">Create Account</button>
                        <button type="button" onclick="window.location.href='login.php'" class="btn btn-login">Already have an account? Log In</button>
                    </form>
                </div>

                <!-- Right Column - Illustration -->
                <div class="illustration-container">
                    <div class="illustration">
                        <svg width="300" height="400" viewBox="0 0 300 400" fill="none" xmlns="http://www.w3.org/2000/svg" class="wave-lines">
                            <path d="M50 200 L250 200" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M70 220 L230 220" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M90 240 L210 240" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M110 260 L190 260" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M130 280 L170 280" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M50 180 L250 180" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M70 160 L230 160" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M90 140 L210 140" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M110 120 L190 120" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                            <path d="M130 100 L170 100" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>
            </div>
        </main>

        <!-- Include Footer Component -->
        <?php include 'components/footer.php'; ?>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            if (password.type === 'password') {
                password.type = 'text';
            } else {
                password.type = 'password';
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPassword = document.getElementById('confirmPassword');
            if (confirmPassword.type === 'password') {
                confirmPassword.type = 'text';
            } else {
                confirmPassword.type = 'password';
            }
        });

        // Phone checkbox
        document.getElementById('phoneCheck').addEventListener('change', function() {
            const phoneInput = this.nextElementSibling;
            if (this.checked) {
                phoneInput.setAttribute('required', 'required');
            } else {
                phoneInput.removeAttribute('required');
            }
        });
    </script>
</body>

</html>