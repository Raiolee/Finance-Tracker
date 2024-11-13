<?php
// Include database connection
include '../connection/config.php';

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
        $options .= '<option value="">No Goals found</option>'; // Default option
    }

    return $options; // Return the generated options
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/modal-styles.scss">
    <title>Allocate to Goals</title>
</head>
<body>
<!-- Modal Structure -->
<div id="bankModalAllocate" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModalAllocate()">&times;</span>
        <h3 class="header">Allocate to Goals</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <input type="hidden" name="action" value="another_action">
            
            <div class="big-divider full">
                <input type="hidden" name="bank_id" readonly>
                <label class="form-labels" for="bank">Bank*</label>
                <input class="var-input" type="text" name="bank" readonly>
                
                <label class="form-labels" for="bank-balance">Remaining Balance*</label>
                <input class="var-input" type="number" name="bank-balance" readonly>
                
                <label class="form-labels" for="bank-category">Goal*</label>
                <select class="var-input" name="bank-category" id="bank-category" required>
                    <?php echo getCategoryOptions(); ?> <!-- Function call correctly placed -->
                </select>
                
                <label class="form-labels" for="allocate-amount">Amount*</label>
                <input class="var-input" type="number" id="allocate-amount" name="allocate-amount" placeholder="0.00" required>
                
                <label class="form-labels" for="allocate-date">Date*</label>
                <input class="var-input" type="date" id="allocate-date" name="allocate-date" value="<?php echo date('Y-m-d'); ?>" required>
                
                <label class="form-labels" for="frequency">Frequency*</label>
                <select class="var-input" name="frequency" id="frequency">
                    <option value="Once">Once</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Monthly">Monthly</option>
                </select>

                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModalAllocate()">Cancel</button>
                    <button type="submit" class="save" >Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
