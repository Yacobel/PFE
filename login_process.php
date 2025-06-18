<?php
session_start();
require_once 'config/db.php'; // Include the database connection

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                $_SESSION['login_time'] = time();

                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = 'Invalid email or password. Please try again.';
                header("Location: login.php?error=invalid");
                exit;
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $errors[] = 'An error occurred during login. Please try again later.';
            header("Location: login.php?error=system");
            exit;
        }
    } else {
        header("Location: login.php?error=validation");
        exit;
    }
}

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
}

header("Location: login.php");
exit;
