<?php
include '../connection/config.php';

// Create a connection
$conn = mysqli_connect($DB_Host, $DB_User, $DB_Password, $DB_Name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}

// Get the user ID
$userId = $_SESSION['user_id'] ?? null;

if (empty($userId)) {
    $error_message = "User  ID is not set. Please log in again.";
} else {
    if (isset($_POST['submit-form'])) {
        $startDate = $_POST['Start-Date'];
        $endDate = $_POST['End-Date'];
        $subject = $_POST['Subject'];
        $category = $_POST['GoalsCategory'];
        $description = $_POST['Description'];
        $budgetLimit = $_POST['Target-Amount'];

        // Validate required fields
        if (empty($startDate) || empty($endDate) || empty($subject) || empty($category) || empty($description) || empty($budgetLimit)) {
            $error_message = "Please fill in all fields.";
        } else {
            try {
                // Prepare and execute the insert statement
                $sql = "INSERT INTO goals (user_id, subject, start_date, date, category, budget_limit, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                // Use 'd' for double (for budget limit) and 'i' for integer (for user_id)
                $stmt->bind_param("issssds", $userId, $subject, $startDate, $endDate, $category, $budgetLimit, $description);

                if (!$stmt->execute()) {
                    $error_message = "Error executing statement: {$stmt->error}";
                } else {
                    header("Location: Goals.php?success=1");
                    exit();
                }
            } catch (Exception $e) {
                $error_message = "An error occurred: " . $e->getMessage();
            }
        }
    }
}
    $sql = "SELECT subject, category, date FROM user_db.goals WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $goalsAndSavings = getGoalsAndSavings($conn, $userId);
    } else {
        $error_message = "Error preparing statement: {$conn->error}";
    }

    function getGoalsAndSavings($conn, $userId) {
        // Fetch goals
        $goalsSql = "SELECT subject, budget_limit FROM goals WHERE user_id = ?";
        $stmt = $conn->prepare($goalsSql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $goalsResult = $stmt->get_result();
        $goals = $goalsResult->fetch_all(MYSQLI_ASSOC);
    
        // Fetch savings
        $savingsSql = "SELECT subject, balance FROM savings WHERE user_id = ?";
        $stmt = $conn->prepare($savingsSql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $savingsResult = $stmt->get_result();
        $savings = $savingsResult->fetch_all(MYSQLI_ASSOC);
    
        // Calculate total balance for each subject
        $totalBalances = [];
        foreach ($savings as $saving) {
            $totalBalances[$saving['subject']] = ($totalBalances[$saving['subject']] ?? 0) + $saving['balance'];
        }
    
        // Calculate the percentage of total balance to budget limit for each goal
        $results = [];
        foreach ($goals as $goal) {
            $totalBalance = $totalBalances[$goal['subject']] ?? 0;
            $percentage = $goal['budget_limit'] ? ($totalBalance / $goal['budget_limit']) * 100 : 0;
            $results[] = [
                'subject' => $goal['subject'],
                'totalBalance' => $totalBalance,
                'budgetLimit' => $goal['budget_limit'],
                'percentage' => $percentage
            ];
        }
        
        return $results;
    }

    

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Goals</title>
</head>
<body class="container">
    <div class="nav-bar">
        <div class="Profile">
            <div class="Profile_img">
                <img src="https://picsum.photos/100/100" alt="" width="110">
            </div>
        </div>

        <div class="user-name">
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
            <div class="inner">
                <div class="goals-container">
                    <div class="left">
                        <h1 id="goals-title">Goals</h1>
                    </div>
                    <div class="right">
                        <div class="box">
                            <div class="add">
                                <button id="newGoalsBTN" class="new-goal">+ New Goal</button>
                            </div>
                            <form class="goal-form" action="search.php" method="GET">
                                <input type="search" name="query" placeholder="Search here ...">
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/magnifying-glass.svg" alt="" width="20px"></i>
                                </button>
                            </form> 
                        </div>
                    </div>
                </div>
                <hr class="new">
                
                <table class="goal-travel">
                    <thead> 
                        <tr>
                            <th class="tab">SUBJECT</th>
                            <th class="tab">CATEGORY</th>
                            <th class="tab">ACCOMPLISHMENT DATE</th>
                            <th class="tab">PROGRESS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if (isset($result) && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $percentage = 0;
                                    foreach ($goalsAndSavings as $goal) {
                                        if ($goal['subject'] === $row['subject']) {
                                            $percentage = $goal['percentage'];
                                            break;
                                        }
                                    }
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['subject']) . "</td>
                                        <td>" . htmlspecialchars($row['category']) . "</td>
                                        <td>" . htmlspecialchars($row['date']) . "</td>
                                        <td>" . htmlspecialchars(number_format($percentage)) . "%</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No results found</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="newGoalForm" class="new-goal-form" style="display:none;">
                <div class="newform">
                    <h1 id="goals-title">New Goal</h1>
                </div>
                <hr class="new1">
                <form id="GoalForm" method="post">
                    <div class="Goal-Form-Format" id="Start-Date-Row">
                        <label for="Start-Date" class="Goals-Label">Start Date*</label>
                        <input type="date" id="Start-Date" name="Start-Date" required>
                    </div>
                    <div class="Goal-Form-Format" id="End-Date-Row">
                        <label for="End-Date" class="Goals-Label">End Date*</label>
                        <input type="date" id="End-Date" name="End-Date" required>
                    </div>
                    <div class="Goal-Form-Format" id="Subject-Row">
                        <label for="Subject" class="Goals-Label">Subject*</label>
                        <input type="text" id="Subject" name="Subject" required>
                    </div>
                    <div class="Goal-Form-Format" id="Category-Row">
                        <label for="GoalsCategory" class="Goals-Label">Category*</label>
                        <select id="GoalsCategory" name="GoalsCategory" required>
                            <option value="" disabled selected>Category</option>
                            <option value="Savings">Savings</option>
                            <option value="Travels">Travels</option>
                            <option value="Miscellaneous">Miscellaneous</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="Goal-Form-Format" id="Description-Row">
                        <label for="Description" class="Goals-Label">Description*</label>
                        <textarea id="Description" name="Description" required></textarea>
                    </div>
                    <div class="Goal-Form-Format" id="Target-Amount-Row">
                        <label for="Target-Amount" class="Goals-Label">Target Amount*</label>
                        <input type="text" id="Target-Amount" name="Target-Amount" required>
                    </div>
                    <div class="Goal-Form" id="Button-Row">
                        <div class="button-div-row">
                            <button type="button" class="button-goals" onclick="closeGoalForm()">Cancel</button>
                            <button type="submit" name="submit-form" class="button-goals">Save</button>
                        </div>
                    </div>
                </form>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if URL contains #newGoalForm, show the form if true
        if (window.location.hash === '#newGoalForm') {
            const rightContainer = document.querySelector('.inner');
            const form = document.getElementById('newGoalForm');

            rightContainer.style.display = 'none'; // Hide the right container
            form.style.display = 'block'; // Show the new goal form
        }

        // Show form when the "New Goal" button is clicked and update the URL
        document.getElementById('newGoalsBTN').addEventListener('click', function() {
            const rightContainer = document.querySelector('.inner');
            const form = document.getElementById('newGoalForm');

            rightContainer.style.display = 'none'; // Hide the right container
            form.style.display = 'block'; // Show the new goal form

            // Update the URL without reloading the page
            window.history.pushState({}, '', '#newGoalForm');
        });
    });

    // Close form and clear URL hash
    function closeGoalForm() {
        const rightContainer = document.querySelector('.inner');
        const form = document.getElementById('newGoalForm');

        form.style.display = 'none'; // Hide the new goal form
        rightContainer.style.display = 'block'; // Show the right container again
        clearForm(); // Clear the form fields

        // Remove the URL fragment
        window.history.pushState({}, '', window.location.pathname);
    }

    // Clear the form
    function clearForm() {
        document.getElementById('GoalForm').reset(); // Clear all form fields
    }
</script>
</body>
</html>