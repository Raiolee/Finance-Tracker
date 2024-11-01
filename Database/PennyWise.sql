-- Run overall for creation of new database 
DROP DATABASE IF EXISTS user_db;
CREATE DATABASE user_db;
USE user_db;

CREATE TABLE `user`
(
    `user_id` INT PRIMARY KEY AUTO_INCREMENT,
    `first_name` VARCHAR(50),
    `last_name` VARCHAR(50),
    `email` VARCHAR(50),
    `password` VARCHAR(255),
    `user_dp` BLOB,
    `otp_code` int default null,
    `is_verified` tinyint(1) default 0
);

CREATE TABLE `otp` (
    `otp_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `otp` VARCHAR(6) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
);

CREATE TABLE `income` (
    `income_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` int not null,
    `date` DATE NOT NULL,
    `source` VARCHAR(255) NOT NULL,
    `total` DECIMAL(10, 2) NOT NULL,
    `category` ENUM('Monthly', 'Weekly', 'Yearly') NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`bank` VARCHAR(255) NOT NULL
);

CREATE TABLE `expenses` (
	`expense_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `date` DATE NOT NULL,
    `next_occurrence` DATE,
    `frequency`  VARCHAR(255) NOT NULL,
    `recurrence_type` ENUM('weekly', 'monthly', 'custom') NOT NULL,
    `category` VARCHAR(255) NOT NULL,
    `reimbursable` ENUM('yes','no') NOT NULL DEFAULT 'no',
    `merchant` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `description` TEXT NOT NULL,
    `receipt` BLOB
);

create table `goals` (
	`goal_id` int primary key auto_increment,
    `user_id` int not null,
    `subject` varchar(255) not null,
    `start_date` date not null,
    `date`date not null,
    `category` varchar(255) not null,
    `budget_limit` decimal(10,2) not null,
    `description` text not null
);

CREATE TABLE `savings` (
    `savings_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `category` VARCHAR(255) NOT NULL,
    `savings_amount` DECIMAL(10, 2) NOT NULL,
    `date` DATE NOT NULL,
    `bank` VARCHAR(255) NOT NULL
);



CREATE TABLE `bank` (
    `bank_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `user_bank_id` INT NOT NULL,
    `bank` VARCHAR(255) NOT NULL,
    `balance`  DECIMAL(10, 2) Default 0.00,
    `date` DATE NOT NULL
);
-- Create the trigger to automatically set user_entry_count
DELIMITER //

CREATE TRIGGER before_insert_orders
BEFORE INSERT ON `bank`
FOR EACH ROW
BEGIN
    SET NEW.`user_bank_id` = (SELECT COUNT(*) + 1 FROM `bank` WHERE `user_id` = NEW.`user_id`);
END //

DELIMITER ;

INSERT INTO bank (user_id, bank, balance, date) VALUES
(1, 'PNC Bank', 10000, '2024-05-30');

