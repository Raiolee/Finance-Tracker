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

<body>
    <div class="container">
        <?php include('navbar.php'); ?>

        <section class="main-section">
            <div class="main-container">
                <div class="content scrollable">
                    <!-- Top bar section -->
                    <div class="top-bar space-between" id="expense">
                        <h1 class="header" id="headerText">Goals</h1>
                        <div class="goals-header">
                            <button class="New-Saving" id="newGoalsBtn" onclick="showGoalForm()">+ Add a Goal</button>
                            <!-- Filter form -->
                            <form class="filter-form" id="filterForm" action="" method="GET">
                                <select class="var-input medium pointer" id="FilterGoalsCategory" name="FilterGoalsCategory">
                                    <option value="" disabled selected>Category</option>
                                    <option value="Travels">Travels</option>
                                    <option value="Miscellaneous">Miscellaneous</option>
                                    <option value="Others">Others</option>
                                </select>
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/filter.svg" alt=""></i>
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
                    </div>

                    <!-- Goals table -->
                    <table id="goalsTable" class="table-approval">
                        <thead>
                            <tr>
                                <th class="th-interact" onclick="sortTable('subject')">
                                    SUBJECT
                                </th>
                                <th>
                                    CATEGORY
                                </th>
                                <th onclick="sortTable('accomplishment_date')">
                                    ACCOMPLISHMENT DATE
                                </th>
                                <th>
                                    PROGRESS
                                </th>
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

                    <!-- Add Goal Form -->
                    <form id="GoalForm" method="post" class="pfp-form" style="display:none;">
                        <div class="big-divider full">
                            <label for="Start-Date" class="form-labels">Start Date*</label>
                            <input class="date-input medium" type="date" id="Start-Date" name="Start-Date" required>

                            <label for="Subject" class="form-labels">Subject*</label>
                            <input class="var-input medium" type="text" id="Subject" name="Subject" required style="text-transform: capitalize;">

                            <label for="GoalsCategory" class="form-labels">Category*</label>
                            <select class="date-input" id="GoalsCategory" name="GoalsCategory" required>
                                <option value="" disabled selected>Category</option>
                                <option value="Travels">Travels</option>
                                <option value="Miscellaneous">Miscellaneous</option>
                                <option value="Others">Others</option>
                            </select>

                            <label for="Target-Amount" class="form-labels">Target Amount*</label>
                            <input class="var-input" type="number" id="Target-Amount" name="Target-Amount" required>

                            <label for="Description" class="form-labels">Description*</label>
                            <textarea class="text-input medium" id="Description" name="Description" required></textarea>


                            <div class="btn-options">
                                <button type="button" class="cancel" onclick="closeGoalForm()">Cancel</button>
                                <button type="submit" name="submit-form" class="save">Save</button>
                            </div>
                    </form>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/goals-form.js"></script>
</body>
</html>