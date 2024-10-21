<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbconnect.php';

// Initialize an empty variable for the search keyword
$searchKeyword = '';

// Check if the form has been submitted and set the search keyword
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['searchKeyword'])) {
    $searchKeyword = htmlspecialchars($_POST['searchKeyword']);
}

// Prepare the SQL query based on whether a search keyword exists
if ($searchKeyword) {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, source, total, currency, category, investment FROM incomes WHERE source LIKE ? OR category LIKE ?");
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
    $sql = "SELECT id, source, total, currency, category, investment FROM incomes";
    $result = $conn->query($sql);
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
    <link rel="stylesheet" href="ViewIncome.css">
    <style>
        body {
    background-color: #1a1a1a;
    color: #ffffff;
}



        
    </style>
</head>
<body>
    <div class="main-holder">
        <div class="Nav-Bar">
            <div class="Profile"> 
                <div class="Profile_img">
                    <img src="https://picsum.photos/100/100" alt="" width="140">
                </div>
            </div>

            <div class="User-name">
                <p>Username</p>
            </div>

            <div class="Home-Nav " id="Nav_Button">
                <div>
                    <img src="Home.svg" alt="Icon" width="70px" height="50px">
                </div>
                <div>
                    <p><a href="Dashboard.php">Home</a></p>
                </div>
            </div>

            <div class="Expenses-Nav" id="Nav_Button">
                <div>
                    <img src="Expenses.svg" alt="Icon" width="70px" height="50px">
                </div>
                <div>
                    <p><a href="Expenses.php">Expenses</a></p>
                </div>
            </div>

           
            <div class="NewIncome-Nav active" id="Nav_Button">
                <div>
                    <img src="income2.svg" alt="New Income Icon" width="70px" height="50px"> <!-- Replace with your SVG icon -->
                </div>
                <div>
                    <p><a href="NewIncome.php">Income</a></p>
                </div>
            </div>

           

            <div class="Goals-Nav" id="Nav_Button">
                <div>
                    <img src="report.svg" alt="Icon" width="40px" >
                </div>
                <div>
                    <p><a href="Report.php">Goals</a></p>
                </div>
            </div>


            <div class="Goals-Nav" id="Nav_Button">
                <div>
                    <img src="report.svg" alt="Icon" width="40px" >
                </div>
                <div>
                    <p><a href="Report.php">Savings</a></p>
                </div>
            </div>

            <div class="Settings-Nav" id="Nav_Button">
                <div>
                    <img src="Setting.svg" alt="Icon" width="40px" height="50px">
                </div>
                <div>
                    <p><a href="Settings.php">Settings</a></p>
                </div>
            </div>
            <div class="Logo-Nav" id="Nav_Side">
                <div class="Penny_Logo">
                    <img src="PENNY_WISE_Logo.png" alt="" width="145px" >
                    
                </div>
    
            </div>
        </div>

    <div class="container mt-5">
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
                     if ($result->num_rows > 0) {
                       
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['source']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['total']) . " " . htmlspecialchars($row['currency']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['investment']) . "</td>";
                        echo "<td><button class='btn btn-outline-light' data-id='" . htmlspecialchars($row['id']) . "'><i class='fas fa-ellipsis-v'></i></button></td>";
                        echo "</tr>";
                    }
                } else {
                    
                    echo "<tr><td colspan='4'>No income records found</td></tr>";
                }
                ?>
                    
                    </tbody>
                   
                </table>
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
   
</body>
</html>