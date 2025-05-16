<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// إذا طلب تبديل الدور
if (isset($_POST['switch_role'])) {
    $new_role = $_SESSION['role'] === 'client' ? 'executor' : 'client';

    // تحديث الدور في قاعدة البيانات
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id_user = ?");
    $stmt->execute([$new_role, $_SESSION['user_id']]);

    // تحديث الدور في الجلسة
    $_SESSION['role'] = $new_role;

    header("Location: dashboard.php");  // إعادة تحميل الصفحة
    exit;
}

echo "Welcome, " . htmlspecialchars($_SESSION['name']) . "<br>";
echo "Your role: " . $_SESSION['role'] . "<br>";
?>

<form method="post">
    <button type="submit" name="switch_role">Switch Role</button>
</form>

<a href="logout.php">Logout</a>