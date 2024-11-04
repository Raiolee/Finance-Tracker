<?php
session_start();
$conn = new mysqli($DB_Host, $DB_User, $DB_Password, $DB_Name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);


if (isset($_POST['submit-form'])) {
    $startDate = $_POST['start-date'];
    $subject = $_POST['name'];
    $category = $_POST['goal-category'];
    $description = $_POST['goal-description'];
    $budgetLimit = $_POST['target-amount'];
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


// Fetch goals and savings
try {
    [$result, $goalsAndSavings] = fetchGoals($conn, $userId);
    $predictions = predictSavingDate($conn, $userId);
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    try {
        [$result] = searchGoals($conn, $user_id, $searchQuery);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

if (isset($_GET['FilterGoalsCategory'])) {
    $filterCategory = $_GET['FilterGoalsCategory'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    try {
        $result = filterCategory($conn, $user_id, $filterCategory);
        $goalsAndSavings = getGoalsAndSavings($conn, $user_id);
        $predictions = predictSavingDate($conn, $user_id);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

if (isset($_GET['sortforsubject'])) {
    try {
        $order = $_GET['order'] ?? 'desc';
        $result = sortGoalsBySubject($conn, $userId,  $order);
        $goalsAndSavings = getGoalsAndSavings($conn, $userId);
        $predictions = predictSavingDate($conn, $userId);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

if (isset($_GET['sort'])) {
    try {
        $order = $_GET['order'] ?? 'asc';
        $result = sortGoalsByDate($conn, $userId, $order);
        $goalsAndSavings = getGoalsAndSavings($conn, $userId);
        $predictions = predictSavingDate($conn, $userId);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

include '../connection/config.php';
// Fetch goals and savings
function fetchGoals($conn, $userId)
{
    $sql = "SELECT goal_id, subject, category FROM goals WHERE user_id = ?";
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

function getGoalsAndSavings($conn, $userId)
{
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
            'Once' => 0,
            'Daily' => 0,
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
                    case 'Once':
                        $daysNeeded = min($daysNeeded, ceil($remainingAmount / $amount));
                        break;
                    case 'Daily':
                        $daysNeeded = min($daysNeeded, ceil($remainingAmount / $amount) + 1);
                        break;
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

function sortGoalsBySubject($conn, $userId, $order) {
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
    $query = "SELECT * FROM goals WHERE user_id = ? ORDER BY subject $order";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

function sortGoalsByDate($conn, $userId, $order) {
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
    $query = "SELECT * FROM goals WHERE user_id = ? ORDER BY date $order";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

