<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="incomeRowModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeIncomeRowModal()">&times;</span>
        <h3 class="header">Delete Income</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <div class="big-divider full">

                <!-- incomeSourceRow -->
                <label class="form-labels" for="source">Source of Income*</label>
                <input class="var-input" type="text" name="source" placeholder="Source of Income" id="incomeSourceRow" readonly>

                <!-- incomeDateRow -->
                <label class="form-labels" name="date" for="date"> Date*</label>
                <input class="var-input" type="date" name="date" id="incomeDateRow" required>

                <!-- incomeCategoryRow -->
                <label class="form-labels" for="income-category">Frequency*</label>
                <select class="var-input" name="income_category" id="incomeCategoryRow" readonly>
                    <option value="Once">Once</option>
                    <option value="Daily">Daily</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Monthly">Monthly</option>
                </select>

                <!-- incomeBankRow -->
                <label class="form-labels" for="income-bank">Bank Name*</label>
                <select class="var-input" name="bank_name" id="incomeBankRow" readonly>
                    <option value="CHANGE ME">Option 1</option>
                    <?php
                    $uid = $_SESSION["user_id"];
                    // Fetch bank names from the database
                    $bankQuery = "SELECT bank FROM bank WHERE user_id = ?";
                    $bankStmt = $conn->prepare($bankQuery);
                    $bankStmt->bind_param("i", $uid);
                    $bankStmt->execute();
                    $bankResult = $bankStmt->get_result();
                    while ($row = $bankResult->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['bank']) . '">' . htmlspecialchars($row['bank']) . '</option>';
                    }
                    $bankStmt->close();
                    ?>
                </select>

                <!-- incomeAmountRow -->
                <label class="form-labels" for="income-amount">Amount*</label>
                <input class="var-input" type="number" id="incomeAmountRow" name="income-amount" placeholder="0.00" readonly>

                <!-- incomeDescriptionRow -->
                <label class="form-labels" for="income-description">Description</label>
                <textarea class="var-input" name="income-description" id="incomeDescriptionRow" placeholder="Description"></textarea>

                <div class="btn-options">
                    <button type="button" class="link-btn cancel">Delete</button> <!-- Palagyan backend code -->
                </div>
            </div>
        </form>
    </div>
</div>