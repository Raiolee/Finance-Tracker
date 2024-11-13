<?php
session_start();
require_once "config/connection.php";
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
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/non-user.css">
</head>

<body class="bg-dark text-light d-flex align-items-center justify-content-center">
    <div class="container p-4 rounded shadow text-center">
        <h2 class="mb-3">Register</h2>
        <div class="alert-area">
            <?php echo $alertMessage; ?>
        </div>
        <form action="register.php" method="post">
            <input class="form-control var-input text-light mb-3" type="text" name="fname" placeholder="First Name" required>
            <input class="form-control var-input text-light mb-3" type="text" name="lname" placeholder="Last Name" required>
            <input class="form-control var-input text-light mb-3" type="email" name="email" placeholder="Email" required>
            <input class="form-control var-input text-light mb-3" type="password" name="password" placeholder="Password" required>
            <input class="form-control var-input text-light mb-3" type="password" name="Confirmpassword" placeholder="Confirm Password" required>
            <button type="submit" name="submit" class="btn btn-custom w-100 mb-3">Register</button>
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>