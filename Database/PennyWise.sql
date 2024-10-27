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
    `user_dp` BLOB
);

CREATE TABLE `income` (
    `income_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` int not null,
    `date` DATE NOT NULL,
    `investment` VARCHAR(255) NOT NULL,
    `source` VARCHAR(255) NOT NULL,
    `total` DECIMAL(10, 2) NOT NULL,
    `currency` VARCHAR(10) NOT NULL,
    `category` ENUM('Monthly', 'Weekly', 'Yearly') NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `expenses` (
	`expense_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` int not null,
    `subject` varchar(255) not null,
    `date` DATE NOT NULL,
    `currency` varchar(10) NOT NULL,
    `reimbursable` enum('yes','no') NOT NULL DEFAULT 'no',
    `merchant` varchar(255) not null,
    `total` decimal(10, 2) not null,
    `description` text not null
);

create table `goals` (
	`goal_id` int primary key auto_increment,
    `user_id` int not null,
    `subject` varchar(255) not null,
    `start_date` date not null,
    `category` enum('Miscellaneous', 'Travels', 'Others') not null,
    `budget_limit` decimal(10,2) not null,
    `description` text not null
);

CREATE TABLE `savings` (
    `savings_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `category` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `balance` DECIMAL(10, 2) NOT NULL,
    `date` DATE NOT NULL,
    `bank` VARCHAR(255) NOT NULL
);