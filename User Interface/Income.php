<?php
// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
    exit();
}

$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);

// Include database connection
include('../connection/config.php');

// Fetch the user's profile picture from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Set the profile picture path or default image
if ($user && $user['user_dp']) {
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    $profile_pic = '../Assets/blank-profile.webp';
}

// Define other functions and handle income operations
function searchIncomeBySubject($conn, $userId, $query) {
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

$userId = $_SESSION['user_id'] ?? null;

if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
    try {
        $result = searchIncomeBySubject($conn, $userId, $searchQuery);
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

    $stmt = $conn->prepare("UPDATE income SET source = ?, total = ?, currency = ?, category = ?, investment = ? WHERE income_id = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("sssssi", $incomeSource, $incomeTotal, $incomeCurrency, $incomeCategory, $incomeInvestment, $incomeId);
    if ($stmt->execute()) {
        $message = "Record updated successfully!";
    } else {
        $message = "Error updating record: " . $stmt->error;
    }
    $stmt->close();
    header("Location: Income.php?message=" . urlencode($message));
    exit();
}

// Handle Delete Operation
if (isset($_GET['id'])) {
    $incomeId = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM income WHERE income_id = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("i", $incomeId);
    if ($stmt->execute()) {
        $message = "Record deleted successfully!";
    } else {
        $message = "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
    header("Location: Income.php?message=" . urlencode($message));
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Styles/ViewIncome.css">
    <link rel="stylesheet" href="../Styles/styles.css">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
    <?php include '../User Interface/navbar.php'; ?>


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
                                <th class="table-header">Date</th>
                                <th class="table-header">Bank</th>
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
                             echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                             echo "<td>" . htmlspecialchars($row['category']) . "</td>";
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
      <form id="editDeleteForm" method="POST" action="">
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
      </form>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Update</button>
      <button type="button" class="btn btn-danger" id="deleteBtn">Delete</button>
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