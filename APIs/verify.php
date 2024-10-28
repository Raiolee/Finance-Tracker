<?php
require_once "../connection/config.php";
$email = $_GET['email'] ?? '';
$alertMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ? AND otp_code = ?");
    $stmt->bind_param("si", $email, $otp);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // OTP is correct; update `is_verified`
        $stmt->close();
        $stmt = $conn->prepare("UPDATE user SET is_verified = 1, otp_code = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $alertMessage = "<div class='alert alert-success'>Your account is verified! You will be redirected in 5 seconds. <br> Didn't redirect? <a href='../index.php'>Click here</a>.</div>";
        // Script for redirection
        $alertMessage .= "<script>
            setTimeout(function() {
                window.location.href = '../index.php';
            }, 5000);
        </script>";
    } else {
        $alertMessage = "<div class='alert alert-danger'>Invalid OTP. Please try again.</div>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head><title>Verify OTP</title></head>
<body>
    <div class="container">
        <h2>Enter OTP</h2>
        <form action="verify.php?email=<?= htmlspecialchars($email) ?>" method="POST">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit">Verify</button>
        </form>
        <?= $alertMessage ?>
    </div>
</body>
</html>
