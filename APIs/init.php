<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
    exit();
}
$uid = $_SESSION["user_id"];
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
// Include database connection
include('../config/connection.php');
// Fetch only the user_dp (profile picture) from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
if ($user && $user['user_dp']) {
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    $profile_pic = '../Assets/blank-profile.webp';
}
?>
