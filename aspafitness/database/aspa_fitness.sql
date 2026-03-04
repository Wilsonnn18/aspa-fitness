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

-- admin table
CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- membership plans
CREATE TABLE IF NOT EXISTS membership_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    duration INT NOT NULL COMMENT 'duration in days',
    price DECIMAL(10,2) NOT NULL,
    description TEXT
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
    level ENUM('beginner','intermediate','advanced') DEFAULT 'beginner',
    goal ENUM('weight_loss','weight_gain','lean_muscle') DEFAULT 'weight_loss'
);

-- example admin (create a real password using password_hash in PHP later)
-- INSERT INTO users (name,email,password,role) VALUES ('Admin','admin@example.com',
--     '$2y$10\$examplehashedpassword', 'admin');

--
-- Dummy data for testing
--

INSERT INTO users (name,email,phone,password,role) VALUES
('John Doe','john@test.com','1112223333','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG','user'),
('Jane Smith','jane@test.com','4445556666','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG','user');

INSERT INTO admin (name,email,password) VALUES
('Admin User','admin@example.com','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG');

INSERT INTO membership_plans (plan_name,duration,price,description) VALUES
('Basic','30',1500,'Monthly access to gym equipment.'),
('Standard','90',4000,'Quarterly plan with group classes.'),
('Premium','365',10000,'Full year membership with personal trainer.');

INSERT INTO workout_plans (title,description,level,goal) VALUES
('Beginner Fat Burn','Low-impact cardio and bodyweight routine.','beginner','weight_loss'),
('Beginner Weight Loss Circuit','3 days/week full-body circuit with brisk walking and mobility work.','beginner','weight_loss'),
('Beginner Cardio Start','Low-impact treadmill, cycling, and core sessions for steady fat loss.','beginner','weight_loss'),
('Intermediate Bulk Builder','Strength split with progressive overload.','intermediate','weight_gain'),
('Advanced Lean Shred','Hybrid resistance and conditioning routine.','advanced','lean_muscle');

INSERT INTO payments (user_id,plan_id,amount,payment_status,transaction_id) VALUES
(2,1,2999,'completed','txn_1001'),
(2,2,7999,'completed','txn_1002'),
(3,1,2999,'completed','txn_1003');

