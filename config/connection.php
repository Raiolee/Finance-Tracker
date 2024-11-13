<?php
require ('constants.php');

// Establish the connection
$conn = mysqli_connect($DB_Host, $DB_User, $DB_Password, $DB_Name);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>