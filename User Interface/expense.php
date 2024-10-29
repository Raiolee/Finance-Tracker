<?php
// Include database connection
include('../connection/config.php');

// Start session and fetch user data from session
session_start();
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
$user_id = $_SESSION['user_id'];

// Fetch user data, change according to what is needed
$query = "SELECT user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Prepare the profile picture for display
if ($user['user_dp']) {
    // Convert BLOB to base64-encoded image
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    // If no profile picture is found, use a placeholder image
    $profile_pic = 'https://picsum.photos/100/100';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- All content is stored in container -->
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
        <!-- Main Section -->
        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <!-- Top bar section -->
                    <div class="top-bar space-between" id="expense">
                        <h1 class="header">Expenses</h1>
                        <a href="add_expense.php"><button class="New-Saving" id="NewExpenseButton">+ Add an Expense</button></a>
                    </div>
                    <!-- Put main code here -->

                    <table class="table-approval">
                        <thead>
                            <tr>
                                <th>DETAILS</th>
                                <th>MERCHANT</th>
                                <th>BANK</th>
                                <th>AMOUNT</th>
                                <th>REIMBURSABLE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once "../connection/config.php";

                            // Query to fetch the required details from the expenses table
                            $query = "SELECT subject AS details, merchant, 'Bank Name' AS bank, total AS amount, reimbursable FROM expenses";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['details']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['merchant']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['bank']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['reimbursable']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No expenses found.</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>



                </div>
            </div>
        </section>
    </div>
    <script src="../js/expense.js"></script>
    <!-- APIs (Put APIs below this comment)-->
</body>

</html>