<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit;
}
$username = $_SESSION["name"];
$user_id = $_SESSION["user_id"];
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../connection/config.php';

$sql = "SELECT subject, start_date FROM goals WHERE user_id = ? AND start_date >= CURDATE() ORDER BY start_date ASC LIMIT 5";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pending_goals[] = [
            'subject' => $row['subject'],
            'start_date' => $row['start_date'],
        ];
    }
}

$stmt->close();
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

        <!-- Section for Goals -->
        <div class="Travels-Nav <?php echo ($current_page == 'Goals.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/approvals.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Goals.php">Goals</a></p>
            </div>
        </div>

        <!-- Section for Savings -->
        <div class="Approvals-Nav <?php echo ($current_page == 'Savings.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/reports.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Savings.php">Savings</a></p>
            </div>
        </div>

        <!-- Settings Section -->
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
                <!-- Grouped Sections in Row (Pending Tasks and Quick Report) -->
                <div class="horizontal-sections">
                    <!-- Pending Tasks Section -->
                    <div class="task-section">
                        <h2>Pending Goals</h2>
                        <ul>
                        <?php if (!empty($pending_goals)): ?>
                            <?php foreach ($pending_goals as $goal): ?>
                                <li>
                                 <i class="fas fa-trophy"></i> 
                                <?php echo htmlspecialchars($goal['subject']); ?> 
                                (Started on: <?php echo htmlspecialchars(date('F j, Y', strtotime($goal['start_date']))); ?>)
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No pending goals found.</li>
                        <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Quick Report Section -->
                    <div class="report-section">
                        <h2>Quick Report</h2>
                        <div class="chart-container">
                            <canvas id="quickReportChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="vertical-sections">
                    <!-- Quick Access Section -->
                    <div class="quick-access-section">
                        <h2>Quick Access</h2>
                        <div class="quick-access-items">
                            <button class="quick-access-item" onclick="window.location.href='Expenses.php#newExpenseForm'">
                                <i class="icon-credit-card"></i>
                                <span>+ New Expense</span>
                            </button>
                            <button class="quick-access-item" onclick="handleNewReceipt()">
                                <i class="icon-receipt"></i>
                                <span>+ Add Income</span>
                            </button>
                            <button class="quick-access-item" onclick="window.location.href='Goals.php#newGoalForm'">
                                <i class="icon-report"></i>
                                <span>+ New Goal</span>
                            </button>
                            <button class="quick-access-item" onclick="handleNewSaving()">
                                <i class="icon-plane"></i>
                                <span>+ Add Saving</span>
                            </button>
                        </div>
                    </div>

              <!-- Recent Transactions Section -->
                <div class="recent-expenses-section">
                    <h2>Recent Expenses</h2>
                        <table class="expense-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Merchant</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                         </thead>
                        <tbody>
                        <?php
                         // SQL query to fetch recent expenses for the logged-in user
                         $sql = "SELECT subject, merchant, total, date FROM expenses WHERE user_id = ? ORDER BY date DESC LIMIT 5";

                         // Prepare and bind the SQL statement
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $user_id);  // Bind the user_id as an integer
                            $stmt->execute();
                            $result = $stmt->get_result();

                         // Check if there are results and loop through them
                         if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['merchant']) . "</td>";
                                echo "<td>₱" . number_format($row['total'], 2) . "</td>";
                                echo "<td>" . date("F j, Y", strtotime($row['date'])) . "</td>"; // Formatting the date
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No recent expenses found</td></tr>";
                        }

                        // Close the statement and connection
                            $stmt->close();
                            $conn->close();
                        ?>
                    </tbody>
             </table>
        </div>
        <script src="../js/quickreport.js"></script>
        <script>
                function handleNewReceipt() {
                window.location.href = 'AddIncome.php';
                }

                function handleNewSaving(){
                    window.location.href='AddSavings.php';
                }
        </script>
</body>
</html>