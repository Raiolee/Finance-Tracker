<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="bankModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModal()">&times;</span>
        <h3 class="header">New Bank</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
        <input type="hidden" name="action" value="insert_bank">
            <div class="big-divider full">
                <label class="form-labels" for="bank-name">Name</label>
                <input class="var-input" type="text" name="bank-name" placeholder="Name" id="bank-name" required id>
                <label for="bank" class="form-labels">Bank</label>
                <input class="var-input" type="text" id="bank" name="bank" placeholder="Bank Name" required>
                <label class="form-labels" for="bank-amount">Amount</label>
                <input class="var-input" type="number" name="bank-amount" placeholder="0.00"  id="bank-amount" required>
                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
