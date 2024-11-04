<?php
session_start();
require '../connection/config.php';

$income_id = $_GET['id'] ?? null;

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

if ($income_id) {
    $query = "SELECT source, bank, total, category, date, description FROM income WHERE income_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $income_id, $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $income = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($income) {
        echo json_encode($income);
    } else {
        echo json_encode(["error" => "Income record not found."]);
    }
} else {
    echo json_encode(["error" => "Invalid income ID"]);
}
?>