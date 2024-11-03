<?php 
session_start();
require_once "connection/config.php"; // Ensure this path is correct
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$alertMessage = '';

if (isset($_POST["submit"])) {
    $email = trim($_POST["email"]);
    $errors = [];

    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is not valid";
    }

    if (empty($errors)) {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Generate OTP
            $otp = rand(100000, 999999);

            // Update OTP in the database
            $stmt->close();
            $stmt = $conn->prepare("UPDATE user SET otp_code = ? WHERE email = ?");
            $stmt->bind_param("is", $otp, $email);
            $stmt->execute();
            $stmt->close();

            // Send OTP email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Host = 'smtp.gmail.com';
                $mail->Username = 'financesampleemail@gmail.com'; // Your Gmail address
                $mail->Password = 'sudc qzmf dksg jzou'; // Your App Password here
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable SSL encryption
                $mail->Port = 465; 

                $mail->setFrom('financesampleemail@gmail.com', 'PennyWise');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code for Password Reset';
                $mail->Body = "<p>Your OTP code is <strong>$otp</strong></p><p>Enter this code to reset your password.</p>";

                $mail->send();
                header("Location: APIs/forgot.php?email=" . urlencode($email));
                exit(); // Make sure to exit after header redirect
            } catch (Exception $e) {
                $alertMessage .= "<div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            $errors[] = "No account found with that email address.";
        }
    }

    // Display errors
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/non-user.css">
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center">
    <div class="container p-4 rounded shadow text-center">
        <h2 class="mb-3">Forgot Password</h2>
        <div class="alert-area">
            <?php echo $alertMessage; ?>
        </div>
        <form action="forgot_password.php" method="post">
            <input class="form-control var-input text-light mb-3" type="email" name="email" placeholder="Enter your Email" required>
            <button type="submit" name="submit" class="btn btn-custom w-100 mb-3">Send OTP</button>
            <p>Remembered your password? <a href="login.php">Log in</a></p>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
