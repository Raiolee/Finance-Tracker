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

// Fetch goals based on the filter and sort order
$currentSortOrder = $_GET['sortOrder'] ?? 'ASC';
$sortOrderDate = $_GET['sortOrderDate'] ?? 'ASC';
$nextSortOrder = ($currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
$nextSortOrderDate = ($sortOrderDate === 'ASC') ? 'DESC' : 'ASC';

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
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Goals</title>
</head>

<body>
    <div class="container">
        <div class="navbar">
            <div class="Profile">
                <div class="Profile_img">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="110">
                </div>
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