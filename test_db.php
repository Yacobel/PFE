<?php
require_once 'config/db.php';

header('Content-Type: text/plain');

try {
    // Test database connection
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!\n\n";
    
    // Check users table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Users table columns:\n";
    print_r($columns);
    
    // Check if we can update a user
    $testEmail = 'test@example.com';
    $testName = 'Test User';
    
    $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE email = ?");
    $result = $stmt->execute([$testName, $testEmail]);
    
    echo "\nTest update result: " . ($result ? 'Success' : 'Failed') . "\n";
    echo "Rows affected: " . $stmt->rowCount() . "\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

// Show the debug log if it exists
if (file_exists('update_profile_debug.log')) {
    echo "\n\nDebug log contents:\n";
    echo file_get_contents('update_profile_debug.log');
}
?>
