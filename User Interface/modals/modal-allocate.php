<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
    exit();
}

$uid = $_SESSION["user_id"];
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);

// Include database connection
include '../connection/config.php';

// Fetch only the user_dp (profile picture) from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch existing goals for the user
$sql3 = "SELECT subject, category FROM user_db.goals WHERE user_id = ?";
$stmt3 = $conn->prepare($sql3);

if ($stmt3) {
    $stmt3->bind_param("i", $uid);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
} else {
    $error_message = "Error preparing statement: {$conn->error}";
}

function getCategoryOptions() {
    global $result3; // Use global to access $result3 inside the function
    $options = '';

    if (isset($result3) && $result3->num_rows > 0) {
        while ($row3 = $result3->fetch_assoc()) {
            $options .= '<option value="' . htmlspecialchars($row3['subject']) . '">' . htmlspecialchars($row3['subject']) . '</option>';
        }
    } else {
        $options .= '<option value="">No categories found</option>'; // Default option
    }

    return $options; // Return the generated options
}
?>

<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="bankModalAllocate" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModalAllocate()">&times;</span>
        <h3 class="header">Allocate to Goals</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <div class="big-divider full">
                <label class="form-labels" for="bank">Bank</label>
                <input class="var-input" type="text" name="bank" value="CHANGE ME" readonly>
                
                <label class="form-labels" for="bank-balance">Remaining Balance</label>
                <input class="var-input" type="number" name="bank-balance" value="CHANGE ME" readonly>
                
                <label class="form-labels" for="bank-allocate">Goal</label>
                <select class="var-input" name="bank-category" id="bank-category" required>
                    <?php echo getCategoryOptions(); ?> <!-- Function call correctly placed -->
                </select>
                
                <label class="form-labels" for="allocate-amount">Amount</label>
                <input class="var-input" type="number" id="bank-amount" name="allocate-amount" placeholder="0.00" required>
                
                <label class="form-labels" for="allocate-date">Date</label>
                <input class="var-input" type="date" id="allocate-date" name="allocate-date" required>
                
                <label class="form-labels" for="frequency">Frequency</label>
                <select class="var-input" name="frequency" id="frequency">
                    <option value="Once">Once</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Monthly">Monthly</option>
                </select>

                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModalAllocate()">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
