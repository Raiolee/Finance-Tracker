<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../connection/config.php';

$userId = $_SESSION['user_id'] ?? null;

function searchGoalsBySubject($conn, $userId, $query) {
    $sql = "SELECT subject, category FROM income WHERE user_id = ? AND subject LIKE ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $likeQuery = "%{$query}%";
        $stmt->bind_param("is", $userId, $likeQuery);
        $stmt->execute();
        return $stmt->get_result();
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
    try {
        $result = searchGoalsBySubject($conn, $userId, $searchQuery);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
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
    <link rel ="stylesheet" href="../Styles/styles.scss">
    <style>
        body {
    background-color: #1a1a1a;
    color: #ffffff;
        }
        
    </style>
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
    </div>

    <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <!-- Top bar section -->
                    <div class="top-bar" id="expense">
                        <h1 class="header">Income</h1>
                        <div class="button-group">
                            <a href="AddIncome.php" class="btn btn-outline-light">+ Add Income</a>
                            <button class="btn btn-outline-light" id="sortBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Put main code here -->

                    <table class="table-approval">
                        <thead>
                            <tr class="header-row">
                                <th class="table-header">Source of Income</th>
                                <th class="table-header">Amount</th>
                                <th class="table-header">Category</th>
                                <th class="table-header">Type of Investment</th>
                                
                            </tr>
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
                             echo "<td>" . htmlspecialchars($row['bank']) . "</td>";
                             echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                             echo "<td><button class='btn btn-outline-light' data-id='" . htmlspecialchars($row['income_id']) . "'><i class='fas fa-ellipsis-v'></i></button></td>";
                             echo "</tr>";
                         }
                     } else {
                         echo "<tr><td colspan='5'>No income records found</td></tr>";
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
            <form id="editDeleteForm" method="POST" action=""> <!-- Adjust the action as needed -->
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
</section>

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