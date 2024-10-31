<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$uid = $_SESSION["user_id"];
include '../connection/config.php';


$message = ''; // Variable to store success/error messages

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    <link rel="stylesheet" href="../Styles/styles.css">
    <link rel="stylesheet" href="../Styles/AddIncome.css">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
<div class="container">
        <div class="navbar">
            <!-- Profile Picture -->
            <div class="Profile">
                <div class="Profile_img">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="110">
                </div>
            </div>
            <!-- Username Section -->
            <div class="user-name">
                <p><?php echo htmlspecialchars($username); ?></p>
            </div>

            <!-- Home Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/home.svg" alt="Icon">
                <p><a class="navbar-items" href="Dashboard.php">Home</a></p>
            </div>

            <!-- Expenses Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'expense.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/expenses.svg" alt="Icon">
                <p><a class="navbar-items" href="expense.php">Expenses</a></p>
            </div>

            <!-- Income Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'income.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/income.svg" alt="Icon">
                <p><a class="navbar-items" href="Income.php">Income</a></p>
            </div>

            <!-- Goal Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Goals.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/approvals.svg" alt="Icon">
                <p><a class="navbar-items" href="Goals.php">Goals</a></p>
            </div>

            <!-- Savings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Savings.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/reports.svg" alt="Icon">
                <p><a class="navbar-items" href="Savings.php">Savings</a></p>
            </div>

            <!-- Settings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Settings.php' || $current_page == 'profile.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
                <p><a class="navbar-items" href="Settings.php">Settings</a></p>
            </div>
            <!-- Logo in the navbar -->
            <div class="Logo-Nav" id="Nav_Side">
                <div class="Penny_Logo">
                    <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
                </div>
            </div>
        </div>

        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <div class="top-bar">
                        <h1 class="header">Income</h1>
                    </div>
                    <form method="POST">
                        <div class="mb-3 row">
                            <label for="date" class="form-label col-sm-3">Date*</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control custom-date" id="date" name="date" required>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="investment" class="form-label col-sm-3">Investment*</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="investment" name="investment" required>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="source" class="form-label col-sm-3">Source of Income*</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="source" name="source" required>
                            </div>
                        </div>
                    
                        <div class="mb-3 row">
                            <label for="total" class="form-label col-sm-3">Total*</label>
                            <div class="col-sm-9 d-flex align-items-center">
                                <input type="number" class="form-control me-2" id="total" name="total" required style="flex: 1;">
                                <select class="form-select" name="currency" style="max-width: 100px;">
                                    <option selected>Currency</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>
                         <div class="mb-3 row">
                                <label for="bank_name" class="form-label col-sm-3">Bank Name*</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                                </div>
                                <option value="" disabled selected>Select a bank</option> <!-- Optional placeholder -->
                                <?php
                                // Fetch bank names from the database
                                $bankQuery = "SELECT bank FROM bank WHERE user_id = ?";
                                $bankStmt = $conn->prepare($bankQuery);
                                $bankStmt->bind_param("i", $uid);
                                $bankStmt->execute();
                                $bankResult = $bankStmt->get_result();

                                while ($row = $bankResult->fetch_assoc()) {
                                    // Use 'bank' instead of 'bank_name' to match the column name in the database
                                    echo '<option value="' . htmlspecialchars($row['bank']) . '">' . htmlspecialchars($row['bank']) . '</option>';
                                }

                                $bankStmt->close();
                                ?>
                            </select>
                        </div>
                    </div>   
                    <div class="mb-3 row">
                        <label for="category" class="form-label col-sm-3">Category*</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="category" name="category" required>
                                <option value="Monthly" selected>Monthly</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Yearly">Yearly</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="description" class="form-label col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="button-containter">
                        <button type="submit" class="btn btn-primary btn-save">Save</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
