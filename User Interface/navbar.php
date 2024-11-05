<head>
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link rel="stylesheet" href="../Styles/mobilestyles.scss">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="navbar-custom">
        <div class="navbar-items">
            <div class="Profile">
                <div class="Profile_img">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="110">
                </div>
            </div>

            <div class="user-name">
                <p class=""><?php echo htmlspecialchars($username); ?></p>
            </div>

            <!-- Home Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon-custom" src="../Assets/Icons/home.svg" alt="Icon">
                <p><a class="navbar-items-custom" href="Dashboard.php">Home</a></p>
            </div>

            <!-- Expenses Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'expense.php' || $current_page == 'add_expense.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon-custom" src="../Assets/Icons/expenses.svg" alt="Icon">
                <p><a class="navbar-items-custom" href="expense.php">Expenses</a></p>
            </div>

            <!-- Income Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'income.php' || $current_page == 'Income.php' || $current_page == 'AddIncome.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon-custom" src="../Assets/Icons/income.svg" alt="Icon">
                <p><a class="navbar-items-custom" href="Income.php">Income</a></p>
            </div>

            <!-- Goal Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Goals.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon-custom" src="../Assets/Icons/approvals.svg" alt="Icon">
                <p><a class="navbar-items-custom" href="Goals.php">Goals</a></p>
            </div>

            <!-- Savings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Savings.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon-custom" src="../Assets/Icons/reports.svg" alt="Icon">
                <p><a class="navbar-items-custom" href="Savings.php">Banks</a></p>
            </div>

            <!-- Settings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Settings.php' || $current_page == 'profile.php' || $current_page == 'report_submitted.php' || $current_page == 'report.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon-custom" src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
                <p><a class="navbar-items-custom" href="Settings.php">Settings</a></p>
            </div>

            <div class="Logo-Nav" id="Nav_Side">
                <div class="Penny_Logo">
                    <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleMenu() {
            document.querySelector('.navbar-items').classList.toggle('show');
        }
    </script>
</body>