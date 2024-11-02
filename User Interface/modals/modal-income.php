<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="incomeModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModalIncome()">&times;</span>
        <h3 class="header">Add an Income</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <div class="big-divider full">
                <label class="form-labels" name="date" for="date"> Date*</label>
                <input class="var-input" type="date" name="date" required>

                <label class="form-labels" for="source">Source of Income*</label>
                <input class="var-input" type="text" name="source" placeholder="Source of Income" required>

                <label class="form-labels" for="income-amount">Amount</label>
                <input class="var-input" type="number" id="income-amount" name="income-amount" placeholder="0.00" required>

                <label class="form-labels" for="goal-category">Category*</label>
                <select class="var-input" name="goal-category" id="goal-category" required>
                    <option value="Once">Once</option> 
                    <option value="Daily">Daily</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Monthly">Monthly</option>
                </select>

                
                <label class="form-labels" for="goal-description">Bank Name*</label>
                <select class="var-input" name="goal-category" id="goal-category" required>
                    <option value="CHANGE ME">Option 1</option> <!-- Palagay na lang sa value yung backend code to get the banks -->
                </select>

                <label class="form-labels" for="income-description">Description</label>
                <textarea class="var-input" name="income-description" id="goal-description" placeholder="Description"></textarea>

                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModalIncome()">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>