<?php
session_start();
$pageTitle = "Login";
require_once 'config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <?php include ('components/head.php');
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
    </style>
    <link rel="stylesheet" href="./style/login.css">
</head>

<body>
    <div class="container">


        <!-- Include Header Component -->
        <?php include 'components/header.php'; ?>
        <div class="language-selector">
        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
        <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>

        <!-- Main Content -->
        <main>
            <div class="main-content">
                <!-- Left Column - Sign Up Form -->
                <div class="signup-container">
                    <h1><?php echo __('welcome_back'); ?></h1>
                    <p class="welcome-text">
                        <?php echo __('sign_in_message'); ?>
                    </p>

                    <?php
                    // Display error messages if any
                    if (isset($_GET['error'])) {
                        echo '<div class="error-message">';

                        switch ($_GET['error']) {
                            case 'invalid':
                                echo __('invalid_credentials');
                                break;
                            case 'validation':
                                echo __('validation_error');
                                break;
                            case 'system':
                                echo __('system_error');
                                break;
                            default:
                                echo __('general_error');
                        }

                        echo '</div>';
                    }

                    // Display session errors if any
                    if (isset($_SESSION['login_errors']) && !empty($_SESSION['login_errors'])) {
                        echo '<div class="error-message">';
                        foreach ($_SESSION['login_errors'] as $error) {
                            echo $error . '<br>';
                        }
                        echo '</div>';
                        // Clear the errors
                        unset($_SESSION['login_errors']);
                    }
                    ?>

                    

                    <!-- Divider -->
                    <div class="divider">
                        <div class="divider-line"></div>
                        
                        <div class="divider-line"></div>
                    </div>

                    <!-- Login Form -->
                    <form action="login_process.php" method="post" class="signup-form">
                        <input type="email" name="email" placeholder="<?php echo __('email_address'); ?>" class="form-input" required>

                        <div class="password-input">
                            <input type="password" id="password" name="password" placeholder="<?php echo __('password'); ?>" class="form-input" required>
                            <button type="button" id="togglePassword" class="password-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>

                        <div style="text-align: right; margin-bottom: 15px;">
                            <a href="#" style="color: #4ce595; font-size: 14px; text-decoration: none;"><?php echo __('forgot_password'); ?></a>
                        </div>

                        <button type="submit" class="btn btn-signup"><?php echo __('log_in'); ?></button>
                        <button type="button" onclick="window.location.href='register.php'" class="btn btn-login"><?php echo __('new_user'); ?></button>
                    </form>
                </div>

                
            </div>
        </main>

        <!-- Include Footer Component -->
        <?php include 'components/footer.php'; ?>
    </div>

    <script src="js/main.js"></script>
    <script src="js/login.js"></script>
</body>

</html>