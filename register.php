<?php
session_start();
$pageTitle = "Register";
require_once 'config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <?php include 'components/head.php'; ?>
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
    <link rel="stylesheet" href="./style/login.css">
</head>

<body>
    <div class="container">
        <?php include 'components/header.php'; ?>
        <div class="language-selector">
            <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
            <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
        </div>
        <main>
            <div class="main-content">
                <div class="signup-container">
                    <h1><?php echo __('create_account'); ?></h1>
                    <p class="welcome-text">
                        <?php echo __('join_message'); ?>
                    </p>
                    <?php
                    if (isset($_GET['error'])) {
                        echo '<div class="error-message">';
                        switch ($_GET['error']) {
                            case 'email_exists':
                                echo __('email_exists_error');
                                break;
                            case 'validation':
                                echo __('validation_error');
                                break;
                            case 'system':
                                echo __('system_error');
                                break;
                            case 'insert_failed':
                                echo __('registration_failed');
                                break;
                            default:
                                echo __('general_error');
                        }
                        echo '</div>';
                    }
                    if (isset($_SESSION['register_errors']) && !empty($_SESSION['register_errors'])) {
                        echo '<div class="error-message">';
                        foreach ($_SESSION['register_errors'] as $error) {
                            echo $error . '<br>';
                        }
                        echo '</div>';
                        unset($_SESSION['register_errors']);
                    }
                    if (isset($_GET['success'])) {
                        echo '<div class="success-message">' . __('registration_success') . '</div>';
                    }
                    ?>
                    <div class="divider">
                        <div class="divider-line"></div>
                        <div class="divider-line"></div>
                    </div>
                    <form action="register_process.php" method="post" class="signup-form">
                        <div class="form-row">
                            <input type="text" name="firstName" placeholder="<?php echo __('first_name'); ?>" class="form-input half" required>
                            <input type="text" name="lastName" placeholder="<?php echo __('last_name'); ?>" class="form-input half" required>
                        </div>
                        <input type="email" name="email" placeholder="<?php echo __('enter_email'); ?>" class="form-input" required>
                        <div class="phone-input">
                            <input type="tel" name="phoneNumber" placeholder="<?php echo __('enter_phone'); ?>" class="form-input">
                        </div>
                        <div class="password-input">
                            <input type="password" id="password" name="password" placeholder="<?php echo __('enter_password'); ?>" class="form-input" required>
                            <button type="button" id="togglePassword" class="password-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="password-input">
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="<?php echo __('confirm_password'); ?>" class="form-input" required>
                            <button type="button" id="toggleConfirmPassword" class="password-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="password-requirements">
                            <p><?php echo __('min_characters'); ?></p>
                            <p><?php echo __('one_number'); ?></p>
                            <p><?php echo __('one_uppercase'); ?></p>
                        </div>
                        <button type="submit" class="btn btn-signup"><?php echo __('create_account'); ?></button>
                        <button type="button" onclick="window.location.href='login.php'" class="btn btn-login"><?php echo __('already_have_account'); ?></button>
                    </form>
                </div>
            </div>
        </main>
        <?php include 'components/footer.php'; ?>
    </div>
    <script src="js/main.js"></script>
    <script src="js/register.js"></script>
</body>

</html>