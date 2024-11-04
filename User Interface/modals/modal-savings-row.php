<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="savingsRowModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeSavingsRowModal()">&times;</span>
        <h3 class="header">New Bank</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <input type="hidden" name="action" value="insert_bank">
            <div class="big-divider full">
                <!-- Bank purpose -->
                <label class="form-labels" for="bank-name">Name</label>
                <input class="var-input" type="text" name="bank-name" placeholder="Name" id="bankName" required id>
                <!-- Bank Name -->
                <label for="bank" class="form-labels">Bank</label>
                <input class="var-input" type="text" id="bank" name="bank" placeholder="Bank Name" itemid="bank" required>
                <!--Bank Amount -->
                <label class="form-labels" for="bank-amount">Amount</label>
                <input class="var-input" type="number" name="bank-amount" placeholder="0.00" id="bankAmount" required>
                <div class="btn-options">
                    <button type="button" class="link-btn cancel">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>