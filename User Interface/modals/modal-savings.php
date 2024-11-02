<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="bankModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModal()">&times;</span>
        <h3 class="header">New Bank</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <div class="big-divider full">
                <label class="form-labels" for="Bank">Name</label>
                <input class="var-input" type="text" name="bank-name" placeholder="Name" required>
                <label for="Bank" class="form-labels">Bank</label>
                <input class="var-input" type="text" id="bank" name="bank" placeholder="Bank Name" required>
                <label class="form-labels" for="amount">Amount</label>
                <input class="var-input" type="number" id="bank-amount" placeholder="0.00" required>
                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
