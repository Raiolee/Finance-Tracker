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

        $alertMessage = "<div class='alert alert-success'>Your account is verified! You will be redirected in 5 seconds. <br> Didn't redirect? <a href='../login.php'>Click here</a>.</div>";
        // Script for redirection
        $alertMessage .= "<script>
            setTimeout(function() {
                window.location.href = '../login.php';
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

<head>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <title>Verify OTP</title>
</head>

<body class="bg-dark text-light d-flex align-items-center justify-content-center" style="height: 100vh; margin: 0;">
    <div class="container bg-secondary p-4 rounded shadow text-center" style="max-width: 400px;">
        <h2 class="mb-3">Enter OTP</h2>
        <form action="verify.php?email=<?= htmlspecialchars($email) ?>" method="POST">
            <div class="mb-3">
                <input type="text" name="otp" class="form-control bg-dark text-light" placeholder="Enter OTP" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>
        <div class="mt-3">
            <?= $alertMessage ?>
        </div>
    </div>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
