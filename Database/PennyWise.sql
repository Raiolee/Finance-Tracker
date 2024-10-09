DROP DATABASE IF EXISTS user_db;
CREATE DATABASE user_db;
USE user_db;

CREATE TABLE User_Registration_Data 
(
    UID INT PRIMARY KEY AUTO_INCREMENT,
    First_Name VARCHAR(50),
    Last_Name VARCHAR(50),
    Email VARCHAR(50),
    Password VARCHAR(255)
);

CREATE TABLE incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    investment VARCHAR(255) NOT NULL,
    source VARCHAR(255) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) NOT NULL,
    category ENUM('Monthly', 'Weekly', 'Yearly') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);