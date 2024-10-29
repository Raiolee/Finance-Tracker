<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../connection/config.php';

$message = ''; // Variable to store success/error messages

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $date = $_POST['date'];
    $investment = $_POST['investment'];
    $source = $_POST['source'];
    $total = $_POST['total'];
    $currency = $_POST['currency'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO income (date, investment, source, total, currency, category, description) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        $message = 'Prepare failed: ' . $conn->error;
    } else {
        // Bind the parameters
        $stmt->bind_param("sssssss", $date, $investment, $source, $total, $currency, $category, $description);

        // Execute the statement and check for errors

        header("Location:Income.php");

        if ($stmt->execute()) {
            $message = 'Record saved successfully!';
        } else {
            $message = 'Error: ' . $stmt->error;
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PennyWise - New Income</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel ="stylesheet" href="../Styles/AddIncome.css">
    
   
</head>
<body class="container">
    <div class="nav-bar">
        <div class="Profile">
            <div class="Profile_img">
                <img src="https://picsum.photos/100/100" alt="" width="110">
            </div>
        </div>

        <div class="user-name">
            
        </div>

        <div class="Home-Nav <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/home.svg" alt="Icon" width="50px" id="icons">
            </div>
            <div>
                <p><a href="Dashboard.php">Home</a></p>
            </div>
        </div>

        <!-- Section for Expenses -->
        <div class="Expenses-Nav <?php echo ($current_page == 'Expenses.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/expenses.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Expenses.php">Expenses</a></p>
            </div>
        </div>

        <!-- Section for Income -->
        <div class="Travels-Nav <?php echo ($current_page == 'Income.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/income.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Income.php">Income</a></p>
            </div>
        </div>

        <!-- Section for Goals -->
        <div class="Travels-Nav <?php echo ($current_page == 'Goals.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/approvals.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Goals.php">Goals</a></p>
            </div>
        </div>

        <!-- Section for Savings -->
        <div class="Approvals-Nav <?php echo ($current_page == 'Savings.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/reports.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Savings.php">Savings</a></p>
            </div>            
        </div>

        <!-- Settings Section -->
        <div class="Settings-Nav <?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Settings.php">Settings</a></p>
            </div>
        </div>

        <div class="Logo-Nav" id="Nav_Side">
            <div class="Penny_Logo">
                <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
            </div>
        </div>
    </div>

        <div class="content">
            
            <div class="right-container">
                <div class ="income-header">
                <h2>New Income</h2>
                <img src="../Assets/Icons/Line 9.svg" alt="Line Icon" class="icon-line" />
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
                    <label for="bank_name" class="form-label col-sm-3">Bank Name*</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="bank_name" name="bank_name" required>
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
                </form>
              
            
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
