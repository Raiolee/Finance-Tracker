<?php
$DB_Host = "Localhost";
$DB_User = "root";
$DB_Password = ""; // (F!nanceTrack3r) Rai's Password for the database
$DB_Name = "user_db";

// Establish the connection
$conn = mysqli_connect($DB_Host, $DB_User, $DB_Password, $DB_Name);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>