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

        <div class="Home-Nav <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/home.svg" alt="Icon" width="50px" id="icons">
            </div>
            <div>
                <p><a href="Dashboard.php">Home</a></p>
            </div>
        </div>

        <div class="Expenses-Nav <?php echo ($current_page == 'Expenses.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/expenses.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Expenses.php">Expenses</a></p>
            </div>
        </div>

        <div class="Travels-Nav <?php echo ($current_page == 'Travels-newtrip.php') ? 'active' : 'Travels.php'; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/travels.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Travels.php">Travels</a></p>
            </div>
        </div>

        <div class="Travels-Nav <?php echo ($current_page == 'Income.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/income.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Income.php">Income</a></p>
            </div>
        </div>

        <div class="Approvals-Nav <?php echo ($current_page == 'Approvals.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/approvals.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Approvals.php">Approval</a></p>
            </div>            
        </div>

        <div class="Report-Nav <?php echo ($current_page == 'Report.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/reports.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Report.php">Report</a></p>
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
                    <h1 id="travel-title">New Trip</h1>
                </div>        
            </div>

            <hr class="new">

            <div class="list">
                <form action="Travels-newtrip.php" method="POST">
                    <label class="list-var">Name*</label>
                    <input class="name-input" type="text" name="name"><br><br>

                    <label class="list-var">Mode*</label>
                    <input class="mode-btn" type="radio" name="public"><label class="list-var" for="">Public Vehicle</label> <input class="mode-btn1" type="radio" name="private"><label class="list-var" for="">Private Vehicle</label><br><br>
                    
                    <label class="list-var">Category*</label>
                    <input class="category-btn" type="radio" name="bussiness-trip"><label class="list-var" for="">Bussiness Trip</label><input  class="category-btn1" type="radio" name="personal-trip"><label class="list-var" for="">Personal Trip</label> <input class="category-btn2" type="radio" name="others"><label class="list-var" for="">Others</label><br><br>
            
                    <label class="list-var">Duration*</label>
                    <input class="duration-input" type="text" name="travelFrom"><img src="../Assets/Icons/right.svg" alt="" width="55px"> <input class="duration-input1" type="text" name="travelTo"><br>
                    <input class="duration-input2" type="text" name="travelTo2"><img src="../Assets/Icons/left.svg" alt="" width="55px"> <input class="duration-input1" type="text" name="travelFrom2"><br>
                    
                    <label class="list-var">Student*</label>
                    <input class="student-btn" type="radio" name="Yes"><label class="list-var" for="">Yes</label> <input class="student-btn1" type="radio" name="No"> <label class="list-var" for="">No</label><br><br>
                    
                    <label class="list-var">Depart From*</label>
                    <input class="from-input" type="text" name="departFrom"><br><br><br>
                    
                    <label class="list-var">Destination*</label>
                    <input class="destination-input" type="text" name="destination"><br><br><br>
                    
                    <label class="list-var">Budget Limit*</label>
                    <input class="budget-input" type="text" name="budgetLimit"><br><br><br>
                    
                    <input class="travel-submit-btn" type="submit" name="submitForm" value="Add">
                </form>
            </div>
        </div>
    </div>
</body>
</html>