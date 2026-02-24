-- SQL dump for ASPA Fitness database
-- run this in phpMyAdmin or via MySQL CLI

CREATE DATABASE IF NOT EXISTS aspa_fitness;
USE aspa_fitness;

-- users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- membership plans
CREATE TABLE IF NOT EXISTS membership_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    duration INT NOT NULL COMMENT 'duration in days',
    price DECIMAL(10,2) NOT NULL,
    description TEXT
);

-- subscriptions
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active','expired','cancelled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(id) ON DELETE CASCADE
);

-- payments
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('pending','completed','failed') DEFAULT 'completed',
    transaction_id VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(id) ON DELETE CASCADE
);

-- workout plans
CREATE TABLE IF NOT EXISTS workout_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    level ENUM('beginner','intermediate','advanced') DEFAULT 'beginner'
);

-- example admin (create a real password using password_hash in PHP later)
-- INSERT INTO users (name,email,password,role) VALUES ('Admin','admin@example.com',
--     '$2y$10\$examplehashedpassword', 'admin');

--
-- Dummy data for testing
--

INSERT INTO users (name,email,phone,password,role) VALUES
('Admin User','admin@example.com','1234567890','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG','admin'),
('John Doe','john@test.com','1112223333','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG','user'),
('Jane Smith','jane@test.com','4445556666','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG','user');

INSERT INTO membership_plans (plan_name,duration,price,description) VALUES
('Basic','30',29.99,'Monthly access to gym equipment.'),
('Standard','90',79.99,'Quarterly plan with group classes.'),
('Premium','365',299.99,'Full year membership with personal trainer.');

INSERT INTO workout_plans (title,description,level) VALUES
('Beginner Full Body','Simple full‑body routine for beginners.','beginner'),
('Intermediate Strength','Focus on compound lifts and progression.','intermediate'),
('Advanced HIIT','High intensity interval training for advanced users.','advanced');

INSERT INTO subscriptions (user_id,plan_id,start_date,end_date,status) VALUES
(2,1,'2026-01-01','2026-01-31','expired'),
(2,2,'2026-02-01','2026-05-02','active'),
(3,1,'2026-02-15','2026-03-17','active');

INSERT INTO payments (user_id,plan_id,amount,payment_status,transaction_id) VALUES
(2,1,29.99,'completed','txn_1001'),
(2,2,79.99,'completed','txn_1002'),
(3,1,29.99,'completed','txn_1003');
