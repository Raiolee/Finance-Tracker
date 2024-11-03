<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="expenseModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModalExpense()">&times;</span>
        <h3 class="header">Add an Expense</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <div class="big-divider full">
                <label for="subject" class="form-labels row">Subject</label>
                <input type="text" class="var-input medium" id="subject" name="subject">

                <label for="category" class="form-labels row medium">Category</label>
                <select class="var-input" id="category" name="category">
                    <option value="food">Food</option>
                    <option value="transport">Transport</option>
                    <option value="entertainment">Entertainment</option>
                    <option value="utilities">Utilities</option>
                    <option value="other">Other</option>
                </select>

                <label for="expense-date" class="form-labels row">Date</label>
                <input type="date" class="var-input" id="expense-date" name="expense-date">

                <label for="recurrence_type" class="form-labels row">Frequency</label>
                <select class="var-input large pointer" name="recurrence_type" id="recurrence_type">
                    <option value="weekly">Once</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom</option>
                </select>

                <label for="Merchant" class="form-labels">Merchant</label>
                <input type="text" class="var-input" id="Merchant" name="merchant">

                <!-- Bank -->
                <label for="bank" class="form-labels row">Bank</label>
                <select class="var-input large pointer" name="bank" id="bank">
                    <option value="CHANGE ME">Bank 1</option> <!-- Change the code to get the bank based on user_id -->
                </select>

                <label for="amount" class="form-labels">Amount*</label>
                <input type="number" class="var-input" id="amount" name="amount" required>
                <!-- Description -->
                <label for="description" class="form-labels">Description</label>
                <textarea class="var-input" name="description" id="description"></textarea>

                <!-- Reimbursable -->
                <label for="reimbursable" class="form-labels">Reimbursable</label>
                <div class="column-form start">
                    <input class="radio" type="radio" name="reimbursable" id="reimbursable-yes" value="yes">
                    <label class="form-labels center no-margin" for="reimbursable-yes">Yes</label>

                    <input class="radio" type="radio" name="reimbursable" id="reimbursable-no" value="no" checked>
                    <label class="form-labels center no-margin" for="reimbursable-no">No</label>
                </div>
                <div class="column-form start">
                    <!-- file input/ photo input -->
                    <label for="attachment" class="file-label" id="file-label">Attach Receipt</label>
                    <input class="file-input" type="file" name="attachment" accept="image/*" id="file-input">
                </div>


                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModalExpense()">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>