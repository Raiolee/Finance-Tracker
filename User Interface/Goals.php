<?php
include '../connection/config.php';

// Create a connection
$conn = new mysqli($DB_Host, $DB_User, $DB_Password, $DB_Name);

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

// Error handling for unknown user ID
if (empty($userId)) {
    $error_message = "User  ID is not set. Please log in again.";
} else {
    // Handle form submission
    if (isset($_POST['submit-form'])) {
        $startDate = $_POST['Start-Date'];
        $subject = $_POST['Subject'];
        $category = $_POST['GoalsCategory'];
        $description = $_POST['Description'];
        $budgetLimit = $_POST['Target-Amount'];

        // Validate required fields
        if (empty($startDate) || empty($subject) || empty($category) || empty($description) || empty($budgetLimit)) {
            $error_message = "Please fill in all fields.";
        } else {
            try {
                // Prepare and execute the insert statement
                $sql = "INSERT INTO goals (user_id, subject, start_date, category, budget_limit, description) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                // Use 'd' for double (for budget limit) and 'i' for integer (for user_id)
                $stmt->bind_param("issssd", $userId, $subject, $startDate, $category, $budgetLimit, $description);

                if ($stmt->execute()) {
                    header("Location: Goals.php");
                    exit();
                } else {
                    $error_message = "Error executing statement: {$stmt->error}";
                }
            } catch (Exception $e) {
                $error_message = "An error occurred: " . $e->getMessage();
            }
        }
    }
}

