# Task Management Platform

A bilingual (English/Arabic) web application for managing tasks between clients and executors. The platform allows users to post tasks, bid on tasks, manage assignments, and handle payments securely.

## Features

- Bilingual support (English/Arabic) with RTL layout
- User authentication and role management (Client/Executor)
- Task creation and management
- Bidding system
- Real-time messaging
- Payment processing
- Task status tracking
- Category-based task organization
- User profile management
- Responsive design

## Project Structure

```
PFE/
├── config/
│   ├── db.php              # Database configuration
│   └── languages.php       # Language configuration
├── components/
│   ├── head.php           # Common header components
│   └── header.php         # Navigation header
├── languages/
│   ├── en.php             # English translations
│   └── ar.php             # Arabic translations
├── style/
│   ├── style.css          # Main stylesheet
│   ├── dashboard.css      # Dashboard styles
│   └── login.css          # Login/Register styles
├── js/
│   ├── main.js            # Main JavaScript functions
│   └── dashboard.js       # Dashboard-specific functions
├── index.php              # Homepage
├── login.php              # Login page
├── register.php           # Registration page
├── dashboard.php          # User dashboard
├── taskes.php            # Available tasks listing
├── my_tasks.php          # User's posted tasks
├── my_assignments.php    # User's accepted tasks
├── pending_bids.php      # Pending bids management
├── related_tasks.php     # Related tasks listing
├── task_details.php      # Task details page
├── task_executors.php    # Task executors listing
└── messages.php          # Messaging system
```

## Database Schema

### Users Table

```sql
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('client', 'executor') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tasks Table

```sql
CREATE TABLE tasks (
    task_id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    budget DECIMAL(10,2),
    deadline DATETIME,
    status ENUM('posted', 'in_progress', 'completed', 'cancelled') DEFAULT 'posted',
    location VARCHAR(255),
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id_user),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);
```

### Categories Table

```sql
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT
);
```

### Bids Table

```sql
CREATE TABLE bids (
    bid_id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT,
    executor_id INT,
    amount DECIMAL(10,2),
    proposal TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (executor_id) REFERENCES users(id_user)
);
```

### Messages Table

```sql
CREATE TABLE messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT,
    receiver_id INT,
    task_id INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id_user),
    FOREIGN KEY (receiver_id) REFERENCES users(id_user),
    FOREIGN KEY (task_id) REFERENCES tasks(task_id)
);
```

## Key Functions

### Authentication Functions

- `login($email, $password)`: Authenticates user login
- `register($name, $email, $password, $phone)`: Creates new user account
- `logout()`: Ends user session

### Task Management Functions

- `createTask($title, $description, $category, $budget, $deadline, $location)`: Creates new task
- `updateTask($task_id, $data)`: Updates task details
- `deleteTask($task_id)`: Removes task
- `getTaskDetails($task_id)`: Retrieves task information
- `getRelatedTasks($category_id)`: Finds similar tasks

### Bidding Functions

- `submitBid($task_id, $executor_id, $amount, $proposal)`: Creates new bid
- `acceptBid($bid_id)`: Accepts bid for task
- `rejectBid($bid_id)`: Rejects bid
- `getPendingBids($task_id)`: Retrieves pending bids

### Assignment Functions

- `startTask($task_id)`: Marks task as in progress
- `completeTask($task_id)`: Marks task as completed
- `cancelTask($task_id, $reason)`: Cancels task assignment

### Messaging Functions

- `sendMessage($sender_id, $receiver_id, $task_id, $message)`: Sends message
- `getMessages($user_id)`: Retrieves user's messages
- `getTaskMessages($task_id)`: Gets messages for specific task

### Payment Functions

- `processPayment($task_id, $amount)`: Handles payment processing
- `confirmPayment($task_id)`: Confirms payment completion
- `getPaymentHistory($user_id)`: Retrieves payment records

## Language Support

The application supports both English and Arabic languages through a translation system:

1. Language files are stored in `languages/` directory
2. Translations are managed through PHP arrays
3. Language switching is handled via URL parameter
4. RTL support for Arabic language

### Translation Function

```php
function __($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}
```

## Setup Instructions

1. Clone the repository
2. Set up a local web server (e.g., XAMPP)
3. Create a MySQL database
4. Import the database schema
5. Configure database connection in `config/db.php`
6. Set up language files in `languages/` directory
7. Configure web server to point to project directory

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser with JavaScript enabled

## Security Features

- Password hashing using PHP's password_hash()
- SQL injection prevention using prepared statements
- XSS protection through output escaping
- CSRF protection
- Secure session management
- Input validation and sanitization

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
