<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../connection/config.php';

// Initialize an empty variable for the search keyword
$searchKeyword = '';

// Check if the form has been submitted and set the search keyword
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['searchKeyword'])) {
    $searchKeyword = htmlspecialchars($_POST['searchKeyword']);
}

// Prepare the SQL query based on whether a search keyword exists
if ($searchKeyword) {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT income_id, source, total, currency, category, investment FROM income WHERE source LIKE ? OR category LIKE ?");
    
    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    
    $likeKeyword = "%$searchKeyword%";
    $stmt->bind_param("ss", $likeKeyword, $likeKeyword);
    
    $stmt->execute();
    $result = $stmt->get_result();

    // Check for errors
    if ($stmt->error) {
        echo "Error: " . $stmt->error;
        exit();
    }
} else {
    // Default SQL query to get all records
    $sql = "SELECT income_id, source, total, currency, category, investment FROM income";
    $result = $conn->query($sql);
}

// Handle Edit Operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['incomeId'])) {
    $incomeId = $_POST['incomeId'];
    $incomeSource = $_POST['incomeSource'];
    $incomeTotal = $_POST['incomeTotal'];
    $incomeCurrency = $_POST['incomeCurrency'];
    $incomeCategory = $_POST['incomeCategory'];
    $incomeInvestment = $_POST['incomeInvestment'];

    // Prepare the SQL statement for updating the record
    $stmt = $conn->prepare("UPDATE income SET source = ?, total = ?, currency = ?, category = ?, investment = ? WHERE income_id = ?");
    
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters
    $stmt->bind_param("sssssi", $incomeSource, $incomeTotal, $incomeCurrency, $incomeCategory, $incomeInvestment, $incomeId);
    
    // Execute the statement and check for errors
    if ($stmt->execute()) {
        $message = "Record updated successfully!";
    } else {
        $message = "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: Income.php?message=" . urlencode($message)); // Redirect back with message
    exit();
}

// Handle Delete Operation
if (isset($_GET['id'])) {
    $incomeId = $_GET['id'];

    // Prepare the SQL statement for deleting the record
    $stmt = $conn->prepare("DELETE FROM income WHERE income_id = ?");
    
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters
    $stmt->bind_param("i", $incomeId);
    
    // Execute the statement and check for errors
    if ($stmt->execute()) {
        $message = "Record deleted successfully!";
    } else {
        $message = "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: Income.php?message=" . urlencode($message)); // Redirect back with message
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Styles/ViewIncome.css">
    <style>
        body {
    background-color: #1a1a1a;
    color: #ffffff;
        }
        
    </style>
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
    <div class="right-container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="card-title">Income List</h2>
                    <div>
                        <a href="AddIncome.php">
                            <button class="btn btn-outline-light me-2">+ New Income</button>
                        </a>
                        <button class="btn btn-outline-light me-2" id="sortBtn">
                                <i class="fas fa-search"></i>
                            </button>
                         
                        </button>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr class="header-row">
                            <th class="table-header">Source of Income</th>
                            <th class="table-header">Amount</th>
                            <th class="table-header">Category</th>
                            <th class="table-header">Type of Investment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <?php
                 $sql = "SELECT * FROM income";
                 $result = $conn->query($sql);
                 
                 if ($result === false) {
                     echo "Error: " . $conn->error;
                 } else {
                     if ($result->num_rows > 0) {
                         while ($row = $result->fetch_assoc()) {
                             echo "<tr>";
                             echo "<td>" . htmlspecialchars($row['source']) . "</td>";
                             echo "<td>" . htmlspecialchars($row['total']) . " " . htmlspecialchars($row['currency']) . "</td>";
                             echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                             echo "<td>" . htmlspecialchars($row['investment']) . "</td>";
                             echo "<td><button class='btn btn-outline-light' data-id='" . htmlspecialchars($row['income_id']) . "'><i class='fas fa-ellipsis-v'></i></button></td>";
;
                             echo "</tr>";
                         }
                     } else {
                         echo "<tr><td colspan='4'>No income records found</td></tr>";
                     }
                 }
                 
                ?>
                    
                    </tbody>
                   
                </table>
            </div>
            </div>
        </div>
    </div>
    <div id="sortModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Search Income Records</h2>
            <form method="POST" action="">
                <input type="text" name="searchKeyword" class="form-control" placeholder="Enter keyword (e.g., Work)" required>
                <br>
                <button type="submit" class="btn-search">Search</button>
            </form>
        </div>
    </div>
<!-- Edit/Delete Modal -->
<div id="editDeleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit/Delete Income Record</h5>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editDeleteForm" method="POST"> <!-- Adjust the action as needed -->
                <input type="hidden" id="incomeId" name="incomeId">
                <div class="form-group">
                    <label for="incomeSource">Source of Income</label>
                    <input type="text" class="form-control" id="incomeSource" name="incomeSource" required>
                </div>
                <div class="form-group">
                    <label for="incomeTotal">Amount</label>
                    <input type="text" class="form-control" id="incomeTotal" name="incomeTotal" required>
                </div>
                <div class="form-group">
                    <label for="incomeCurrency">Currency</label>
                    <input type="text" class="form-control" id="incomeCurrency" name="incomeCurrency" required>
                </div>
                <div class="form-group">
                    <label for="incomeCategory">Category</label>
                    <select class="form-control" id="incomeCategory" name="incomeCategory" required>
                        <option value="Monthly">Monthly</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Yearly">Yearly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="incomeInvestment">Type of Investment</label>
                    <input type="text" class="form-control" id="incomeInvestment" name="incomeInvestment" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-danger" id="deleteBtn">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
        var modal = document.getElementById("sortModal");
        var btn = document.getElementById("sortBtn");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
<script>
var editDeleteModal = document.getElementById("editDeleteModal");
var editDeleteClose = editDeleteModal.getElementsByClassName("close")[0];

document.querySelectorAll('.btn-outline-light[data-id]').forEach(function(button) {
    button.addEventListener('click', function() {
        var id = this.getAttribute('data-id');
        var source = this.getAttribute('data-source');
        var total = this.getAttribute('data-total');
        var currency = this.getAttribute('data-currency');
        var category = this.getAttribute('data-category');
        var investment = this.getAttribute('data-investment');

        document.getElementById('incomeId').value = id;
        document.getElementById('incomeSource').value = source;
        document.getElementById('incomeTotal').value = total;
        document.getElementById('incomeCurrency').value = currency;
        document.getElementById('incomeCategory').value = category;
        document.getElementById('incomeInvestment').value = investment;

        editDeleteModal.style.display = "block";
    });
});

editDeleteClose.onclick = function() {
    editDeleteModal.style.display = "none";
};

window.onclick = function(event) {
    if (event.target == editDeleteModal) {
        editDeleteModal.style.display = "none";
    }
};
   </script>
</body>
</html>