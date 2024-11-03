<?php
session_start();
require_once "../connection/config.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_GET['email'] ?? '';
$alertMessage = '';

// Verify OTP if POST request for OTP verification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['otp'])) {
    $otp = $_POST['otp'];

    // Prepare and execute statement to verify OTP
    $stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ? AND otp_code = ?");
    $stmt->bind_param("si", $email, $otp);
    
    if ($stmt->execute()) {
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['otp_verified'] = true; // Store OTP verification status in session
            $stmt->close();
            header("Location: new_password.php?email=" . urlencode($email));
            exit();
        } else {
            $alertMessage = "<div class='alert alert-danger'>Invalid OTP. Please try again.</div>";
        }
    } else {
        $alertMessage = "<div class='alert alert-danger'>Error executing query. Please try again later.</div>";
    }
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../Styles/non-user.css">
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center" style="height: 100vh; margin: 0;">
    <div class="container bg-secondary p-4 rounded shadow text-center" style="max-width: 400px;">
        <h2 class="mb-3">Verify OTP</h2>
        <?php if ($alertMessage): ?>
            <div class="mb-3">
                <?= $alertMessage ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <input type="text" name="otp" class="form-control bg-dark text-light" placeholder="Enter OTP" required>
            </div>
            <button type="submit" class="btn btn-custom w-100">Verify OTP</button>
        </form>
    </div>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
