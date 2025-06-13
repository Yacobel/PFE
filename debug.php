<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if debug log exists
$log_file = __DIR__ . '/debug.log';

if (file_exists($log_file)) {
    echo '<h2>Debug Log:</h2>';
    echo '<pre>' . htmlspecialchars(file_get_contents($log_file)) . '</pre>';
} else {
    echo 'No debug log found. The log file should be at: ' . htmlspecialchars($log_file);
}

// Show last 20 lines of PHP error log
$php_log = 'C:/xampp/php/logs/php_error_log';
if (file_exists($php_log)) {
    echo '<h2>PHP Error Log (last 20 lines):</h2>';
    $log_content = `tail -n 20 "$php_log"`;
    echo '<pre>' . htmlspecialchars($log_content) . '</pre>';
}

// Show session data
echo '<h2>Session Data:</h2>';
session_start();
echo '<pre>' . print_r($_SESSION, true) . '</pre>';

// Show POST data
if (!empty($_POST)) {
    echo '<h2>POST Data:</h2>';
    echo '<pre>' . print_r($_POST, true) . '</pre>';
}

// Show FILES data
if (!empty($_FILES)) {
    echo '<h2>FILES Data:</h2>';
    echo '<pre>' . print_r($_FILES, true) . '</pre>';
}
?>
