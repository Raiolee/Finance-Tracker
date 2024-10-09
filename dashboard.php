<?php
session_start();
if(!isset($_SESSION["user"]))
{
    header("Location: Login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/style.css">
    <title>User Dashboards</title>
</head>
<body>
    <div class="container">
    <a href="logout.php" class="btn btn-warning">Logout</a>
    </div>
    
</body>
</html>