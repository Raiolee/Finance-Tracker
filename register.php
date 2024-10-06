<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: User Interface/Dashboard.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PennyWise</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
</head>

<body class="container">
    <div class="register-left-section">
        <img src="logo/PENNY_WISE_Logo.png" alt="Penny Wise Logo" width="200" height="200" class="logo1">
    </div>
    <div class="register-right-section">
        <div class="register-container">
            <p class="reg-title">REGISTER</p>

            <?php
                $alertMessage = '';

                if (isset($_POST["submit"])) {
                    $fname = trim($_POST["fname"]);
                    $lname = trim($_POST["lname"]);
                    $email = trim($_POST["email"]);
                    $password = $_POST["password"];
                    $confirmPassword = $_POST["Confirmpassword"];
                    
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $errors = [];

                    // Validate inputs
                    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirmPassword)) {
                        $errors[] = "All fields are required";
                    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Email is not valid";
                    } elseif (strlen($password) < 8) {
                        $errors[] = "Password must be at least 8 characters";
                    } elseif ($password !== $confirmPassword) {
                        $errors[] = "Passwords do not match";
                    }

                    require_once "connection/config.php";

                    // Check if email exists
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_registration_data WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $stmt->bind_result($rowCount);
                    $stmt->fetch();
                    $stmt->close();

                    if ($rowCount > 0) {
                        $errors[] = "Email already exists!";
                    }

                    // Handle errors or insert user
                    if ($errors) {
                        foreach ($errors as $error) {
                            $alertMessage .= "<div class='alert alert-danger'>$error</div>";
                        }
                    } else {
                        $stmt = $conn->prepare("INSERT INTO user_registration_data (First_Name, Last_Name, Email, Password) VALUES (?, ?, ?, ?)");
                        if ($stmt) {
                            $stmt->bind_param("ssss", $fname, $lname, $email, $passwordHash);
                            $stmt->execute();
                            $alertMessage .= "<div class='alert alert-success'>You are registered successfully.</div>";
                            $stmt->close();
                        } else {
                            $alertMessage .= "<div class='alert alert-danger'>Something went wrong</div>";
                        }
                    }  
                }
            ?>

            
            <div class="alert-area">
                <?php echo $alertMessage; ?>
            </div>

            <form action="register.php" method="post">
                <div class="first">
                    <div class="firstname">
                        <input type="text" name="fname" placeholder="First Name" id="fname">
                    </div>
                    <div class="lastname">
                        <input type="text" name="lname" placeholder="Last Name" id="lname">
                    </div>
                </div>

                <div class="register-email">
                    <input type="text" name="email" placeholder="Email" id="reg-email">
                </div>

                <div class="password-email">
                    <input type="password" name="password" placeholder="Enter Password" id="reg-password">
                </div>

                <div class="password-email">
                    <input type="password" name="Confirmpassword" placeholder="Confirm Password" id="reg-password">
                </div>

                <input type="submit" class="register" value="Register" name="submit">
                <p class="already-have">Already have an account? <a class="forgot" href="index.php">Login</a></p>
            </form>
        </div>
    </div>
    <div class="section"></div>
</body>
</html>
