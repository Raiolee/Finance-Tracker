<?php
session_start();
if(!isset($_SESSION["user"]))
{
    header("Location: ../Login.php");
}
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Interface.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
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

        <div class="Home-Nav <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="Icons/home.svg" alt="Icon" width="50px" id="icons">
            </div>
            <div>
                <p><a href="Dashboard.php">Home</a></p>
            </div>
        </div>

        <div class="Expenses-Nav <?php echo ($current_page == 'Expenses.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="Icons/expenses.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Expenses.php">Expenses</a></p>
            </div>
        </div>

        <div class="Travels-Nav <?php echo ($current_page == 'Travels.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="Icons/travels.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Travels.php">Travels</a></p>
            </div>
        </div>

        <div class="Travels-Nav <?php echo ($current_page == 'Income.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="Icons/income.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Income.php">Income</a></p>
            </div>
        </div>

        <div class="Approvals-Nav <?php echo ($current_page == 'Approvals.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="Icons/approvals.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Approvals.php">Approval</a></p>
            </div>            
        </div>

        <div class="Report-Nav <?php echo ($current_page == 'Report.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="Icons/reports.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Report.php">Report</a></p>
            </div>
        </div>

        <div class="Settings-Nav <?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="Icons/settings.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Settings.php">Settings</a></p>
            </div>
        </div>

        <div class="Logo-Nav" id="Nav_Side">
            <div class="Penny_Logo">
                <img src="../logo/PENNY_WISE_Logo.png" alt="" width="200">
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
                            <a href="Travels-newtrip.php"><img src="Icons/pen.svg" alt="" width="20px" id="logo-new-trip"></a>
                        </div>
                        <div class="search">
                            <img src="icons/magnifying-glass.svg" alt="" width="20px" id="search">
                        </div>
                        <div class="filter">
                            <img src="icons/filter.svg" alt="" width="20px" id="search">
                        </div>
                    </div>
                </div>
            </div>
            <hr class="new1">
        </div>
    </div>


</body>
</html>