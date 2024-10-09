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
