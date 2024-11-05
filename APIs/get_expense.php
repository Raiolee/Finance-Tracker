<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../connection/config.php";

$expense_id = $_GET['id'] ?? null;

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

if ($expense_id) {
    $query = "SELECT subject, category, date, recurrence_type, merchant, bank, amount, description, reimbursable FROM expenses WHERE expense_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $expense_id, $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $expense = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($expense) {
        echo json_encode($expense);
    } else {
        echo json_encode(["error" => "Expense not found."]);
    }
}
