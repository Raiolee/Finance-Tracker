<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}
$username = $_SESSION["name"];
$user_id = $_SESSION["user_id"];
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../connection/config.php';

$sql = "SELECT subject, start_date FROM goals WHERE user_id = ? AND start_date >= CURDATE() AND date != '0000-00-00' ORDER BY start_date ASC LIMIT 5";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pending_goals[] = [
            'subject' => $row['subject'],
            'start_date' => $row['start_date'],
        ];
    }
}
$stmt->close();
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



