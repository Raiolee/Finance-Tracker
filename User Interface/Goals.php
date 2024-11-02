<?php
include '../connection/config.php';
include '../APIs/goals_api.php'; // Include the new API file

// Create a connection
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link rel="stylesheet" href="../Styles/custom-style.css">
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
                        <div class="custom-header">
                            <button class="New-Saving" id="newGoalBtn">+ Add a Goal</button>
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
                                <th class="th-interact" onclick="window.location.href='?sortforsubject&order=<?php echo ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc'; ?>'">SUBJECT</th>
                                <th>
                                    CATEGORY
                                </th>
                                <th class="th-interact" onclick="window.location.href='?sort=date&order=<?php echo ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc'; ?>'">ACCOMPLISHMENT DATE</th>
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
                </div>
            </div>
    </div>
    </div>
    <?php include('modals/modal-goals.php'); ?>
    <script src="../js/modal.js"></script>
</body>

</html>