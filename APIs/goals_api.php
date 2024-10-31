<?php
include '../connection/config.php';

// Fetch goals and savings
function fetchGoals($conn, $userId)
{
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

function predictSavingDate($conn, $userId)
{
    // Fetch goals
    $goalsPredict = "SELECT user_id, subject, start_date, budget_limit FROM goals WHERE user_id = ?";
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

function searchGoalsBySubject($conn, $userId, $query)
{
    $sql = "SELECT subject, category FROM goals WHERE user_id = ? AND subject LIKE ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $likeQuery = "%{$query}%";
        $stmt->bind_param("is", $userId, $likeQuery);
        $stmt->execute();
        return $stmt->get_result();
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

// Handle search request
if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
    try {
        $result = searchGoalsBySubject($conn, $userId, $searchQuery);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

function fetchGoalsByCategory($conn, $userId, $category, $order = 'ASC')
{
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

function fetchGoalsByDate($conn, $userId, $order = 'ASC')
{
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

// Determine which goals to fetch based on request parameters
if (isset($_GET['filter']) && $_GET['filter'] === 'category') {
    $category = $_GET['category'] ?? '';
    $goals = fetchGoalsByCategory($conn, $userId, $category, $currentSortOrder);
} elseif (isset($_GET['filter']) && $_GET['filter'] === 'date') {
    $goals = fetchGoalsByDate($conn, $userId, $sortOrderDate);
} else {
    [$goals, $goalsAndSavings] = fetchGoals($conn, $userId);
}

// Format and return the results as JSON
$response = [
    'goals' => $goals->fetch_all(MYSQLI_ASSOC),
    'goalsAndSavings' => $goalsAndSavings,
    'predictions' => predictSavingDate($conn, $userId),
    'searchResults' => isset($result) ? $result->fetch_all(MYSQLI_ASSOC) : []
];

header('Content-Type: application/json');
echo json_encode($response);
?>
