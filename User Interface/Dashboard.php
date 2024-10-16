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
    <title>Dashboard</title>
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link rel="stylesheet" href="../Styles/dashboardstyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <!-- Section for Travels -->
        <div class="Travels-Nav <?php echo ($current_page == 'Travels.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/travels.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Travels.php">Savings</a></p>
            </div>
        </div>

        <!-- Section for Goals -->
        <div class="Approvals-Nav <?php echo ($current_page == 'Approvals.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/approvals.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Approvals.php">Goals</a></p>
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
         <div class="main-content">
             <!-- Pending Tasks Section -->
             <div class="task-section">
                 <h2>Pending Tasks</h2>
                 <ul>
                     <li><i class="fas fa-clock"></i> Pending Approvals <span><?php echo htmlspecialchars(isset($pending_approvals) ? $pending_approvals : 0); ?></span></li>
                     <li><i class="fas fa-plane"></i> New Trips Registered <span><?php echo htmlspecialchars(isset($new_trips) ? $new_trips : 0); ?></span></li>
                     <li><i class="fas fa-wallet"></i> Unreported Expenses <span><?php echo htmlspecialchars(isset($unreported_expenses) ? $unreported_expenses : 0); ?></span></li>
                     <li><i class="fas fa-folder"></i> Upcoming Expenses <span><?php echo htmlspecialchars(isset($upcoming_expenses) ? $upcoming_expenses : 0); ?></span></li>
                 </ul>
             </div>
         </div>
 
        <!-- Quick Report Section -->
               <div class="report-section">
                    <h2>Quick Report</h2>
                    <div class="chart-container">
                        <canvas id="quickReportChart"></canvas>
                    </div>
                </div>
        </div>
    </div>

         <p><a href="../Logout.php">Logout</a></p>
     </div>
 </div>
 

 <script src="../User Interface/quickreport.js"></script>

</body>
</html>