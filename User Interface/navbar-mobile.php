<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../Styles/mobilestyles.scss">
<!-- Top Navigation Menu -->
<div class="navbar-mobile">
    <!-- Hamburger menu icon (left) -->
    <a href="javascript:void(0);" class="icon-left" onclick="navbarFunction()">
        <i class="fa fa-bars"></i>
    </a>

    <!-- Centered Logo -->
    <a href="Dashboard.php" class="center-icon">PennyWise</a>

    <!-- Search icon (right) -->
    <a href="javascript:void(0);" class="icon-right">
        <i class="fa fa-search"></i>
    </a>

    <!-- Navigation links (hidden by default) -->
    <div class="navbarLinks" id="navbarLinks">
        <a class="navbarItems" href="Dashboard.php">Home</a>
        <a class="navbarItems" href="expense.php">Expenses</a>
        <a class="navbarItems" href="Income.php">Income</a>
        <a href="Goals.php" class="navbarItems">Goals</a>
        <a href="Savings.php" class="navbarItems">Banks</a>
        <a href="Settings.php" class="navbarItems">Settings</a>
    </div>
</div>

<script src="../js/navbar.js"></script>