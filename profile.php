<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error_message'] = "User not found.";
    header("Location: dashboard.php");
    exit;
}

// Get user's tasks count
$stmt = $pdo->prepare("SELECT 
    COUNT(CASE WHEN client_id = ? THEN 1 END) as tasks_posted,
    COUNT(CASE WHEN executor_id = ? THEN 1 END) as tasks_completed
FROM tasks");
$stmt->execute([$user_id, $user_id]);
$task_counts = $stmt->fetch();

// Get user's rating if they're an executor
$rating = 0;
if ($user['role'] === 'executor') {
    $stmt = $pdo->prepare("
        SELECT AVG(r.rating) as avg_rating
        FROM reviews r
        JOIN tasks t ON r.task_id = t.task_id
        WHERE t.executor_id = ?
    ");
    $stmt->execute([$user_id]);
    $rating_result = $stmt->fetch();
    $rating = $rating_result['avg_rating'] ? round($rating_result['avg_rating'], 1) : 0;
}

$pageTitle = "My Profile";
include 'components/head.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style/profile.css">
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="container">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'images/default-avatar.png'; ?>" alt="Profile Picture">
                    <?php if (isset($user['status'])): ?>
                    <span class="status-badge <?php echo htmlspecialchars($user['status']); ?>">
                        <?php echo ucfirst(htmlspecialchars($user['status'])); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h1><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'User'; ?></h1>
                    <?php if (isset($user['role'])): ?>
                    <p class="role"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                    <?php endif; ?>
                    <?php if (isset($user['role']) && $user['role'] === 'executor'): ?>
                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $rating ? 'filled' : ''; ?>"></i>
                        <?php endfor; ?>
                        <span>(<?php echo $rating; ?>)</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Stats -->
            <div class="profile-stats">
                <div class="stat-card">
                    <i class="fas fa-tasks"></i>
                    <div class="stat-info">
                        <h3><?php echo $task_counts['tasks_posted'] ?? 0; ?></h3>
                        <p>Tasks Posted</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-info">
                        <h3><?php echo $task_counts['tasks_completed'] ?? 0; ?></h3>
                        <p>Tasks Completed</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar"></i>
                    <div class="stat-info">
                        <h3><?php echo isset($user['created_at']) && !empty($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : 'N/A'; ?></h3>
                        <p>Member Since</p>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="profile-details">
                <h2>About Me</h2>
                <p class="bio"><?php echo !empty($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : 'No bio available.'; ?></p>
                
                <div class="detail-row">
                    <i class="fas fa-envelope"></i>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                
                <?php if (!empty($user['phone'])): ?>
                <div class="detail-row">
                    <i class="fas fa-phone"></i>
                    <span><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($user['address'])): ?>
                <div class="detail-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($user['address']); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <i class="fas fa-user-shield"></i>
                    <span>Account Status: <span class="status-badge <?php echo $user['status']; ?>"><?php echo ucfirst($user['status']); ?></span></span>
                </div>
                
                <div class="detail-row">
                    <i class="fas fa-calendar-check"></i>
                    <span>Last Login: <?php echo isset($user['last_login']) && !empty($user['last_login']) ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="profile-actions">
                <button onclick="openEditProfileModal()" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <a href="change_password.php" class="btn btn-secondary">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    <?php include 'components/edit_profile_modal.php'; ?>
    
    <script src="js/profile.js"></script>
</body>
</html>
