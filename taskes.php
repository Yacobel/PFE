<?php
require_once 'config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __("tasks"); ?> - <?php echo __("task_platform"); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./style/header.css">
    <link rel="stylesheet" href="./style/footer.css">
    <link rel="stylesheet" href="./style/taskes.css">
</head>

<body>
    <div class="language-selector">
        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
        <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>
    <div class="main-container">
        <?php include 'components/header.php'; ?>
        <main class="tasks-container">
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");
                exit;
            }
            if (isset($_GET['error'])) {
                echo '<div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    ' . htmlspecialchars(urldecode($_GET['error'])) . '
                </div>';
            }
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "task_platform";
            try {
                $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                $pdo = new PDO($dsn, $username, $password, $options);
                $stmt = $pdo->prepare("
                    SELECT t.*, c.name as category_name, u.name as creator_name, u.id_user as creator_id
                    FROM tasks t 
                    LEFT JOIN categories c ON t.category_id = c.category_id 
                    LEFT JOIN users u ON t.client_id = u.id_user
                    WHERE t.status = 'Posted' 
                    AND t.client_id != :current_user_id
                    ORDER BY t.created_at DESC
                ");
                $stmt->bindParam(':current_user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                $tasks = $stmt->fetchAll();
                if (count($tasks) > 0) {
            ?>
                    <div class="tasks-header">
                        <h1><?php echo __("available_courses"); ?></h1>
                        <p><?php echo __("browse_courses_message"); ?></p>
                    </div>
                    <div class="task-grid">
                        <?php
                        foreach ($tasks as $row) {
                            $title = htmlspecialchars($row['title']);
                            $category = htmlspecialchars($row['category_name']);
                            $image_url = !empty($row['image_url']) ? $row['image_url'] : "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&h=500&fit=crop";
                        ?>
                            <div class="task-card">
                                <div class="card-image-container">
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo $title; ?>" class="card-image">
                                </div>
                                <div class="card-content">
                                    <div class="card-info">
                                        <span class="card-category"><?php echo $category; ?></span>
                                        <h3 class="card-title"><?php echo $title; ?></h3>
                                        <?php if (isset($row['creator_name'])): ?>
                                            <span class="card-creator"><?php echo __("posted_by"); ?>: <?php echo htmlspecialchars($row['creator_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="task_details.php?id=<?php echo $row['task_id']; ?>" class="see-task-btn">
                                        <i class="fas fa-eye"></i> <?php echo __("see_details"); ?>
                                    </a>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                <?php
                } else {
                ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h2><?php echo __("no_courses_available"); ?></h2>
                        <p><?php echo __("no_courses_message"); ?></p>
                    </div>
            <?php
                }
            } catch (PDOException $e) {
                echo '<div class="error-message">' . __("database_error") . ': ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
        </main>
    </div>
    <script src="js/main.js"></script>
</body>

</html>