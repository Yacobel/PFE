<?php
session_start();
require_once 'config/db.php';
require_once 'config/languages.php';

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

// Debug: Check user data and profile picture path
error_log('User profile_picture from DB: ' . ($user['profile_picture'] ?? 'NULL'));
error_log('DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT']);

if (!$user) {
    $_SESSION['error_message'] = __("User not found.");
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

// For executors: Get additional statistics
$executor_stats = [];
if ($user['role'] === 'executor') {
    // Get total earnings
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(p.amount), 0) as total_earnings 
        FROM payments p 
        JOIN tasks t ON p.task_id = t.task_id 
        WHERE t.executor_id = ? AND p.status = 'completed'");
    $stmt->execute([$user_id]);
    $executor_stats['total_earnings'] = $stmt->fetch()['total_earnings'];    // Get count of available tasks (tasks with no executor assigned and not posted by current user)
    $stmt = $pdo->prepare("SELECT COUNT(*) as available_tasks 
        FROM tasks 
        WHERE executor_id IS NULL 
        AND status = 'posted' 
        AND client_id != ?");
    $stmt->execute([$user_id]);
    $executor_stats['available_tasks'] = $stmt->fetch()['available_tasks'];

    // Get count of current assignments (in progress tasks)
    $stmt = $pdo->prepare("SELECT COUNT(*) as current_assignments 
        FROM tasks 
        WHERE executor_id = ? AND status = 'in_progress'");
    $stmt->execute([$user_id]);
    $executor_stats['current_assignments'] = $stmt->fetch()['current_assignments'];
}

// For clients: Get additional statistics
$client_stats = [];
if ($user['role'] === 'client') {
    // Get total payments made to executors
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(p.amount), 0) as total_payments 
        FROM payments p 
        JOIN tasks t ON p.task_id = t.task_id 
        WHERE t.client_id = ? AND p.status = 'completed'");
    $stmt->execute([$user_id]);
    $client_stats['total_payments'] = $stmt->fetch()['total_payments'];

    // Get count of pending bids
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_bids 
        FROM bids b 
        JOIN tasks t ON b.task_id = t.task_id 
        WHERE t.client_id = ? AND b.status = 'pending'");
    $stmt->execute([$user_id]);
    $client_stats['pending_bids'] = $stmt->fetch()['pending_bids'];

    // Get tasks with executors
    $stmt = $pdo->prepare("SELECT COUNT(*) as tasks_with_executors 
        FROM tasks 
        WHERE client_id = ? AND executor_id IS NOT NULL");
    $stmt->execute([$user_id]);
    $client_stats['tasks_with_executors'] = $stmt->fetch()['tasks_with_executors'];
}

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

$pageTitle = __('my_profile');
include 'components/head.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'en'; ?>" dir="<?php echo ($_SESSION['lang'] ?? 'en') === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./style/profile.css">
    <?php if (($_SESSION['lang'] ?? 'en') === 'ar'): ?>
    <link rel="stylesheet" href="style/rtl.css">
    <?php endif; ?>
</head>

<body>


    <div class="container">
        <?php include 'components/header.php'; ?>
        
        <!-- Language Selector -->
        <div class="language-selector">
            <a href="?lang=en" class="<?php echo ($_SESSION['lang'] ?? 'en') === 'en' ? 'active' : ''; ?>">En</a>
            <a href="?lang=ar" class="<?php echo ($_SESSION['lang'] ?? 'en') === 'ar' ? 'active' : ''; ?>">Ar</a>
        </div>
        
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php
                    // Default avatar path (relative to web root)
                    $default_avatar = '/PFE/images/default-avatar.png';
                    $profile_pic = $default_avatar;

                    // Debug information
                    $debug_info = [];
                    $debug_info[] = 'Starting profile picture processing';

                    try {
                        // Ensure default avatar exists
                        $default_avatar_path = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') . '/' . ltrim($default_avatar, '/');
                        $default_avatar_dir = dirname($default_avatar_path);

                        $debug_info[] = 'Default avatar path: ' . $default_avatar_path;

                        // Create default avatar directory if it doesn't exist
                        if (!is_dir($default_avatar_dir)) {
                            $debug_info[] = 'Creating directory: ' . $default_avatar_dir;
                            if (!@mkdir($default_avatar_dir, 0777, true) && !is_dir($default_avatar_dir)) {
                                throw new Exception('Failed to create directory: ' . $default_avatar_dir);
                            }
                            $debug_info[] = 'Directory created successfully';
                        }

                        // Check if default avatar exists, if not, create a blank one
                        if (!file_exists($default_avatar_path)) {
                            $debug_info[] = 'Default avatar not found, creating blank one';
                            $blank_image = imagecreatetruecolor(200, 200);
                            $bg_color = imagecolorallocate($blank_image, 221, 221, 221);
                            imagefill($blank_image, 0, 0, $bg_color);

                            // Save the image
                            imagepng($blank_image, $default_avatar_path);
                            imagedestroy($blank_image);
                            $debug_info[] = 'Default avatar created at: ' . $default_avatar_path;
                        }
                    } catch (Exception $e) {
                        $debug_info[] = 'Error setting up default avatar: ' . $e->getMessage();
                        error_log('Profile Picture Error: ' . $e->getMessage());
                    }

                    try {
                        if (!empty($user['profile_picture'])) {
                            $debug_info[] = 'Original profile_picture: ' . $user['profile_picture'];

                            // If it's a full URL
                            if (strpos($user['profile_picture'], 'http') === 0) {
                                $profile_pic = $user['profile_picture'];
                                $debug_info[] = 'Using HTTP URL: ' . $profile_pic;
                            }
                            // Handle local paths
                            else {
                                // Clean up the path
                                $clean_path = ltrim($user['profile_picture'], '/');

                                // If path already starts with PFE, use as is, otherwise prepend /PFE/
                                if (strpos($clean_path, 'PFE/') === 0 || strpos($clean_path, 'PFE\\') === 0) {
                                    $profile_pic = '/' . ltrim(str_replace('\\', '/', $clean_path), '/');
                                } else {
                                    $profile_pic = '/PFE/' . ltrim($clean_path, '/');
                                }

                                $debug_info[] = 'Processed path: ' . $profile_pic;

                                // Check if file exists on server
                                $full_path = str_replace('//', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim($profile_pic, '/'));
                                $debug_info[] = 'Checking file at: ' . $full_path;

                                if (file_exists($full_path) && is_file($full_path)) {
                                    $debug_info[] = 'File exists and is readable';
                                    // Ensure the file is readable
                                    if (!is_readable($full_path)) {
                                        throw new Exception('File exists but is not readable');
                                    }

                                    // Verify it's a valid image
                                    $image_info = @getimagesize($full_path);
                                    if ($image_info === false) {
                                        throw new Exception('File is not a valid image');
                                    }

                                    $debug_info[] = 'Image type: ' . ($image_info['mime'] ?? 'unknown');
                                } else {
                                    throw new Exception('File does not exist or is not readable');
                                }
                            }
                        } else {
                            $debug_info[] = 'No profile_picture in user data, using default';
                            $profile_pic = $default_avatar;
                        }
                    } catch (Exception $e) {
                        $debug_info[] = 'Error processing profile picture: ' . $e->getMessage();
                        error_log('Profile Picture Error: ' . $e->getMessage());
                        $profile_pic = $default_avatar;
                    }

                    // Final check to ensure we have a valid image
                    try {
                        if ($profile_pic !== $default_avatar) {
                            $final_path = strpos($profile_pic, 'http') === 0
                                ? $profile_pic
                                : rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim($profile_pic, '/');

                            $debug_info[] = 'Final validation path: ' . $final_path;

                            if (!file_exists($final_path) || !is_file($final_path) || !is_readable($final_path)) {
                                throw new Exception('Final validation failed: File not accessible');
                            }

                            // Verify it's an image one more time
                            if (@getimagesize($final_path) === false) {
                                throw new Exception('Final validation failed: Not a valid image');
                            }
                        }
                    } catch (Exception $e) {
                        $debug_info[] = 'Final validation error: ' . $e->getMessage();
                        $profile_pic = $default_avatar;
                    }

                    // Log debug info for troubleshooting
                    error_log("Profile Picture Debug: " . implode(" | ", $debug_info));

                    // Add cache buster to prevent caching issues
                    $cache_buster = '?v=' . time();
                    $img_src = htmlspecialchars($profile_pic, ENT_QUOTES, 'UTF-8') . $cache_buster;
                    $default_src = htmlspecialchars($default_avatar, ENT_QUOTES, 'UTF-8') . $cache_buster;
                    ?>
                    <img src="<?php echo $img_src; ?>"
                        alt="<?php echo __('profile_picture'); ?>"
                        id="profilePicture"
                        onerror="this.onerror=null; this.src='<?php echo $default_src; ?>';">
                    <?php if (isset($user['status'])): ?>
                        <span class="status-badge <?php echo htmlspecialchars($user['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($user['status'])); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h1><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : __('User'); ?></h1>
                    <?php if (isset($user['role'])): ?>
                        <p class="role"><?php echo $user['role'] === 'client' ? __('Client') : __('Freelancer'); ?></p>
                    <?php endif; ?> <?php if (isset($user['role']) && $user['role'] === 'executor'): ?>
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $rating ? 'filled' : ''; ?>"></i>
                            <?php endfor; ?>
                            <span>(<?php echo $rating; ?>)</span>
                        </div>
                    <?php endif; ?>

                    <!-- Switch Role Button -->
                    <div class="profile-actions">
                        <a href="api/switch_role.php?role=<?php echo $user['role'] === 'executor' ? 'client' : 'executor'; ?>" class="btn btn-switch">
                            <i class="fas fa-sync"></i> <?php echo $user['role'] === 'executor' ? __('Switch to Client') : __('Switch to Executor'); ?>
                        </a>

                        <!-- Language Switch Button -->
                        <a href="?lang=<?php echo $_SESSION['lang'] === 'en' ? 'ar' : 'en'; ?>" class="btn btn-lang-switch">
                            <i class="fas fa-language"></i> <?php echo $_SESSION['lang'] === 'en' ? __('Arabic') : __('English'); ?>
                        </a>
                    </div>
                </div>
            </div> <!-- Profile Stats -->
            <div class="profile-stats">
                <?php if ($user['role'] === 'client'): ?>
                    <div class="stat-card">
                        <i class="fas fa-tasks" title="<?php echo __('tasks_posted'); ?>"></i>
                        <div class="stat-info">
                            <h3><?php echo $task_counts['tasks_posted'] ?? 0; ?></h3>
                            <p><?php echo __('tasks_posted'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="stat-card">
                    <i class="fas fa-check-circle" title="<?php echo __('tasks_completed'); ?>"></i>
                    <div class="stat-info">
                        <h3><?php echo $task_counts['tasks_completed'] ?? 0; ?></h3>
                        <p><?php echo __('tasks_completed'); ?></p>
                    </div>
                </div><?php if ($user['role'] === 'client'): ?>
                    <div class="stat-card">
                        <i class="fas fa-money-bill-wave" title="<?php echo __('total_earnings'); ?>"></i>
                        <div class="stat-info">
                            <h3>$<?php echo number_format($client_stats['total_payments'] ?? 0, 2); ?></h3>
                            <p><?php echo __('total_payments'); ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-user-clock" title="<?php echo __('pending_bids'); ?>"></i>
                        <div class="stat-info">
                            <h3><?php echo $client_stats['pending_bids'] ?? 0; ?></h3>
                            <p><?php echo __('pending_bids'); ?></p>
                        </div>
                    </div>
                <?php elseif ($user['role'] === 'executor'): ?>
                    <div class="stat-card">
                        <i class="fas fa-money-bill-wave" title="<?php echo __('total_earnings'); ?>"></i>
                        <div class="stat-info">
                            <h3>$<?php echo number_format($executor_stats['total_earnings'] ?? 0, 2); ?></h3>
                            <p><?php echo __('total_earnings'); ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-briefcase" title="<?php echo __('available_tasks'); ?>"></i>
                        <div class="stat-info">
                            <h3><?php echo $executor_stats['available_tasks'] ?? 0; ?></h3>
                            <p><?php echo __('available_tasks'); ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clock" title="<?php echo __('current_assignments'); ?>"></i>
                        <div class="stat-info">
                            <h3><?php echo $executor_stats['current_assignments'] ?? 0; ?></h3>
                            <p><?php echo __('current_assignments'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div> <!-- Task Overview Section -->
            <?php if ($user['role'] === 'client'): ?>
                <div class="profile-details task-overview">
                    <h2><?php echo __('task_overview'); ?></h2>
                    <!-- Quick Links -->
                    <div class="quick-links">
                        <a href="my_tasks.php" class="btn btn-outline-primary">
                            <i class="fas fa-tasks" title="<?php echo __('tasks_posted'); ?>"></i> <?php echo __('view_all_tasks'); ?>
                        </a>
                        <a href="pending_bids.php" class="btn btn-outline-info">
                            <i class="fas fa-clock" title="<?php echo __('current_assignments'); ?>"></i> <?php echo __('view_pending_bids'); ?>
                        </a>
                    </div>
                </div>
            <?php elseif ($user['role'] === 'executor'): ?>
                <div class="profile-details task-overview">
                    <h2><?php echo __('task_overview'); ?></h2>
                    <!-- Quick Links -->
                    <div class="quick-links"> <a href="taskes.php" class="btn btn-outline-primary">
                            <i class="fas fa-briefcase" title="<?php echo __('available_tasks'); ?>"></i> <?php echo __('available_tasks'); ?>
                        </a>
                        <a href="my_assignments.php" class="btn btn-outline-success">
                            <i class="fas fa-clipboard-check"></i> <?php echo __('my_assignments'); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Profile Details -->
            <div class="profile-details">
                <h2><?php echo __('about_me'); ?></h2>
                <p class="bio"><?php echo !empty($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : __('no_bio_available'); ?></p>

                <div class="detail-row">
                    <i class="fas fa-envelope" title="<?php echo __('email'); ?>"></i>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>

                <?php if (!empty($user['phone'])): ?>
                    <div class="detail-row">
                        <i class="fas fa-phone" title="<?php echo __('phone'); ?>"></i>
                        <span><?php echo htmlspecialchars($user['phone']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['address'])): ?>
                    <div class="detail-row">
                        <i class="fas fa-map-marker-alt" title="<?php echo __('address'); ?>"></i>
                        <span><?php echo htmlspecialchars($user['address']); ?></span>
                    </div>
                <?php endif; ?>

                <div class="detail-row">
                    <i class="fas fa-user-shield" title="<?php echo __('account_status'); ?>"></i>
                    <span><?php echo __('account_status'); ?>: <span class="status-badge <?php echo !empty($user['status']) ? htmlspecialchars($user['status']) : 'active'; ?>"><?php echo !empty($user['status']) ? ucfirst(htmlspecialchars($user['status'])) : __('Active'); ?></span></span>
                </div>

                <div class="detail-row">
                    <i class="fas fa-calendar-check" title="<?php echo __('last_login'); ?>"></i>
                    <span><?php echo __('last_login'); ?>: <?php echo isset($user['last_login']) && !empty($user['last_login']) ? date('M d, Y H:i', strtotime($user['last_login'])) : __('Never'); ?></span>
                </div>
            </div> <!-- Action Buttons -->
            <div class="profile-actions">
                <button onclick="openEditProfileModal()" class="btn btn-primary">
                    <i class="fas fa-edit"></i> <?php echo __('edit_profile'); ?>
                </button>
                                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#changePasswordModal">
                    <i class="fas fa-key"></i> <?php echo __('change_password'); ?>
                </button>
            </div>
        </div>
    </div>
    <?php include 'components/edit_profile_modal.php'; ?>
    <?php include 'components/change_password_modal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/profile.js"></script>
    <script src="js/change_password.js"></script>
    <style>

    </style>
</body>

</html>