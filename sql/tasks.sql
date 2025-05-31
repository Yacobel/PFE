-- Drop existing indexes if they exist
DROP INDEX IF EXISTS idx_category_tasks ON tasks;
DROP INDEX IF EXISTS idx_client_tasks ON tasks;
DROP INDEX IF EXISTS idx_executor_tasks ON tasks;
DROP INDEX IF EXISTS idx_status_tasks ON tasks;
DROP INDEX IF EXISTS idx_created_at_tasks ON tasks;

-- Create tasks table
CREATE TABLE IF NOT EXISTS tasks (
    task_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    client_id INT NOT NULL,
    executor_id INT DEFAULT NULL,
    budget DECIMAL(10,2) NOT NULL,
    deadline DATETIME NOT NULL,
    location VARCHAR(255) DEFAULT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    status ENUM('posted', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'posted',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    accepted_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    INDEX idx_category_tasks (category_id),
    INDEX idx_client_tasks (client_id),
    INDEX idx_executor_tasks (executor_id),
    INDEX idx_status_tasks (status),
    INDEX idx_created_at_tasks (created_at),
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (executor_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 