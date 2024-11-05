<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="goalRowModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeGoalRowModal()">&times;</span>
        <h3 class="header">Add to Savings</h3>
        <hr class="bottom-line">
        <form class="form-container" id="goalRowForm" method="POST" action="">
            <div class="big-divider full">
                <label class="form-labels" for="goalNameRow">Name*</label>
                <input class="var-input" type="text" name="goalNameRow" placeholder="Name" id="goalNameRow" readonly> <!-- Palagay na lang sa value yung backend code to get the specific bank -->
                
                <label class="form-labels" name="bank-balance" for="goalDateRow">Date*</label>
                <input class="var-input" type="date" name="goalDateRow" id="goalDatetRow" required>
                
                <label class="form-labels" for="goalCategoryRow">Frequency*</label>
                <select class="var-input" name="goalCategoryRow" id="goalCategoryRow" required>
                    <option value="Once">Once</option>
                    <option value="Daily">Daily</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Monthly">Monthly</option>
                </select>
                
                <label class="form-labels" for="goal-bank">Bank Name*</label>
                <select class="var-input" name="goalBankRow" id="goalBankRow" required>
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

                <label class="form-labels" for="goalAmountRow">Amount*</label> <!-- Put error handling para di sosobra yung amount sa remaining balance -->
                <input class="var-input" type="number" id="goalAmountRow" name="goalAmountRow" placeholder="0.00" required>
                
                
                <div class="btn-options">
                    <button type="button" class="link-btn cancel" name="goalsdeleteBTN">Delete</button>
                    <button type="submit" class="save" name="submitgoalform">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>
