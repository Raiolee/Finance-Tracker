<?php
$DB_Host = "Localhost";
$DB_User = "root";
$DB_Password = "F!nanceTrack3r";
$DB_Name = "user_db";

    $conn = mysqli_connect($DB_Host, $DB_User, $DB_Password, $DB_Name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
?>