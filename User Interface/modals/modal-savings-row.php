<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="savingsRowModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeSavingsRowModal()">&times;</span>
        <h3 class="header">Edit Bank</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <input type="hidden" name="action" value="update_action">
            <input type="hidden" name="bank_id" id="bank_id">
            <div class="big-divider full">
                <!-- Bank purpose -->
                <label class="form-labels" for="bank-name">Name</label>
                <input class="var-input" type="text" name="bank-name" placeholder="Name" id="bankNameRow" required id>
                <!-- Bank Name -->
                <label for="bank" class="form-labels">Bank</label>
                <input class="var-input" type="text" name="bankRow" placeholder="Bank Name" id="bankRow" required>
                <!--Bank Amount -->
                <label class="form-labels" for="bank-amount">Amount</label>
                <input class="var-input" type="number" name="bank-amount" placeholder="0.00" id="bankAmountRow" required>
                <div class="btn-options">
                    <button type="submit" class="link-btn cancel" id="Delete_Bank" name="action" value="dbank_action">Delete</button> <!-- PALAGYAN NA LANG NG BACKEND CODE -->
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>