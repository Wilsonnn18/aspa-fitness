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
    plan_id INT NOT NULL UNIQUE,
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
    user_id INT NOT NULL,
    workout_plan_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    level ENUM('beginner','intermediate','advanced') DEFAULT 'beginner',
    goal ENUM('weight_loss','weight_gain','lean_muscle') DEFAULT 'weight_loss',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- example admin (create a real password using password_hash in PHP later)
-- INSERT INTO users (name,email,password) VALUES ('Admin','admin@example.com',
--     '$2y$10\$examplehashedpassword');

--
-- Dummy data for testing
--

INSERT INTO users (name,email,phone,password) VALUES
('John Doe','john@test.com','1112223333','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG'),
('Jane Smith','jane@test.com','4445556666','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG');

INSERT INTO admin (name,email,password) VALUES
('Admin User','admin@example.com','$2y$10$TKh8H1.PaW.PQl.MXc2Goe/0m6TqaG3L69162QG63kg70HJsJzHbG');

INSERT INTO membership_plans (plan_id,plan_name,duration,price,description) VALUES
(1,'Basic','30',1500,'Monthly access to gym equipment.'),
(2,'Standard','90',4000,'Quarterly plan with group classes.'),
(3,'Premium','365',10000,'Full year membership with personal trainer.');

INSERT INTO workout_plans (user_id,workout_plan_id,title,level,goal) VALUES
(1,1,'Beginner Fat Burn','beginner','weight_loss'),
(1,2,'Beginner Weight Loss Circuit','beginner','weight_loss'),
(1,3,'Beginner Cardio Start','beginner','weight_loss'),
(1,4,'Intermediate Bulk Builder','intermediate','weight_gain'),
(1,5,'Advanced Lean Shred','advanced','lean_muscle');

INSERT INTO payments (user_id,plan_id,amount,payment_status,transaction_id) VALUES
(2,1,2999,'completed','txn_1001'),
(2,2,7999,'completed','txn_1002'),
(3,1,2999,'completed','txn_1003');

