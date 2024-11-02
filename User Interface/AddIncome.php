<?php
include '../connection/config.php';

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
    exit();
}

$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);

// Include database connection
include('../connection/config.php');

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

$message = ''; // Variable to store success/error messages

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    $uid = $_SESSION["user_id"];

    // Get form data
    $date = $_POST['date'];
    $source = $_POST['source'];
    $total = $_POST['total'];
    $category = $_POST['category'];
    $bank = $_POST['bank_name'];
    $description = $_POST['description'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Prepare the SQL statement for inserting income
        $stmt = $conn->prepare("INSERT INTO income (user_id, date, source, total, category, description, bank) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            throw new Exception("Prepare failed: {$conn->error}");
        }

        // Bind the parameters
        $stmt->bind_param("issssss", $uid, $date, $source, $total, $category, $description, $bank);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: {$stmt->error}");
        }

        // Update the bank balance
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

        $message = 'Record saved successfully!';
        header("Location:Income.php");
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        $message = 'Error: ' . $e->getMessage();
    }

    // Close statements
    if (isset($stmt)) $stmt->close();
    if (isset($updateStmt)) $updateStmt->close();

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income</title>
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>

    <div class="container">
        <?php include 'navbar.php'; ?>

        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <div class="top-bar">
                        <h1 class="header">Income</h1>
                    </div>
                    <form method="POST" class="pfp-form" action="Income.php" id="add-income">
                        <div class="big-divider full">
                            <label for="date" class="form-labels">Date*</label>
                            <input type="date" class="date-input" id="date" name="date" required>

                            <label for="source" class="form-labels">Source of Income*</label>
                            <input type="text" class="var-input" id="source" name="source" required>

                            <label for="name" class="form-labels">Amount</label>
                            <input type="number" class="var-input" id="amount" name="amount">

                            <label for="category" class="form-labels">Category*</label>
                            <select class="var-input medium pointer" id="category" name="category" required>
                                <option value="Monthly" selected>Monthly</option>
                                <option value="Weekly">Weekly</option>
                            </select>

                            <label for="bank_name" class="form-labels">Bank Name*</label>
                            <select class="var-input medium pointer" name="recurrence_type" required>
                                <option value="" disabled selected>Select a bank</option>
                                <?php
                                session_start();
                                $uid = $_SESSION["user_id"];
                                // Fetch bank names from the database
                                $bankQuery = "SELECT bank FROM bank WHERE user_id = ?";
                                $bankStmt = $conn->prepare($bankQuery);
                                $bankStmt->bind_param("i", $uid);
                                $bankStmt->execute();
                                $bankResult = $bankStmt->get_result();
                                while ($row = $bankResult->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row['bank']) . '">' . htmlspecialchars($row['bank']) . '</option>';
                                }
                                $bankStmt->close();
                                ?>
                            </select>

                            <label for="description" class="form-labels">Description</label>
                            <textarea class="text-input" id="description" name="description" rows="3"></textarea>

                            <div class="btn-options center" id="report-btns">
                                <a href="income.php" class="link-btn"><button type="button" class="cancel">Cancel</button></a>
                                <button type="submit" name="save" class="save">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</body>
</body>

</html>