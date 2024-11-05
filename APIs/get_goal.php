<?php
session_start();
require '../connection/config.php';

$goal_id = $_GET['id'] ?? null;

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

if ($goal_id) {
    $query = "SELECT * FROM goals WHERE goal_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $goal_id, $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $goal = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($goal) {
        echo json_encode($goal);
    } else {
        echo json_encode(["error" => "Goal record not found."]);
    }
} else {
    echo json_encode(["error" => "Invalid goal ID"]);
}
?>