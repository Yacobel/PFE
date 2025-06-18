<?php
session_start();
require_once 'config/db.php';
require_once 'config/languages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

error_log('User profile_picture from DB: ' . ($user['profile_picture'] ?? 'NULL'));
error_log('DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT']);

if (!$user) {
    $_SESSION['error_message'] = __("User not found.");
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT 
    COUNT(CASE WHEN client_id = ? THEN 1 END) as tasks_posted,
    COUNT(CASE WHEN executor_id = ? THEN 1 END) as tasks_completed
FROM tasks");
$stmt->execute([$user_id, $user_id]);
$task_counts = $stmt->fetch();

$executor_stats = [];
if ($user['role'] === 'executor') {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(p.amount), 0) as total_earnings 
        FROM payments p 
        JOIN tasks t ON p.task_id = t.task_id 
        WHERE t.executor_id = ? AND p.status = 'completed'");
    $stmt->execute([$user_id]);
    $executor_stats['total_earnings'] = $stmt->fetch()['total_earnings'];    
    $stmt = $pdo->prepare("SELECT COUNT(*) as available_tasks 
        FROM tasks 
        WHERE executor_id IS NULL 
        AND status = 'posted' 
        AND client_id != ?");
    $stmt->execute([$user_id]);
    $executor_stats['available_tasks'] = $stmt->fetch()['available_tasks'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as current_assignments 
        FROM tasks 
        WHERE executor_id = ? AND status = 'in_progress'");
    $stmt->execute([$user_id]);
    $executor_stats['current_assignments'] = $stmt->fetch()['current_assignments'];
}

$client_stats = [];
if ($user['role'] === 'client') {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(p.amount), 0) as total_payments 
        FROM payments p 
        JOIN tasks t ON p.task_id = t.task_id 
        WHERE t.client_id = ? AND p.status = 'completed'");
    $stmt->execute([$user_id]);
    $client_stats['total_payments'] = $stmt->fetch()['total_payments'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_bids 
        FROM bids b 
        JOIN tasks t ON b.task_id = t.task_id 
        WHERE t.client_id = ? AND b.status = 'pending'");
    $stmt->execute([$user_id]);
    $client_stats['pending_bids'] = $stmt->fetch()['pending_bids'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as tasks_with_executors 
        FROM tasks 
        WHERE client_id = ? AND executor_id IS NOT NULL");
    $stmt->execute([$user_id]);
    $client_stats['tasks_with_executors'] = $stmt->fetch()['tasks_with_executors'];
}

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

        <div class="language-selector">
            <a href="?lang=en" class="<?php echo ($_SESSION['lang'] ?? 'en') === 'en' ? 'active' : ''; ?>">En</a>
            <a href="?lang=ar" class="<?php echo ($_SESSION['lang'] ?? 'en') === 'ar' ? 'active' : ''; ?>">Ar</a>
        </div>

        <div class="profile-container">
            <div class="profile-header">
                <div class="user-info-wrapper">
                    <div class="profile-avatar">
                        <?php
                        $default_avatar = '/PFE/images/default-avatar.png';
                        $profile_pic = $default_avatar;
                        $debug_info = [];
                        $debug_info[] = 'Starting profile picture processing';
                        try {
                            $default_avatar_path = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') . '/' . ltrim($default_avatar, '/');
                            $default_avatar_dir = dirname($default_avatar_path);
                            $debug_info[] = 'Default avatar path: ' . $default_avatar_path;
                            if (!is_dir($default_avatar_dir)) {
                                $debug_info[] = 'Creating directory: ' . $default_avatar_dir;
                                if (!@mkdir($default_avatar_dir, 0777, true) && !is_dir($default_avatar_dir)) {
                                    throw new Exception('Failed to create directory: ' . $default_avatar_dir);
                                }
                                $debug_info[] = 'Directory created successfully';
                            }
                            if (!file_exists($default_avatar_path)) {
                                $debug_info[] = 'Default avatar not found, creating blank one';
                                $blank_image = imagecreatetruecolor(200, 200);
                                $bg_color = imagecolorallocate($blank_image, 221, 221, 221);
                                imagefill($blank_image, 0, 0, $bg_color);
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
                                if (strpos($user['profile_picture'], 'http') === 0) {
                                    $profile_pic = $user['profile_picture'];
                                    $debug_info[] = 'Using HTTP URL: ' . $profile_pic;
                                }
                                else {
                                    $clean_path = ltrim($user['profile_picture'], '/');
                                    if (strpos($clean_path, 'PFE/') === 0 || strpos($clean_path, 'PFE\\') === 0) {
                                        $profile_pic = '/' . ltrim(str_replace('\\', '/', $clean_path), '/');
                                    } else {
                                        $profile_pic = '/PFE/' . ltrim($clean_path, '/');
                                    }
                                    $debug_info[] = 'Processed path: ' . $profile_pic;
                                    $full_path = str_replace('//', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim($profile_pic, '/'));
                                    $debug_info[] = 'Checking file at: ' . $full_path;
                                    if (file_exists($full_path) && is_file($full_path)) {
                                        $debug_info[] = 'File exists and is readable';
                                        if (!is_readable($full_path)) {
                                            throw new Exception('File exists but is not readable');
                                        }
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
                        try {
                            if ($profile_pic !== $default_avatar) {
                                $final_path = strpos($profile_pic, 'http') === 0
                                    ? $profile_pic
                                    : rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim($profile_pic, '/');
                                $debug_info[] = 'Final validation path: ' . $final_path;
                                if (!file_exists($final_path) || !is_file($final_path) || !is_readable($final_path)) {
                                    throw new Exception('Final validation failed: File not accessible');
                                }
                                if (@getimagesize($final_path) === false) {
                                    throw new Exception('Final validation failed: Not a valid image');
                                }
                            }
                        } catch (Exception $e) {
                            $debug_info[] = 'Final validation error: ' . $e->getMessage();
                            $profile_pic = $default_avatar;
                        }
                        error_log("Profile Picture Debug: " . implode(" | ", $debug_info));
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
                    <div class="user-details">
                        <h1><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : __('User'); ?></h1>
                        <?php if (isset($user['role'])): ?>
                            <p class="role"><?php echo $user['role'] === 'client' ? __('Client') : __('Freelancer'); ?></p>
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
                <div class="profile-actions">
                    <a href="api/switch_role.php?role=<?php echo $user['role'] === 'executor' ? 'client' : 'executor'; ?>" class="btn-switch">
                        <i class="fas fa-sync"></i> <?php echo $user['role'] === 'executor' ? __('Switch to Client') : __('Switch to Executor'); ?>
                    </a>
                </div>
            </div>
            <div class="stats-grid">
                <?php if ($user['role'] === 'client'): ?>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-value"><?php echo $task_counts['tasks_posted'] ?? 0; ?></div>
                        <div class="stat-label"><?php echo __('tasks_posted'); ?></div>
                    </div>
                <?php endif; ?>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?php echo $task_counts['tasks_completed'] ?? 0; ?></div>
                    <div class="stat-label"><?php echo __('tasks_completed'); ?></div>
                </div>
                <?php if ($user['role'] === 'client'): ?>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-value">$<?php echo number_format($client_stats['total_payments'] ?? 0, 0); ?></div>
                        <div class="stat-label"><?php echo __('total_payments'); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="stat-value"><?php echo $client_stats['pending_bids'] ?? 0; ?></div>
                        <div class="stat-label"><?php echo __('pending_bids'); ?></div>
                    </div>
                <?php elseif ($user['role'] === 'executor'): ?>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-value">$<?php echo number_format($executor_stats['total_earnings'] ?? 0, 0); ?></div>
                        <div class="stat-label"><?php echo __('total_earnings'); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-value"><?php echo $executor_stats['available_tasks'] ?? 0; ?></div>
                        <div class="stat-label"><?php echo __('available_tasks'); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-value"><?php echo $executor_stats['current_assignments'] ?? 0; ?></div>
                        <div class="stat-label"><?php echo __('current_assignments'); ?></div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($user['role'] === 'client'): ?>
                <div class="profile-details task-overview">
                    <h2><?php echo __('task_overview'); ?></h2>
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
                    <div class="quick-links"> <a href="taskes.php" class="btn btn-outline-primary">
                            <i class="fas fa-briefcase" title="<?php echo __('available_tasks'); ?>"></i> <?php echo __('available_tasks'); ?>
                        </a>
                        <a href="my_assignments.php" class="btn btn-outline-success">
                            <i class="fas fa-clipboard-check"></i> <?php echo __('my_assignments'); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            <div class="profile-details">
                <div class="section-header">
                    <h2><?php echo __('about_me'); ?></h2>
                    <button id="editProfileBtn" class="btn btn-primary">
                        <i class="fas fa-edit"></i> <?php echo __('edit_profile'); ?>
                    </button>
                </div>
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
            </div>
            <div class="profile-actions">
                <button class="btn btn-primary" onclick="openModal('editProfileModal')">
                    <i class="fas fa-user-edit"></i> <?php echo __("edit_profile"); ?>
                </button>
                <button class="btn btn-secondary" id="changePasswordButton">
                    <i class="fas fa-key"></i> <?php echo __("change_password"); ?>
                </button>
            </div>
        </div>
    </div>
    <?php include 'components/edit_profile_modal.php'; ?>
    <?php include 'components/change_password_modal.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                const firstInput = modal.querySelector('input, select, textarea');
                if (firstInput) {
                    setTimeout(() => firstInput.focus(), 100);
                }
            }
        }
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                const form = modal.querySelector('form');
                if (form) form.reset();
                const errorMessages = modal.querySelectorAll('.alert');
                errorMessages.forEach(el => el.classList.add('d-none'));
            }
        }
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                closeModal(event.target.id);
            }
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal-overlay');
                modals.forEach(modal => {
                    if (modal.style.display === 'flex') {
                        closeModal(modal.id);
                    }
                });
            }
        });
    </script>
    <script src="js/profile.js"></script>
    <script src="js/change_password.js"></script>
    <style>

    </style>
    <div class="modal-overlay" id="editProfileModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> <?php echo __('edit_profile'); ?></h2>
                <button class="modal-close" id="closeEditModal">&times;</button>
            </div>
            <form id="profileForm" action="api/update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name"><?php echo __('full_name'); ?></label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email"><?php echo __('email'); ?></label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone"><?php echo __('phone'); ?></label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="bio"><?php echo __('bio'); ?></label>
                    <textarea id="bio" name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="profile_picture"><?php echo __('profile_picture'); ?></label>
                    <div class="file-upload">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="file-input">
                        <label for="profile_picture" class="file-label">
                            <i class="fas fa-upload"></i> <?php echo __('choose_file'); ?>
                            <span id="file-name" class="file-name"><?php echo __('no_file_chosen'); ?></span>
                        </label>
                    </div>
                    <small class="text-muted"><?php echo __('max_file_size', ['size' => '5MB']); ?></small>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelEdit"><?php echo __('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo __('save_changes'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-overlay" id="changePasswordModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-key"></i> <?php echo __('change_password'); ?></h2>
                <button class="modal-close" id="closeChangePasswordModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm" action="api/change_password.php" method="POST" novalidate>
                    <div class="form-group">
                        <label for="currentPassword"><?php echo __('current_password'); ?></label>
                        <div class="input-group">
                            <input type="password" id="currentPassword" name="current_password" class="form-control" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#currentPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="error-message" id="currentPasswordError"></div>
                    </div>
                    <div class="form-group">
                        <label for="newPassword"><?php echo __('new_password'); ?></label>
                        <div class="input-group">
                            <input type="password" id="newPassword" name="new_password" class="form-control"
                                minlength="8"
                                title=""
                                autocomplete="new-password"
                                oninvalid="this.setCustomValidity('')"
                                oninput="this.setCustomValidity('')">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#newPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted" id="passwordRequirements">
                            <div class="password-requirement">
                                <i class="fas fa-info-circle"></i> Password must be at least 8 characters long
                            </div>
                        </small>
                        <div class="error-message" id="newPasswordError"></div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword"><?php echo __('confirm_new_password'); ?></label>
                        <div class="input-group">
                            <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#confirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="error-message" id="confirmPasswordError"></div>
                    </div>
                    <div id="changePasswordMessage" class="alert d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelChangePassword">
                    <i class="fas fa-times"></i> <?php echo __('cancel'); ?>
                </button>
                <button type="submit" form="changePasswordForm" class="btn btn-primary" id="changePasswordBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <i class="fas fa-save"></i> <?php echo __('update_password'); ?>
                </button>
            </div>
        </div>
    </div>
    <div class="profile-picture-modal" id="profilePictureModal">
        <div class="picture-modal-content">
            <button class="picture-modal-close" id="closePictureModal">&times;</button>
            <img src="" alt="<?php echo __('profile_picture'); ?>" id="modalProfilePicture">
            <div class="picture-caption"><?php echo htmlspecialchars($user['name'] ?? __('Profile Picture')); ?></div>
        </div>
    </div>
    <script>
        function showFixedAlert(message, type = 'success') {
            const existingAlert = document.getElementById('fixedAlert');
            if (existingAlert) {
                existingAlert.remove();
            }
            const alert = document.createElement('div');
            alert.id = 'fixedAlert';
            alert.className = `fixed-alert alert-${type}`;
            const icon = document.createElement('i');
            icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            alert.appendChild(icon);
            const messageSpan = document.createElement('span');
            messageSpan.textContent = message;
            alert.appendChild(messageSpan);
            document.body.appendChild(alert);
            void alert.offsetWidth;
            alert.style.opacity = '1';
            alert.style.transform = 'translateY(0)';
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }, 5000);
        }
        function validatePassword(password) {
            const isValid = password.length >= 8;
            const requirement = document.querySelector('.password-requirement');
            if (isValid) {
                requirement.classList.add('valid');
                requirement.innerHTML = '<i class="fas fa-check-circle"></i> Password is valid';
            } else {
                requirement.classList.remove('valid');
                requirement.innerHTML = '<i class="fas fa-info-circle"></i> Password must be at least 8 characters long';
            }
            return isValid;
        }
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordInput = document.getElementById('newPassword');
            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', function() {
                    validatePassword(this.value);
                });
            }
            document.addEventListener('click', function(e) {
                if (e.target.closest('.toggle-password')) {
                    const button = e.target.closest('.toggle-password');
                    const target = document.querySelector(button.getAttribute('data-target'));
                    const icon = button.querySelector('i');
                    if (target.type === 'password') {
                        target.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        target.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            });
            const changePasswordModal = document.getElementById('changePasswordModal');
            const closeChangePasswordBtn = document.getElementById('closeChangePasswordModal');
            const cancelChangePasswordBtn = document.getElementById('cancelChangePassword');
            const changePasswordForm = document.getElementById('changePasswordForm');
            const changePasswordBtn = document.getElementById('changePasswordBtn');
            const openChangePasswordModal = () => {
                changePasswordModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    changePasswordModal.classList.add('active');
                    document.getElementById('currentPassword').focus();
                }, 10);
            };
            const closeChangePasswordModal = () => {
                changePasswordModal.classList.remove('active');
                document.body.style.overflow = '';
                setTimeout(() => {
                    changePasswordModal.style.display = 'none';
                }, 300);
            };
            const changePasswordButton = document.getElementById('changePasswordButton');
            if (changePasswordButton) {
                changePasswordButton.addEventListener('click', openChangePasswordModal);
            }
            if (closeChangePasswordBtn) {
                closeChangePasswordBtn.addEventListener('click', closeChangePasswordModal);
            }
            if (cancelChangePasswordBtn) {
                cancelChangePasswordBtn.addEventListener('click', closeChangePasswordModal);
            }
            changePasswordModal.addEventListener('click', (e) => {
                if (e.target === changePasswordModal) {
                    closeChangePasswordModal();
                }
            });
            if (changePasswordForm) {
                changePasswordForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const currentPassword = formData.get('current_password');
                    const newPassword = formData.get('new_password');
                    const confirmPassword = formData.get('confirm_password');
                    let isValid = true;
                    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
                    document.getElementById('changePasswordMessage').classList.add('d-none');
                    if (!currentPassword) {
                        document.getElementById('currentPasswordError').textContent = 'Current password is required';
                        document.getElementById('currentPassword').classList.add('is-invalid');
                        isValid = false;
                    }
                    if (!newPassword) {
                        document.getElementById('newPasswordError').textContent = 'New password is required';
                        document.getElementById('newPassword').classList.add('is-invalid');
                        isValid = false;
                    } else if (newPassword.length < 8) {
                        document.getElementById('newPasswordError').textContent = 'Password must be at least 8 characters long';
                        document.getElementById('newPassword').classList.add('is-invalid');
                        isValid = false;
                    }
                    if (!confirmPassword) {
                        document.getElementById('confirmPasswordError').textContent = 'Please confirm your new password';
                        document.getElementById('confirmPassword').classList.add('is-invalid');
                        isValid = false;
                    } else if (newPassword !== confirmPassword) {
                        document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                        document.getElementById('confirmPassword').classList.add('is-invalid');
                        isValid = false;
                    }
                    if (!isValid) {
                        const firstError = document.querySelector('.is-invalid');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            firstError.focus();
                        }
                        return;
                    }
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';
                    fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err || {
                                        message: 'An error occurred. Please try again.'
                                    };
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const successMessage = data.message || 'Password changed successfully';
                                showFixedAlert(successMessage, 'success');
                                this.reset();
                                setTimeout(() => {
                                    closeChangePasswordModal();
                                }, 1500);
                            } else {
                                if (data.errors) {
                                    Object.entries(data.errors).forEach(([field, message]) => {
                                        const errorElement = document.getElementById(`${field}Error`);
                                        if (errorElement) {
                                            errorElement.textContent = Array.isArray(message) ? message[0] : message;
                                            const input = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
                                            if (input) input.classList.add('is-invalid');
                                        }
                                    });
                                } else if (data.message) {
                                    const errorDiv = document.getElementById('changePasswordMessage');
                                    errorDiv.textContent = data.message;
                                    errorDiv.className = 'alert alert-danger';
                                    errorDiv.classList.remove('d-none');
                                }
                                const firstError = document.querySelector('.is-invalid');
                                if (firstError) {
                                    firstError.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                    firstError.focus();
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            const errorDiv = document.getElementById('changePasswordMessage');
                            errorDiv.textContent = error.message || 'An error occurred. Please try again.';
                            errorDiv.className = 'alert alert-danger';
                            errorDiv.classList.remove('d-none');
                        })
                        .finally(() => {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                        });
                });
            }
            const profilePicture = document.getElementById('profilePicture');
            const modal = document.getElementById('profilePictureModal');
            const modalImg = document.getElementById('modalProfilePicture');
            const closeBtn = document.getElementById('closePictureModal');
            if (profilePicture) {
                profilePicture.addEventListener('click', function() {
                    modalImg.src = this.src;
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            }
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                    setTimeout(() => {
                        modal.classList.remove('active');
                    }, 300);
                });
            }
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                    setTimeout(() => {
                        modal.classList.remove('active');
                    }, 300);
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                    setTimeout(() => {
                        modal.classList.remove('active');
                    }, 300);
                }
            });
            const editModal = document.getElementById('editProfileModal');
            const editBtn = document.getElementById('editProfileBtn');
            const closeEditBtn = document.getElementById('closeEditModal');
            const cancelEditBtn = document.getElementById('cancelEdit');
            const profileForm = document.getElementById('profileForm');
            const fileInput = document.getElementById('profile_picture');
            const fileName = document.getElementById('file-name');
            if (editBtn) {
                editBtn.addEventListener('click', () => {
                    editModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        editModal.classList.add('active');
                    }, 10);
                });
            }
            function closeEditModal() {
                editModal.classList.remove('active');
                document.body.style.overflow = '';
                setTimeout(() => {
                    editModal.style.display = 'none';
                }, 300);
            }
            if (closeEditBtn) {
                closeEditBtn.addEventListener('click', closeEditModal);
            }
            if (cancelEditBtn) {
                cancelEditBtn.addEventListener('click', closeEditModal);
            }
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) {
                    closeEditModal();
                }
            });
            if (fileInput && fileName) {
                fileInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        fileName.textContent = file.name;
                    } else {
                        fileName.textContent = '<?php echo __('no_file_chosen'); ?>';
                    }
                });
            }
            if (profileForm) {
                profileForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(profileForm);
                    const submitBtn = profileForm.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    try {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php echo __('saving'); ?>...';
                        const response = await fetch(profileForm.action, {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();
                        if (result.success) {
                            alert('<?php echo __('profile_updated_successfully'); ?>');
                            window.location.reload();
                        } else {
                            alert(result.message || '<?php echo __('error_updating_profile'); ?>');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('<?php echo __('error_occurred'); ?>: ' + error.message);
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                });
            }
        });
    </script>
</body>

</html>