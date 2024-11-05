<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
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
// Fetch only the user_dp (profile picture) from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && $user['user_dp']) {
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    $profile_pic = '../Assets/blank-profile.webp';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link rel="stylesheet" href="../Styles/dashboardstyles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="container">
    <?php include 'navbar.php'; ?>

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
                            <button class="quick-access-item" id="newExpenseBtn">
                                <i class="icon-credit-card"></i>
                                <span>+ New Expense</span>
                            </button>
                            <button class="quick-access-item" id="newIncomeBtn">
                                <i class="icon-receipt"></i>
                                <span>+ Add Income</span>
                            </button>
                            <button class="quick-access-item" id="newGoalBtn">
                                <i class="icon-report"></i>
                                <span>+ New Goal</span>
                            </button>
                            <button class="quick-access-item" id="BankButton">
                                <i class="fas fa-university"></i>
                                <span>+ Add Banks</span>
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
                                $sql = "SELECT subject, merchant, amount, date FROM expenses WHERE user_id = ? ORDER BY date DESC LIMIT 5";

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
                                        echo "<td>₱" . number_format($row['amount'], 2) . "</td>";
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
        <?php include('modals/modal-expense.php'); ?>
        <?php include('modals/modal-income.php'); ?>
        <?php include('modals/modal-goals.php'); ?>
        <?php include("modals/modal-savings.php"); ?>
        <?php include("../APIS/init.php"); ?>
        <?php include("../APIS/savings_api.php"); ?>
        <?php include("../APIS/goals_api.php"); ?>
        <?php include("../APIS/income_api.php"); ?>
        <script src="../js/quickreport.js"></script>
        <script src="../js/modal.js"></script>
        <script src="../js/savings.js"></script>
</body>
</html>