// Fetch goals and savings
try {
    [$result, $goalsAndSavings] = fetchGoals($conn, $userId);
    $predictions = predictSavingDate($conn, $userId);
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

// Function definitions
$goalsAndSavings = getGoalsAndSavings($conn, $userId);
function fetchGoals($conn, $userId) {
    $sql = "SELECT subject, category FROM goals WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $goalsAndSavings = getGoalsAndSavings($conn, $userId);
        return [$result, $goalsAndSavings];
        } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
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
        $savingsSql = "SELECT subject, savings_amount FROM savings WHERE user_id = ?";
        $stmt = $conn->prepare($savingsSql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $savingsResult = $stmt->get_result();
        $savings = $savingsResult->fetch_all(MYSQLI_ASSOC);
        
        // Calculate total balance for each subject
        $totalBalances = [];
        foreach ($savings as $saving) {
            $totalBalances[$saving['subject']] = ($totalBalances[$saving['subject']] ?? 0) + $saving['savings_amount'];
        }
        
    // Calculate the percentage of total balance to budget limit for each goal
    $results = [];
    foreach ($goals as $goal) {
        $totalBalance = $totalBalances[$goal['subject']] ?? 0;
        $percentage = $goal['budget_limit'] ? min(($totalBalance / $goal['budget_limit']) * 100, 100) : 0; // Ensure percentage does not exceed 100%
        $percentage = number_format($percentage, 2); // Limit percentage to 2 decimal places
        $results[] = [
            'subject' => $goal['subject'],
            'totalBalance' => $totalBalance,
            'budgetLimit' => $goal['budget_limit'],
            'percentage' => $percentage
        ];
    }
    return $results;
}

function predictSavingDate($conn, $userId) {
    // Fetch goals
    $goalsPredict = "SELECT user_id, subject, start_date, budget_limit FROM goals WHERE user_id = ?";
    $stmtGoals = $conn->prepare($goalsPredict);
    $stmtGoals->bind_param("i", $userId);
    $stmtGoals->execute();
    $goalsResult = $stmtGoals->get_result();
    $goals = $goalsResult->fetch_all(MYSQLI_ASSOC);
    $stmtGoals->close(); // Close the statement after fetching goals
    
    // Fetch savings
    $savingsPredict = "SELECT subject, category, savings_amount AS balance, date FROM savings WHERE user_id = ?";
    $stmtSavings = $conn->prepare($savingsPredict);
    $stmtSavings->bind_param("i", $userId);
    $stmtSavings->execute();
    $savingsResult = $stmtSavings->get_result();
    $savings = $savingsResult->fetch_all(MYSQLI_ASSOC);
    $stmtSavings->close(); // Close the statement after fetching savings
    
    // Prepare an array to hold predictions
    $predictions = [];
    
    // Step 1: Determine the latest date for each savings subject
    $latestSavings = [];
    foreach ($savings as $saving) {
        $subject = $saving['subject'];
        $date = new DateTime($saving['date']);
    
        // Store the latest savings entry for each subject
        if (!isset($latestSavings[$subject]) || $date > new DateTime($latestSavings[$subject]['date'])) {
            $latestSavings[$subject] = $saving; // Store the entire saving entry
        }
    }
    
    // Step 2: Calculate the total savings based on the latest entries
    foreach ($goals as $goal) {
        $goalSubject = $goal['subject'];
        $budgetLimit = $goal['budget_limit'];
    
        // Check if there is a latest saving for the goal subject
        $latestTotalSavings = isset($latestSavings[$goalSubject]['savings_amount']) ? $latestSavings[$goalSubject]['savings_amount'] : 0;
    
        // Initialize total savings
        $totalSavings = 0;

        // Calculate the remaining amount needed to reach the goal
        $remainingAmount = $budgetLimit - $totalSavings;
    
        // If the goal is already met
        if ($remainingAmount <= 0) {
            $predictions[$goalSubject] = "You have already reached your goal.";
            continue;
        }
    
        // Initialize savings based on frequency
        $totalSavings = 0; // Initialize a variable to hold the total savings

        foreach ($savings as $saving) {
            // Check if the saving's subject matches the goal subject
            if ($saving['subject'] === $goalSubject) {
                // Accumulate the savings
                $totalSavings += $saving['savings_amount'] ?? 0;
            }
        }
    
        // Calculate the number of days needed to reach the goal
        $daysNeeded = PHP_INT_MAX;
    
        // Calculate days needed based on savings contributions
        if ($totalSavings > 0 && isset($latestSavings[$goalSubject]['date'])) {
            $latestDate = new DateTime($latestSavings[$goalSubject]['date']);
            $daysSinceStart = $latestDate->diff(new DateTime($goal['start_date']))->days;
            if ($daysSinceStart > 0) {
                $dailySavingsRate = $totalSavings / $daysSinceStart;
                if ($dailySavingsRate > 0) {
                    $daysNeeded = ceil($remainingAmount / $dailySavingsRate);
                }
            }
        }
        
        // Calculate the target date
        if ($daysNeeded === PHP_INT_MAX) {
            $predictions[$goalSubject] = "N/A"; // No valid savings to reach the goal
        } else {
            $targetDate = new DateTime();
            $targetDate->add(new DateInterval("P{$daysNeeded}D"));
            $predictions[$goalSubject] = $targetDate->format("Y-m-d");
            
            // Update the prediction date in the goals table for the specific goal
            $updateQuery = "UPDATE goals SET `date` = ? WHERE user_id = ? AND subject = ?";
            $stmtUpdate = $conn->prepare($updateQuery);
            if ($stmtUpdate) {
                $stmtUpdate->bind_param("sis", $predictions[$goalSubject], $userId, $goalSubject);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            } else {
                throw new Exception("Error preparing update statement: {$conn->error}");
            }
        }
    }
    
    return $predictions;
}

function searchGoalsBySubject($conn, $userId, $searchQuery) {
    $query = "SELECT * FROM goals WHERE user_id = ? AND subject LIKE ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%{$searchQuery}%";
    $stmt->bind_param("is", $userId, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
// Handle search request
if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
    $result = searchGoalsBySubject($conn, $userId, $searchQuery);
}

function fetchGoalsByCategory($conn, $userId, $category, $order = 'ASC') {
    $query = "SELECT * FROM goals WHERE user_id = ?";
    if (!empty($category)) {
        $query .= " AND category LIKE ?";
    }
    $query .= " ORDER BY subject " . ($order === 'DESC' ? 'DESC' : 'ASC');

    $stmt = $conn->prepare($query);
    if (!empty($category)) {
        $searchParam = "%{$category}%";
        $stmt->bind_param("is", $userId, $searchParam);
    } else {
        $stmt->bind_param("i", $userId);
    }
    $stmt->execute();
    return $stmt->get_result();
}

function fetchGoalsByDate($conn, $userId, $order = 'ASC') {
    $query = "SELECT * FROM goals WHERE user_id = ? ORDER BY date " . ($order === 'DESC' ? 'DESC' : 'ASC');
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result();
}

// Get filter and sort parameters from the request
$currentSortOrder = $_GET['sortOrder'] ?? 'ASC';
$sortOrderDate = $_GET['sortOrderDate'] ?? 'ASC';
$nextSortOrder = ($currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
$nextSortOrderDate = ($sortOrderDate === 'ASC') ? 'DESC' : 'ASC';

// Fetch goals based on the filter and sort order
try {
    if (!empty($searchQuery)) {
        $result = fetchGoalsByCategory($conn, $userId, $searchQuery, $currentSortOrder);
    } elseif (isset($_GET['sortOrderDate'])) {
        $result = fetchGoalsByDate($conn, $userId, $sortOrderDate);
    } else {
        $result = fetchGoalsByCategory($conn, $userId, '', $currentSortOrder);
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
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
        <div class="Expenses-Nav <?php echo ($current_page == 'expense.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/expenses.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="expense.php">Expenses</a></p>
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
                            <form class="filter-form" action="" method="GET">
                                <select id="FilterGoalsCategory" name="FilterGoalsCategory">
                                    <option value="" disabled selected>Category</option>
                                    <option value="Travels">Travels</option>
                                    <option value="Miscellaneous">Miscellaneous</option>
                                    <option value="Others">Others</option>
                                </select>
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/filter.svg" alt="" width="20px"></i>
                                </button>
                            </form>
                            <form class="search-form" action="" method="GET">
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
                            <th class="tab">
                                <form class="Subject" action="" method="GET">
                                    <button type="submit" name="sortOrder" value="<?php echo htmlspecialchars($nextSortOrder); ?>">
                                        SUBJECT
                                    </button>
                                </form>
                            </th>
                            <th class="tab">CATEGORY</th>
                            <th class="tab">
                                <form class="accomplishmentDate" action="" method="GET">
                                    <button type="submit" name="sortOrderDate" value="<?php echo htmlspecialchars($nextSortOrderDate); ?>">
                                        ACCOMPLISHMENT DATE
                                    </button>
                                </form>
                            </th>
                            <th class="tab">PROGRESS</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?php
                        if (isset($result) && $result->num_rows > 0) {
                            $rowCounter = 0;
                            foreach ($result as $row) {
                                // Find the corresponding goal in $goalsAndSavings to get the percentage
                                $percentage = 0;
                                foreach ($goalsAndSavings as $goal) {
                                    if ($goal['subject'] === $row['subject']) {
                                        $percentage = $goal['percentage'];
                                        break;
                                    }
                                }
                                $rowClass = ($rowCounter % 2 == 0) ? 'row-color-1' : 'row-color-2';

                                echo "<tr class='" . htmlspecialchars($rowClass) . "'>
                                        <td><div class='sub'>" . htmlspecialchars($row['subject']) . "</div></td>
                                        <td>" . htmlspecialchars($row['category']) . "</td>
                                        <td>" . htmlspecialchars($predictions[$row['subject']] ?? 'N/A') . "</td>
                                        <td class='progress-row'>
                                            <div class='progress-container'>
                                                <div class='progress-bar1' style='width: " . htmlspecialchars($percentage) . "%;'></div>
                                            </div>
                                            <div class='progress-text'>
                                                <span>" . htmlspecialchars($percentage) . "%</span>
                                            </div>
                                        </td>
                                    </tr>";

                                $rowCounter++;
                            }
                        } else {
                            echo "<tr><td colspan='4'>No results found</td></tr>";
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
                    <div class="Goal-Form-Format" id="Subject-Row">
                        <label for="Subject" class="Goals-Label">Subject*</label>
                        <input type="text" id="Subject" name="Subject" required style="text-transform: capitalize;">
                    </div>
                    <div class="Goal-Form-Format" id="Category-Row">
                        <label for="GoalsCategory" class="Goals-Label">Category*</label>
                        <select id="GoalsCategory" name="GoalsCategory" required>
                            <option value="" disabled selected>Category</option>
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