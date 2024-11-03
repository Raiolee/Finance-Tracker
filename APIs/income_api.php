<?php

session_start();
$user_id = $_SESSION['user_id'];
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);

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

function searchIncome($conn, $userId, $IncomesearchQuery)
{
    $sql = "SELECT * FROM income WHERE user_id = ? AND (source LIKE ? OR date LIKE ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $likeQuery = "%{$IncomesearchQuery}%";
        $stmt->bind_param("iss", $userId, $likeQuery, $likeQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

$userId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incomeId'])) {
    $incomeId = $_POST['incomeId'];

    $sql = "DELETE FROM income WHERE income_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("i", $incomeId);

        // Start the transaction
        $conn->begin_transaction();

        // Execute the first statement
        if ($stmt->execute()) {
            $total = $_POST['incomeTotal']; // Assuming the total amount is passed in the POST request
            $uid = $_SESSION['user_id'];
            $bank = $_POST['incomeBank']; // Assuming the bank name is passed in the POST request

            echo "Total: $total, User ID: $uid, Bank: $bank";

            // Prepare the update statement
            $updateStmt = $conn->prepare("UPDATE bank SET balance = balance - ? WHERE user_id = ? AND bank = ?");
            if ($updateStmt === false) {
                $conn->rollback();
                throw new Exception("Prepare failed: {$conn->error}");
            }

            // Bind parameters to the update statement
            $updateStmt->bind_param("dis", $total, $uid, $bank);

            // Execute the update statement
            if (!$updateStmt->execute()) {
                $conn->rollback();
                throw new Exception("Execute failed: {$updateStmt->error}");
            }

            // Commit transaction
            $conn->commit();
        } else {
            $conn->rollback();
            throw new Exception("Execute failed for the first statement: {$stmt->error}");
        }
    } else {
        echo "Error preparing statement: {$conn->error}";
    }
}

function sortIncomeByDate($conn, $userId, $sortOrder)
{
    $sql = "SELECT * FROM income WHERE user_id = ? ORDER BY date " . ($sortOrder === 'asc' ? 'ASC' : 'DESC');
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

if (isset($_GET['sortIncomeDate'])) {
    $sortOrder = $_GET['sortIncomeDate'];
    $nextSortOrderDate = $sortOrder === 'asc' ? 'desc' : 'asc';
    try {
        $result = sortIncomeByDate($conn, $user_id, $sortOrder);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
} else {
    $nextSortOrderDate = 'asc';
}

if (isset($_POST['submitIncome'])) {
    $uid = $_SESSION['user_id']; // Define $uid here
    $date = $_POST['date'] ?? '';
    $source = $_POST['source'] ?? '';
    $total = $_POST['income-amount'] ?? '';
    $category = $_POST['income_category'] ?? '';
    $bank = $_POST['bank_name'] ?? '';
    $description = $_POST['income-description'] ?? '';
    // Validate required fields
    if (empty($date) || empty($source) || empty($total) || empty($category) || empty($bank) || empty($description)) {
        $error_message = "Please fill in all fields.";
    } else {
        try {
            // Prepare and execute the insert statement
            $sql = "INSERT INTO income (user_id, date, source, total, category, description, bank) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // Use 'd' for double (for budget limit) and 'i' for integer (for user_id)
            $stmt->bind_param("issssss", $uid, $date, $source, $total, $category, $description, $bank);
            if ($stmt->execute()) {
                $updateStmt = $conn->prepare("UPDATE bank SET balance = balance + ? WHERE user_id = ? AND bank = ?");
                if ($updateStmt === false) {
                    throw new Exception("Prepare failed: {$conn->error}");
                }
                $updateStmt->bind_param("dis", $total, $uid, $bank);
                if (!$updateStmt->execute()) {
                    throw new Exception("Execute failed: {$updateStmt->error}");
                }
                // Commit transaction
                $conn->commit();
                header("Location:Income.php");
            } else {
                $error_message = "Error executing statement: {$stmt->error}";
            }
        } catch (Exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    }
    if (isset($updateStmt)) $updateStmt->close();
}