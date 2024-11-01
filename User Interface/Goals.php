<?php
include '../connection/config.php';
include '../APIs/goals_api.php'; // Include the new API file

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
$username = $_SESSION["name"];

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

<<<<<<< HEAD
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
    $goalsPredict = "SELECT user_id, subject, start_date, budget_limit, date FROM goals WHERE user_id = ?";
    $stmtGoals = $conn->prepare($goalsPredict);
    $stmtGoals->bind_param("i", $userId);
    $stmtGoals->execute();
    $goalsResult = $stmtGoals->get_result();
    $goals = $goalsResult->fetch_all(MYSQLI_ASSOC);
    $stmtGoals->close(); // Close the statement after fetching goals

    // Fetch savings
    $savingsPredict = "SELECT subject, category, savings_amount, date FROM savings WHERE user_id = ?";
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
    $totalSavings = isset($latestSavings[$goalSubject]) ? $latestSavings[$goalSubject]['savings_amount'] : 0;

    // Calculate the remaining amount needed to reach the goal
    $remainingAmount = $budgetLimit - $totalSavings;

    // If the goal is already met
    if ($remainingAmount <= 0) {
        $predictions[$goalSubject] = "You have already reached your goal.";
        continue;
    }

    // Initialize savings based on frequency
    $savingsByCategory = [
        'Weekly' => 0,
        'Monthly' => 0,
    ];

    // Step 3: Accumulate savings based on the latest entries
    foreach ($savings as $saving) {
        if ($saving['subject'] === $goalSubject && isset($latestSavings[$goalSubject]) && $saving['date'] === $latestSavings[$goalSubject]['date']) {
            $savingsByCategory[$saving['category']] += $saving['savings_amount'];
        }
    }

    // Calculate the number of days needed to reach the goal
    $daysNeeded = PHP_INT_MAX;

    // Calculate days needed based on savings contributions
    foreach ($savingsByCategory as $category => $amount) {
        if ($amount > 0) {
            switch ($category) {
                case 'Weekly':
                    $daysNeeded = min($daysNeeded, ceil($remainingAmount / $amount) * 7);
                    break;
                case 'Monthly':
                    $daysNeeded = min($daysNeeded, ceil($remainingAmount / $amount) * 30); // Approximate month as 30 days
                    break;
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

function searchGoals($conn, $userId, $searchQuery) {
    $sql = "SELECT subject, category FROM goals WHERE user_id = ? AND (subject LIKE ? OR category LIKE ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $likeQuery = "%{$searchQuery}%";
        $stmt->bind_param("iss", $userId, $likeQuery, $likeQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        $goalsAndSavings = getGoalsAndSavings($conn, $userId);
        $predictions = predictSavingDate($conn, $userId);
        return [$result, $goalsAndSavings, $predictions];
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
    try {
        [$result, $goalsAndSavings, $predictions] = searchGoals($conn, $userId, $searchQuery);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

function filterCategory($conn, $userId, $filterCategory) {
    $sql = "SELECT subject, category FROM goals WHERE user_id = ? AND category = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("is", $userId, $filterCategory);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

if (isset($_GET['FilterGoalsCategory'])) {
    $filterCategory = $_GET['FilterGoalsCategory'];
    try {
        $result = filterCategory($conn, $userId, $filterCategory);
        $goalsAndSavings = getGoalsAndSavings($conn, $userId);
        $predictions = predictSavingDate($conn, $userId);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$currentSortOrderSubject = $_GET['sortOrderSubject'] ?? 'asc';
$nextSortOrderSubject = getNextSortOrder($currentSortOrderSubject);
function getNextSortOrder($currentSortOrder) {
    return $currentSortOrder === 'asc' ? 'desc' : 'asc';
}

if (isset($_GET['sortOrderSubject'])) {
    $sortOrderSubject = $_GET['sortOrderSubject'];
    $sql = "SELECT subject, category FROM goals WHERE user_id = ? ORDER BY subject $sortOrderSubject";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $goalsAndSavings = getGoalsAndSavings($conn, $userId);
        $predictions = predictSavingDate($conn, $userId);
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

$currentSortOrderDate = $_GET['sortOrderDate'] ?? 'asc';
$nextSortOrderDate = getNextSortOrder($currentSortOrderDate);

function sortGoalsByDate($conn, $userId, $sortOrderDate) {
    $sql = "SELECT subject, category, date FROM goals WHERE user_id = ? ORDER BY date $sortOrderDate";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $goalsAndSavings = getGoalsAndSavings($conn, $userId);
        $predictions = predictSavingDate($conn, $userId);
        return [$result, $goalsAndSavings, $predictions];
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

if (isset($_GET['sortOrderDate'])) {
    $sortOrderDate = $_GET['sortOrderDate'];
    try {
        [$result, $goalsAndSavings, $predictions] = sortGoalsByDate($conn, $userId, $sortOrderDate);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Goals</title>
</head>

<<<<<<< HEAD
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
                                <input type="search" name="query" placeholder="Search here ..." style="text-transform: capitalize;">
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
                                    <button type="submit" name="sortOrderSubject" value="<?php echo htmlspecialchars($nextSortOrderSubject); ?>">
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
=======
<body>
    <div class="container">
        <div class="navbar">
            <div class="Profile">
                <div class="Profile_img">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="110">
                </div>
>>>>>>> f2195ada70cbbbcbba663ef04a3b1cab9d14c5c2
            </div>

            <div class="user-name">
                <p><?php echo htmlspecialchars($username); ?></p>
            </div>

            <!-- Home Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/home.svg" alt="Icon">
                <p><a class="navbar-items" href="Dashboard.php">Home</a></p>
            </div>

            <!-- Expenses Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'expense.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/expenses.svg" alt="Icon">
                <p><a class="navbar-items" href="expense.php">Expenses</a></p>
            </div>

            <!-- Income Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Income.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/income.svg" alt="Icon">
                <p><a class="navbar-items" href="Income.php">Income</a></p>
            </div>

            <!-- Goal Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Goals.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/approvals.svg" alt="Icon">
                <p><a class="navbar-items" href="Goals.php">Goals</a></p>
            </div>

            <!-- Savings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Savings.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/reports.svg" alt="Icon">
                <p><a class="navbar-items" href="Savings.php">Banks</a></p>
            </div>

            <!-- Settings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Settings.php' || $current_page == 'profile.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
                <p><a class="navbar-items" href="Settings.php">Settings</a></p>
            </div>

            <div class="Logo-Nav" id="Nav_Side">
                <div class="Penny_Logo">
                    <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
                </div>
            </div>
        </div>

        <section class="main-section">
            <div class="main-container">
                <div class="content scrollable">
                    <!-- Top bar section -->
                    <div class="top-bar space-between" id="expense">
                        <h1 class="header" id="headerText">Goals</h1>
                        <button class="New-Saving" id="newGoalsBtn" onclick="showGoalForm()">+ Add a Goal</button>

                        <!-- Filter form -->
                        <form class="filter-form" id="filterForm" action="" method="GET">
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

                        <!-- Search form -->
                        <form class="search-form" id="searchForm" action="" method="GET">
                            <input type="search" name="query" placeholder="Search here ...">
                            <button type="submit">
                                <i class="fa"><img src="../Assets/Icons/magnifying-glass.svg" alt="" width="20px"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Goals table -->
                    <table id="goalsTable" class="table-approval">
                        <thead>
                            <tr>
                                <th>
                                    <form class="Subject" action="" method="GET">
                                        <button type="submit" name="sortOrder" value="<?php echo htmlspecialchars($nextSortOrder); ?>">
                                            SUBJECT
                                        </button>
                                    </form>
                                </th>
                                <th>CATEGORY</th>
                                <th>
                                    <form class="accomplishmentDate" action="" method="GET">
                                        <button type="submit" name="sortOrderDate" value="<?php echo htmlspecialchars($nextSortOrderDate); ?>">
                                            ACCOMPLISHMENT DATE
                                        </button>
                                    </form>
                                </th>
                                <th>PROGRESS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($result) && $result->num_rows > 0) {
                                $rowCounter = 0;
                                foreach ($result as $row) {
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

                    <form id="GoalForm" method="post" style="display:none;">
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
        </section>
    </div>
    <script src="../js/goals-form.js"></script>
</body>

</html>