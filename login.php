<?php
session_start();
// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION["user"])) {
    header("Location: User Interface/Dashboard.php");
    exit();
}

if (isset($_POST["Login"])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    require_once "connection/config.php";

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        // Prepare and execute SQL query to fetch user by email
        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

        // Check if user exists and password matches
        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user"] = "yes"; // Mark user as logged in
            $_SESSION["user_id"] = $user["user_id"]; // Store the user ID
            $_SESSION["name"] = $user["first_name"] . ' ' . $user["last_name"]; // Store full name          
            header("Location: User Interface/Dashboard.php"); // Redirect to Dashboard
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/non-user.css">
</head>

<body class="bg-dark text-light d-flex align-items-center justify-content-center font">
    <div class="container p-4 rounded shadow text-center">
        <h2 class="mb-3">LOGIN</h2>
        <?php if (isset($error_message)): ?>
            <div class='alert alert-danger'><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form class="column" action="" method="POST">
            <input class="form-control bg-dark text-light" type="email" placeholder="Email" name="email" id="email" required></input>
            <input class="form-control bg-dark text-light" type="password" placeholder="Password" name="password" id="password" required></input>
            <button type="submit" value="Login" name="Login" class="btn btn-custom w-100 mb-3">Log in</button>
            <p>Don't have an account? <a href="register.php">Register</a></p>
            <a class="font" href="forgot-password.php">Forgot Password</a>
        </form>
    </div>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>