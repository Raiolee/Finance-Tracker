<?php
session_start();
require_once "connection/config.php";
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$alertMessage = '';

if (isset($_POST["submit"])) {
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["Confirmpassword"];

    $errors = [];

    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirmPassword)) {
        $errors[] = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is not valid";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($rowCount);
        $stmt->fetch();
        $stmt->close();

        if ($rowCount > 0) {
            $errors[] = "Email already exists!";
        } else {
            // Generate OTP and hash password
            $otp = rand(100000, 999999);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $defaultProfilePicturePath = 'Assets/blank-profile.webp';
            $profilePicture = file_get_contents($defaultProfilePicturePath);

            // Insert user with OTP
            $stmt = $conn->prepare("INSERT INTO user (first_name, last_name, email, password, user_dp, otp_code) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $fname, $lname, $email, $passwordHash, $profilePicture, $otp);
            $stmt->send_long_data(4, $profilePicture);
            $stmt->execute();
            $stmt->close();

            // Send OTP email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Host = "smtp.gmail.com";
                $mail->Username = "financesampleemail@gmail.com";
                $mail->Password = "sudc qzmf dksg jzou";
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('financesampleemail@gmail.com', 'PennyWise');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code for PennyWise';
                $mail->Body = "<p>Your OTP code is <strong>$otp</strong></p><p>Enter this code to verify your account.</p>";

                $mail->send();
                header("Location: APIs/verify.php?email=" . urlencode($email));
                exit();
            } catch (Exception $e) {
                $alertMessage .= "<div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
            }
        }
    }

    if ($errors) {
        foreach ($errors as $error) {
            $alertMessage .= "<div class='alert alert-danger'>$error</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PennyWise</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/styles.scss">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
</head>

<body class="container">
    <div class="register">
        <div class="register-left-section">
            <img src="Assets/PENNY_WISE_Logo.png" alt="Penny Wise Logo" width="200" height="200" class="logo1">
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

                    if (empty($errors)) {
                        // Check if email already exists
                        if ($stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE email = ?")) {
                            $stmt->bind_param("s", $email);
                            $stmt->execute();
                            $stmt->bind_result($rowCount);
                            $stmt->fetch();
                            $stmt->close();

                            if ($rowCount > 0) {
                                $errors[] = "Email already exists!";
                            }
                        } else {
                            $errors[] = "Database error: Unable to prepare statement";
                        }
                    }

                    // Handle errors or insert user
                    if ($errors) {
                        foreach ($errors as $error) {
                            $alertMessage .= "<div class='alert alert-danger'>$error</div>";
                        }
                    } else {
                        // Set default profile picture
                        $defaultProfilePicturePath = 'Assets/blank-profile.webp';
                        $profilePicture = file_get_contents($defaultProfilePicturePath);

                        // Insert new user
                        if ($stmt = $conn->prepare("INSERT INTO user (first_name, last_name, email, password, user_dp) VALUES (?, ?, ?, ?, ?)")) {
                            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                            $stmt->bind_param("sssss", $fname, $lname, $email, $passwordHash, $profilePicture);
                            $stmt->send_long_data(4, $profilePicture); // Send profile picture as BLOB
                            $stmt->execute();
                            $stmt->close();

                            $sql = "SELECT * FROM user WHERE email = ?";
                            $stmt = mysqli_prepare($conn, $sql);
                            mysqli_stmt_bind_param($stmt, "s", $email);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

                            // Check if user exists and password matches
                            if ($user && password_verify($password, $user["password"])) {
                                session_start();
                                $_SESSION["user"] = "yes"; // Mark user as logged in
                                $_SESSION["user_id"] = $user["user_id"]; // Store the user ID
                                $_SESSION["name"] = $user["first_name"] . ' ' . $user["last_name"]; // Store full name          
                                header("Location: User Interface/Dashboard.php"); // Redirect to Dashboard
                                exit();
                            } else {
                                $alertMessage .= "<div class='alert alert-danger'>Something went wrong</div>";
                            }
                        } else {
                            $alertMessage .= "<div class='alert alert-danger'>Something went wrong</div>";
                        }
                    }

                    // Close the database connection
                    $conn->close();
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

                    <input type="submit" id="register" value="Register" name="submit">
                    <p class="already-have">Already have an account? <a class="forgot" href="index.php">Login</a></p>
                </form>
            </div>
        </div>
        <div class="section"></div>
    </div>
</body>

</html>