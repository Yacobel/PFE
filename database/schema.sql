-- Database Schema for Task Management Platform
-- Based on MCD (Modèle Conceptuel de Données) design

-- Drop tables if they exist to avoid conflicts
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS bids;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

-- Create roles table
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    bio TEXT,
    profile_picture VARCHAR(255),
    role VARCHAR(20) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    verification_token VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Create categories table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create tasks table
CREATE TABLE tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    budget DECIMAL(10,2) NOT NULL,
    deadline DATE NOT NULL,
    client_id INT NOT NULL,
    executor_id INT NULL,
    status ENUM('posted', 'in_progress', 'completed', 'cancelled') DEFAULT 'posted',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT NULL,
    location VARCHAR(255),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (client_id) REFERENCES users(id_user),
    FOREIGN KEY (executor_id) REFERENCES users(id_user)
);

-- Create bids table
CREATE TABLE bids (
    bid_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    executor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    proposal TEXT NOT NULL,
    estimated_days INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'cancelled', 'done') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (executor_id) REFERENCES users(id_user),
    UNIQUE KEY unique_bid (task_id, executor_id)
);

-- Create messages table
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    task_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id_user),
    FOREIGN KEY (receiver_id) REFERENCES users(id_user),
    FOREIGN KEY (task_id) REFERENCES tasks(task_id)
);

-- Create payments table
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    bid_id INT NOT NULL,
    client_id INT NOT NULL,
    executor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (bid_id) REFERENCES bids(bid_id),
    FOREIGN KEY (client_id) REFERENCES users(id_user),
    FOREIGN KEY (executor_id) REFERENCES users(id_user)
);

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrator with full access'),
('client', 'User who can create tasks and hire freelancers'),
('freelancer', 'User who can bid on and complete tasks');

-- Insert default categories
INSERT INTO categories (name, description, icon) VALUES
('Web Development', 'Website creation, maintenance, and optimization', 'fa-globe'),
('Mobile Development', 'Mobile app development for iOS and Android', 'fa-mobile-alt'),
('Graphic Design', 'Logo design, branding, illustrations, and visual content', 'fa-paint-brush'),
('Content Writing', 'Blog posts, articles, copywriting, and content creation', 'fa-pen'),
('Digital Marketing', 'SEO, social media marketing, and online advertising', 'fa-bullhorn'),
('Video Editing', 'Video production, editing, and animation', 'fa-video'),
('Data Entry', 'Data processing, spreadsheet management, and organization', 'fa-database'),
('Translation', 'Language translation and localization services', 'fa-language'),
('Virtual Assistant', 'Administrative support, scheduling, and customer service', 'fa-user-tie'),
('Other', 'Other services not listed in the categories above', 'fa-ellipsis-h');

-- Create indexes for performance optimization
CREATE INDEX idx_tasks_client ON tasks(client_id);
CREATE INDEX idx_tasks_executor ON tasks(executor_id);
CREATE INDEX idx_tasks_category ON tasks(category_id);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_bids_task ON bids(task_id);
CREATE INDEX idx_bids_executor ON bids(executor_id);
CREATE INDEX idx_bids_status ON bids(status);
CREATE INDEX idx_messages_sender ON messages(sender_id);
CREATE INDEX idx_messages_receiver ON messages(receiver_id);
CREATE INDEX idx_payments_task ON payments(task_id);
