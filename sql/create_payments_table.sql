-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    client_id INT NOT NULL,
    executor_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATETIME NOT NULL,
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    transaction_id VARCHAR(255),
    payment_method VARCHAR(50),
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (client_id) REFERENCES users(id_user),
    FOREIGN KEY (executor_id) REFERENCES users(id_user),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add payment_status column to tasks table if it doesn't exist
ALTER TABLE tasks 
ADD COLUMN IF NOT EXISTS payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
ADD COLUMN IF NOT EXISTS payment_date DATETIME;
