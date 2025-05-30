<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Navigation Bar -->
<div class="nav-bar">
    <div class="logo"><a href="index.php">TASK</a></div>
    <div class="icon">
        <i class="fa-solid fa-bars"></i>
    </div>
    <div class="nav-links">
        <ul>
            <li>
                <a href="taskes.php">
                    <img src="./style/images/task.png" alt="Task icon" />Task</a>
            </li>
            <li>
                <a href="dashboard.php"><img
                        src="./style/images/dash.png"
                        alt="Dashboard icon" />Dashboard</a>
            </li>
            <li>
                <a href="#"><img src="./style/images/aboute.png" alt="About icon" />About
                    Us</a>
            </li>
        </ul>
    </div>
    <div class="join-nav">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </a>
        <?php else: ?>
            <a href="./register.php">Join Now</a>
        <?php endif; ?>
    </div>
</div>

<script>
    // Add mobile navigation and sticky header functionality
    document.addEventListener('DOMContentLoaded', function() {
        const navIcon = document.querySelector('.icon');
        const navBar = document.querySelector('.nav-bar');

        // Mobile menu toggle
        navIcon.addEventListener('click', function() {
            navBar.classList.toggle('mobile');
        });
        
        // Sticky header on scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navBar.classList.add('sticky');
            } else {
                navBar.classList.remove('sticky');
            }
        });
    });
</script>

<style>
    .logout-btn {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .logout-btn i {
        font-size: 16px;
    }

    .logout-btn:hover {
        background-color: transparent;
        border-color: #dc3545;
        color: #dc3545;
    }
</style>