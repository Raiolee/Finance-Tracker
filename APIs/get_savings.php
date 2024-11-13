<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'init.php';

$bank_id = $_GET['id'] ?? null;

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

if ($bank_id) {
    $query = "SELECT purpose, bank, balance FROM bank WHERE bank_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $bank_id, $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $savings = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($savings) {
        echo json_encode($savings);
    } else {
        echo json_encode(["error" => "Savings record not found."]);
    }
} else {
    echo json_encode(["error" => "Invalid bank ID"]);
}
?>