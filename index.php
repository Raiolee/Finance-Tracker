<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: User Interface/Dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="PENNY WISE" content="A Finance Tracker">
  <title>Penny Wise</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="Styles/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
</head>
<body class="container">
  <div class="left-section">
    <img src="Assets/PENNY_WISE_Logo.png" alt="Trulli" width="600" height="600" class="logo">
  </div>

  <div class="right-section">
    <div class="login-container">
    <form action="index.php" method="post">
      <p class="log-title">LOGIN</p>

      <?php
        if (isset($_POST["Login"])) {
            $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
            $password = $_POST["password"];
            require_once "connection/config.php";

            if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
          $sql = "SELECT * FROM user WHERE email = ?";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "s", $email);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);
          $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

          if ($user && password_verify($password, $user["Password"])) {
              $_SESSION["user"] = "yes";
              header("Location: User Interface/Dashboard.php");
              $_SESSION["name"] = $user["First_Name"] . ' ' . $user["Last_Name"];
              exit();
          } else {
              echo "<div class='alert alert-danger'>Invalid email or password.</div>";
          }
            } else {
          echo "<div class='alert alert-danger'>Invalid email or password.</div>";
            }
        }
      ?>
      
        <div class="email">
            <input type="email" placeholder="Email" name="email" id="email" required>
        </div>
        <div class="password">
            <input type="password" placeholder="Password" name="password" id="password" required>
        </div>
        <div class="form-btn">
            <input type="submit" value="Login" name="Login" class="login-btn">
        </div>
      </form>    
      <p class="dont-have">Don't have an account? <a class="forgot" href="register.php">Register</a></p>
      <a class="forgot" href="forgot-password.php">Forgot Password</a>
    </div>
  </div>
</body>
</html>
