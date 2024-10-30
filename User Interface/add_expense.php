<?php
// Include database connection
include('../connection/config.php');

// Start session and fetch user data from session
session_start();
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
$user_id = $_SESSION['user_id'];

// Fetch user data, change according to what is needed
$query = "SELECT user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Prepare the profile picture for display
if ($user['user_dp']) {
    // Convert BLOB to base64-encoded image
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    // If no profile picture is found, use a placeholder image
    $profile_pic = 'https://picsum.photos/100/100';
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- All content is stored in container -->
    <div class="container">
        <div class="navbar">
            <!-- Profile Picture -->
            <div class="Profile">
                <div class="Profile_img">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="110">
                </div>
            </div>
            <!-- Username Section -->
            <div class="user-name">
                <p><?php echo htmlspecialchars($username); ?></p>
            </div>

            <!-- Home Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/home.svg" alt="Icon">
                <p><a class="navbar-items" href="Dashboard.php">Home</a></p>
            </div>

            <!-- Expenses Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/expenses.svg" alt="Icon">
                <p><a class="navbar-items" href="expense.php">Expenses</a></p>
            </div>

            <!-- Income Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'expense.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/income.svg" alt="Icon">
                <p><a class="navbar-items" href="Income.php">Income</a></p>
            </div>

            <!-- Goal Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Goals.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/approvals.svg" alt="Icon">
                <p><a class="navbar-items" href="Goals.php">Goals</a></p>
            </div>

            <!-- Savings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Savings.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/reports.svg" alt="Icon">
                <p><a class="navbar-items" href="Savings.php">Savings</a></p>
            </div>

            <!-- Settings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Settings.php' || $current_page == 'profile.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
                <p><a class="navbar-items" href="Settings.php">Settings</a></p>
            </div>
            <!-- Logo in the navbar -->
            <div class="Logo-Nav" id="Nav_Side">
                <div class="Penny_Logo">
                    <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
                </div>
            </div>
        </div>
        <!-- Main Section -->
        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <div class="top-bar">
                        <h1 class="header">Add an Expense</h1>
                    </div>
                    <form id="addExpense" class="pfp-form" action="add_expense.php" method="POST" enctype="multipart/form-data">
                        <div class="big-divider full center">
                            <div class="row-form no-margin">
                                <div class="column-form">
                                    <label for="name" class="form-labels row">Subject</label>
                                    <input type="text" class="var-input medium" id="name" name="subject">
                                    <label for="category" class="form-labels row medium">Category</label>
                                    <select class="date-input medium" id="category" name="category">
                                        <option value="food">Food</option>
                                        <option value="transport">Transport</option>
                                        <option value="entertainment">Entertainment</option>
                                        <option value="utilities">Utilities</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="column-form">
                                    <label for="name" class="form-labels row">Date</label>
                                    <input type="date" class="date-input" id="name" name="date">
                                    <label for="recurrence_type" class="form-labels row">Frequency</label>
                                    <select class="var-input large" name="recurrence_type" id="recurrence_type">
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>
                                <label for="name" class="form-labels">Merchant</label>
                                <input type="text" class="var-input" id="name" name="merchant">
                                <label for="name" class="form-labels">Amount</label>
                                <input type="number" class="var-input" id="amount" name="amount" step="100.00">
                                <label for="name" class="form-labels">Description</label>
                                <textarea class="text-input" name="description" id="description"></textarea>

                                <!-- Reimbursable -->
                                <div class="column-form start">
                                    <label for="reimbursable" class="form-labels">Reimbursable</label>
                                    <input class="radio" type="radio" name="reimbursable" id="reimbursable-yes" value="yes">
                                    <label class="form-labels center no-margin" for="reimbursable-yes">Yes</label>

                                    <input class="radio" type="radio" name="reimbursable" id="reimbursable-no" value="no" checked>
                                    <label class="form-labels center no-margin" for="reimbursable-no">No</label>
                                </div>
                                
                                <!-- file input/ photo input -->
                                <label for="attachment" class="file-label" id="file-label">Attach Receipt</label>
                                <input class="file-input" type="file" name="attachment" accept="image/*" id="file-input">

                                <div class="btn-options center" id="report-btns">
                                    <a href="expense.php" class="link-btn"><button type="button" class="cancel">Cancel</button></a>
                                    <button type="submit" name="save" class="save">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    </div>
    <!-- APIs (Put APIs below this comment)-->
    <script src="../js/add_expense.js"></script>
</body>

</html>

<?php
// PHP Backend for add_expense.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch form data
    $subject = $_POST['subject'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $frequency = $_POST['recurrence_type'];
    $merchant = $_POST['merchant'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $reimbursable = ($_POST['reimbursable'] == 'yes') ? 'yes' : 'no';

    // Handle file upload
    $receipt = null;
    if (!empty($_FILES['receipt']['tmp_name'])) {
        $receipt = file_get_contents($_FILES['receipt']['tmp_name']);
    }

    // Calculate the next occurrence date
    $next_occurrence = $date;
    if ($frequency == 'weekly') {
        $next_occurrence = date('Y-m-d', strtotime($date . ' +1 week'));
    } elseif ($frequency == 'monthly') {
        $next_occurrence = date('Y-m-d', strtotime($date . ' +1 month'));
    }

    // Insert data into the database
    $sql = "INSERT INTO expenses (user_id, subject, date, recurrence_type, frequency, category, reimbursable, merchant, amount, description, receipt) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssdss", $user_id, $subject, $date, $frequency, $next_occurrence, $category, $reimbursable, $merchant, $amount, $description, $receipt);

    if ($stmt->execute()) {
        echo "<p>Expense added successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>