<?php
// Disable all error reporting
error_reporting(0);
session_start();
if(!isset($_SESSION["user"]))
{
    header("Location: ../index.php");
}
$uid = $_SESSION["ID"];
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body class="container">
    <div class="nav-bar">
        <div class="Profile"> 
            <div class="Profile_img">
                <img src="https://picsum.photos/100/100" alt="" width="110">
            </div>
        </div>

        <div class="user-name">
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>

        <!-- Section for Dashboard -->
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
                <img src="../Assets/Icons/travels.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Goals.php">Savings</a></p>
            </div>
        </div>

        <!-- Section for Savings -->
        <div class="Approvals-Nav <?php echo ($current_page == 'Savings.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/approvals.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Savings.php">Goals</a></p>
            </div>            
        </div>

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
            <div class="travel-title">
                <div class="left">
                    <h1 id="travel-title">Travels</h1>
                </div>
                <div class="right">
                    <div class="box">
                        <div class=logo-new-trip">
                            <a href="Travels-newtrip.php"><img src="../Assets/Icons/pen.svg" alt="" width="20px" id="logo-new-trip"></a>
                        </div>
                        <div class="search">
                            <img src="../Assets/Icons/magnifying-glass.svg" alt="" width="20px" id="search">
                        </div>
                        <div class="filter">
                            <img src="../Assets/Icons/filter.svg" alt="" width="20px" id="search">
                        </div>
                    </div>
                </div>
            </div>
            <hr class="new1">
            <table class="table-travel">
                <thead> 
                    <th class="tab">DETAILS</th>
                    <th class="tab">CATEGORY</th>
                    <th class="tab">AMOUNT</th>
                    <th class="tab">DATE</th>
                </thead>
            </table>
        </div>
    </div>
</body>
</html>