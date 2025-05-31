<?php
session_start();
require_once 'db.php'; // Include the database connection

// Initialize errors array
$errors = [];

// Process the form only if it's submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Validate email
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    // Validate password
    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    // If no validation errors, proceed with login
    if (empty($errors)) {
        try {
            // Prepare SQL statement to prevent SQL injection
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // Set a login timestamp
                $_SESSION['login_time'] = time();

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                // Invalid credentials
                $errors[] = 'Invalid email or password. Please try again.';
                header("Location: login.php?error=invalid");
                exit;
            }
        } catch (PDOException $e) {
            // Log the error and show a generic message
            error_log("Login error: " . $e->getMessage());
            $errors[] = 'An error occurred during login. Please try again later.';
            header("Location: login.php?error=system");
            exit;
        }
    } else {
        // If there are validation errors, redirect back with error flag
        header("Location: login.php?error=validation");
        exit;
    }
}

// Store errors in session for displaying on the login page
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
}

// If somehow we get here without redirecting, go back to login page
header("Location: login.php");
exit;
