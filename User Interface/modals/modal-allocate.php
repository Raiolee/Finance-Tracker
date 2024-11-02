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
                <input class="var-input" type="text" name="bank" value="CHANGE ME" readonly> <!-- Palagay na lang sa value yung backend code to get the specific bank -->
                <label class="form-labels" name="bank-balance" for="Bank">Remaining Balance</label>
                <input class="var-input" type="number" name="bank-balance" value="CHANGE ME" readonly> <!-- Palagay na lang sa value yung backend code to get the specific bank balance -->
                <label class="form-labels" for="goal-allocate">Goal</label>
                <select class="var-input" name="goal-allocate" id="goal-allocate">
                    <option value="CHANGE ME">Option 1</option> <!-- Palagay na lang sa value yung backend code to get the specific goal -->
                </select>
                <label class="form-labels" for="allocate-amount">Amount</label> <!-- Put error handling para di sosobra yung amount sa remaining balance -->
                <input class="var-input" type="number" id="bank-amount" name="allocate-amount" placeholder="0.00" required>
                <label class="form-labels" for="allocate-date">Date</label>
                <input class="var-input" type="date" id="allocate-date" name="allocate-date" required>
                
                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModalAllocate()">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
