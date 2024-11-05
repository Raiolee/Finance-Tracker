<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../connection/config.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}


$uid = $_SESSION["user_id"];

// Prepare SQL to fetch expenses grouped by category
$sql = "SELECT subject, SUM(amount) as total_expense FROM expenses WHERE user_id = ? GROUP BY subject";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Close the connection and return data as JSON
$stmt->close();
$conn->close();
echo json_encode($data);
?>
