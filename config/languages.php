<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default language to English if not set
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Allow language switching via GET parameter
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'];

// Load the appropriate language file
$langFile = __DIR__ . "/../languages/{$lang}.php";
if (file_exists($langFile)) {
    require_once $langFile;
} else {
    // Fallback to English if the requested language file doesn't exist
    require_once __DIR__ . "/../languages/en.php";
}

// Function to get translated text
function __($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}
?> 