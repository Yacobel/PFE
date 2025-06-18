<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $category_id = (int)$_POST['category_id'];
        $budget = (float)$_POST['budget'];
        $deadline = $_POST['deadline'];
        $location = isset($_POST['location']) ? trim($_POST['location']) : null;
        $client_id = $_SESSION['user_id'];

        if (empty($title) || empty($description) || empty($category_id) || empty($budget) || empty($deadline)) {
            throw new Exception("All required fields must be filled out");
        }

        $image_url = '';
        if (isset($_FILES['task_image']) && $_FILES['task_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['task_image']['tmp_name'];
            $file_name = $_FILES['task_image']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_ext, $allowed_ext)) {
                throw new Exception("Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.");
            }

            $new_file_name = uniqid('task_', true) . '.' . $file_ext;
            $upload_path = 'uploads/tasks/';


            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }


            if (!move_uploaded_file($file_tmp, $upload_path . $new_file_name)) {
                throw new Exception("Failed to upload image");
            }

            $image_url = $upload_path . $new_file_name;
        } else {
            throw new Exception("Task image is required");
        }


        $stmt = $pdo->prepare("
            INSERT INTO tasks (title, description, category_id, client_id, budget, deadline, location, image_url, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'posted', NOW())
        ");

        $result = $stmt->execute([
            $title,
            $description,
            $category_id,
            $client_id,
            $budget,
            $deadline,
            $location,
            $image_url
        ]);

        if ($result) {
            $_SESSION['success_message'] = "Task created successfully!";
            header("Location: dashboard.php");
            exit;
        } else {
            throw new Exception("Failed to create task");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: dashboard.php");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}
