<?php
// Include database connection
include '../connection/config.php';

// Fetch existing goals for the user
$sql4 = "SELECT bank FROM user_db.bank WHERE user_id = ?";
$stmt4 = $conn->prepare($sql4);

if ($stmt4) {
    $stmt4->bind_param("i", $uid);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
} else {
    $error_message = "Error preparing statement: {$conn->error}";
}
function getBankOptions($conn, $uid)
{
    $options = '';

    $sql4 = "SELECT bank_id, bank, purpose FROM user_db.bank WHERE user_id = ?";
    $stmt4 = $conn->prepare($sql4);

    if ($stmt4) {
        $stmt4->bind_param("i", $uid);
        $stmt4->execute();
        $result4 = $stmt4->get_result();

        if ($result4 && $result4->num_rows > 0) {
            while ($row4 = $result4->fetch_assoc()) {
                // Corrected line
                $options .= '<option value="' . htmlspecialchars($row4['bank_id']) . '">' . htmlspecialchars($row4['bank']) . ' (' . htmlspecialchars($row4['purpose']) . ')</option>';
            }
        } else {
            $options .= '<option value="">No Bank found</option>';
        }

        $stmt4->close();
    } else {
        $options .= '<option value="">Error fetching banks</option>';
    }

    return $options;
}

function sanitizeInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $merchant = sanitizeInput($_POST["merchant"]);
    $bank = sanitizeInput($_POST["bank"]);
    $amount = sanitizeInput($_POST["amount"]);
    $description = sanitizeInput($_POST["description"]);
    $reimbursable = sanitizeInput($_POST["reimbursable"]);
    // Process the sanitized input values
}


?>

<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="expenseModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModalExpense()">&times;</span>
        <h3 class="header">Add an Expense</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="POST" action="">
            <input type="hidden" name="action" value="insert_expense">
            <input type="hidden" name="expense_id" id="expense_id">
            <div class="big-divider full">
                <!-- Subject Field -->
                <label for="subject" class="form-labels row">Subject</label>
                <input type="text" class="var-input medium" id="subject" name="subject" required>

                <!-- Category Field -->
                <label for="category" class="form-labels row medium">Category</label>
                <select class="var-input" id="category" name="category" required>
                    <option value="food">Food</option>
                    <option value="transport">Transport</option>
                    <option value="entertainment">Entertainment</option>
                    <option value="utilities">Utilities</option>
                    <option value="other">Other</option>
                </select>

                <!-- Date Field -->
                <label for="expense-date" class="form-labels row">Date</label>
                <input type="date" class="var-input" id="expense-date" name="expense-date"
                    value="<?php echo date('Y-m-d'); ?>" required>

                <!-- Frequency Field -->
                <label for="recurrence_type" class="form-labels row">Frequency</label>
                <select class="var-input large pointer" name="recurrence_type" id="recurrence_type" required>
                    <option value="once">Once</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom</option>
                </select>

                <!-- Merchant Field -->
                <label for="merchant" class="form-labels">Merchant</label>
                <input type="text" class="var-input" id="merchant" name="merchant" required>

                <!-- Bank Selection -->
                <label for="bank" class="form-labels row">Bank</label>
                <select class="var-input large pointer" name="bank" id="bank" required>
                    <?php echo getBankOptions($conn, $uid); ?>
                </select>

                <!-- Amount Field -->
                <label for="amount" class="form-labels">Amount*</label>
                <input type="number" class="var-input" id="amount" name="amount" required>

                <!-- Description Field -->
                <label for="description" class="form-labels">Description</label>
                <textarea class="var-input" name="description" id="description"></textarea>

                <!-- Reimbursable Option -->
                <label for="reimbursable" class="form-labels">Reimbursable</label>
                <div class="column-form start">
                    <input class="radio" type="radio" name="reimbursable" id="reimbursable-yes" value="yes">
                    <label class="form-labels center no-margin" for="reimbursable-yes">Yes</label>

                    <input class="radio" type="radio" name="reimbursable" id="reimbursable-no" value="no" checked>
                    <label class="form-labels center no-margin" for="reimbursable-no">No</label>
                </div>

                <!-- Attachment Field -->
                <label for="attachment" class="file-label" id="file-label">Attach Receipt</label>
                <input class="file-input" type="file" name="attachment" accept="image/*" id="file-input">

                <!-- Action Buttons -->
                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModalExpense()">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>

    </div>
</div>