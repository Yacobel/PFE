<?php
session_start();
require_once 'config/languages.php';
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = __("messages");
include 'components/head.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./style/dashboard.css">
    <link rel="stylesheet" href="./style/messages.css">
</head>

<body>
    <div class="container">
        <?php include 'components/header.php'; ?>

        <div class="dashboard-container">
            <div class="messages-header">
                <h1><i class="fas fa-comments"></i> <?php echo __("messages"); ?></h1>
                <button class="btn btn-primary" id="newMessageBtn">
                    <i class="fas fa-plus"></i> <?php echo __("new_message"); ?>
                </button>
            </div>

            <div class="messages-container">
                <!-- Conversations List -->
                <div class="conversations-sidebar">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="<?php echo __("search_conversations"); ?>">
                    </div>
                    
                    <div class="conversations-list">
                        <!-- Conversation Item -->
                        <div class="conversation-item active">
                            <div class="avatar">
                                <img src="images/default-avatar.png" alt="User">
                                <span class="online-status"></span>
                            </div>
                            <div class="conversation-details">
                                <div class="conversation-header">
                                    <h4>John Doe</h4>
                                    <span class="time">2h ago</span>
                                </div>
                                <p class="last-message">Hey, how are you doing?</p>
                                <span class="unread-count">3</span>
                            </div>
                        </div>

                        <!-- More conversation items -->
                        <div class="conversation-item">
                            <div class="avatar">
                                <img src="images/default-avatar.png" alt="User">
                            </div>
                            <div class="conversation-details">
                                <div class="conversation-header">
                                    <h4>Task: Website Development</h4>
                                    <span class="time">1d ago</span>
                                </div>
                                <p class="last-message">I've completed the homepage design</p>
                            </div>
                        </div>

                        <div class="conversation-item">
                            <div class="avatar">
                                <img src="images/default-avatar.png" alt="User">
                                <span class="online-status"></span>
                            </div>
                            <div class="conversation-details">
                                <div class="conversation-header">
                                    <h4>Sarah Johnson</h4>
                                    <span class="time">2d ago</span>
                                </div>
                                <p class="last-message">Thanks for your help with the project!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="chat-area">
                    <div class="chat-header">
                        <div class="chat-partner-info">
                            <div class="avatar">
                                <img src="images/default-avatar.png" alt="User">
                                <span class="online-status"></span>
                            </div>
                            <div>
                                <h3>John Doe</h3>
                                <p class="status">Online</p>
                            </div>
                        </div>
                        <div class="chat-actions">
                            <button class="btn-icon"><i class="fas fa-phone"></i></button>
                            <button class="btn-icon"><i class="fas fa-video"></i></button>
                            <button class="btn-icon"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>

                    <div class="messages-list">
                        <!-- Message from other user -->
                        <div class="message received">
                            <div class="avatar">
                                <img src="images/default-avatar.png" alt="User">
                            </div>
                            <div class="message-content">
                                <p>Hey there! How are you doing?</p>
                                <span class="time">10:30 AM</span>
                            </div>
                        </div>

                        <!-- Your message -->
                        <div class="message sent">
                            <div class="message-content">
                                <p>Hi! I'm doing great, thanks for asking. How about you?</p>
                                <span class="time">10:32 AM</span>
                            </div>
                        </div>

                        <!-- More messages -->
                        <div class="message received">
                            <div class="avatar">
                                <img src="images/default-avatar.png" alt="User">
                            </div>
                            <div class="message-content">
                                <p>I'm doing well too! Just working on that project we discussed.</p>
                                <span class="time">10:33 AM</span>
                            </div>
                        </div>
                    </div>

                    <div class="message-input-container">
                        <button class="btn-icon"><i class="fas fa-paperclip"></i></button>
                        <input type="text" placeholder="<?php echo __("type_a_message"); ?>" class="message-input">
                        <button class="btn-icon"><i class="far fa-smile"></i></button>
                        <button class="btn-send"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Message Modal -->
    <div class="modal-overlay" id="newMessageModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><?php echo __("new_message"); ?></h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-search">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="<?php echo __("search_users_or_tasks"); ?>">
                </div>
                <div class="modal-body-inner">
                    <!-- User list will be populated here -->
                    <div class="user-list">
                        <div class="user-item">
                            <div class="user-content">
                                <div class="avatar-container">
                                    <img src="images/default-avatar.png" alt="User" class="avatar">
                                    <div class="user-name">John Doe</div>
                                </div>
                                <div class="user-info">
                                    <div class="user-status">
                                        <span class="status-dot"></span>
                                        <span>Online</span>
                                    </div>
                                    <div class="last-message" title="Last message preview...">
                                        Last message preview...
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary"><?php echo __("message"); ?></button>
                        </div>
                        <div class="user-item">
                            <div class="user-content">
                                <div class="avatar-container">
                                    <img src="images/default-avatar.png" alt="User" class="avatar">
                                    <div class="user-name">Sarah Johnson</div>
                                </div>
                                <div class="user-info">
                                    <div class="user-status">
                                        <span class="status-dot"></span>
                                        <span>Online</span>
                                    </div>
                                    <div class="last-message" title="Last message preview...">
                                        Last message preview...
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary"><?php echo __("message"); ?></button>
                        </div>
                        <!-- More user items can be added here -->
                    </div>
                    
                    <!-- Empty state (hidden by default) -->
                    <div class="empty-state" style="display: none;">
                        <i class="fas fa-comment-dots"></i>
                        <h4>No users found</h4>
                        <p>Try adjusting your search or invite new users to join</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-modal"><?php echo __("cancel"); ?></button>
                <button class="btn btn-primary" id="startChatBtn" disabled>
                    <i class="fas fa-paper-plane"></i> <?php echo __("start_chat"); ?>
                </button>
            </div>
        </div>
    </div>

    <script src="js/messages.js"></script>
</body>
</html>
