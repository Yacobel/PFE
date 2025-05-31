<?php
session_start();
require_once 'db.php'; // Include the database connection

// Initialize errors array
$errors = [];

// Process the form only if it's submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Basic validations
    if (empty($firstName)) {
        $errors[] = 'First name is required';
    }

    if (empty($lastName)) {
        $errors[] = 'Last name is required';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    // Password validation
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    // If no validation errors, proceed with registration
    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $emailExists = (bool)$stmt->fetchColumn();

            if ($emailExists) {
                $errors[] = 'Email address is already registered';
                $_SESSION['register_errors'] = $errors;
                header("Location: register.php?error=email_exists");
                exit;
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Create the full name
            $name = $firstName . ' ' . $lastName;

            // Default role is 'client'
            $role = 'client';

            // Insert the new user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$name, $email, $hashedPassword, $role]);

            if ($result) {
                // Get the user ID of the newly registered user
                $userId = $pdo->lastInsertId();

                // Auto-login the user
                $_SESSION['user_id'] = $userId;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                $_SESSION['login_time'] = time();

                // Redirect to dashboard
                header("Location: dashboard.php?welcome=1");
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again.';
                $_SESSION['register_errors'] = $errors;
                header("Location: register.php?error=insert_failed");
                exit;
            }
        } catch (PDOException $e) {
            // Log the error and show a generic message
            error_log("Registration error: " . $e->getMessage());
            $errors[] = 'An error occurred during registration. Please try again later.';
            $_SESSION['register_errors'] = $errors;
            header("Location: register.php?error=system");
            exit;
        }
    } else {
        // If there are validation errors, redirect back with error flag
        $_SESSION['register_errors'] = $errors;
        header("Location: register.php?error=validation");
        exit;
    }
}

// If somehow we get here without redirecting, go back to registration page
header("Location: register.php");
exit;
