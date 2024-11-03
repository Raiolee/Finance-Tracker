<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="expenseRowModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeExpenseRowModal()">&times;</span>
        <h3 class="header">Edit</h3>
        <hr class="bottom-line">
        <form class="form-container" id="expenseForm" method="post" action="">
            <div class="big-divider full">

                <!-- label and input for subject -->
                <label for="subject" class="form-labels row">Subject</label>
                <input type="text" class="var-input medium" id="subject-row" name="subject">

                <!-- label and input for category -->
                <label for="category" class="form-labels row medium">Category</label>
                <select class="var-input" id="category-row" name="category">
                    <option value="food">Food</option>
                    <option value="transport">Transport</option>
                    <option value="entertainment">Entertainment</option>
                    <option value="utilities">Utilities</option>
                    <option value="other">Other</option>
                </select>

                <!-- label and input for date -->
                <label for="expense-date" class="form-labels row">Date</label>
                <input type="date" class="var-input" id="expense-date-row" name="expense-date" >

                <!-- label and input for recurrence type -->
                <label for="recurrence_type" class="form-labels row">Frequency</label>
                <select class="var-input large pointer" name="recurrence_type" id="recurrence_type-row">
                    <option value="weekly">Once</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom</option>
                </select>

                <!-- Merchant -->
                <label for="Merchant" class="form-labels">Merchant</label>
                <input type="text" class="var-input" id="merchant-row" name="merchant">

                <!-- Bank -->
                <label for="bank" class="form-labels row">Bank</label>
                <select class="var-input large pointer" name="bank" id="bank-row">
                    <option value="CHANGE ME">Bank 1</option> <!-- Change the code to get the bank based on user_id -->
                </select>

                <label for="amount" class="form-labels">Amount*</label>
                <input type="number" class="var-input" id="amount-row" name="amount" required>
                <!-- Description -->
                <label for="description" class="form-labels">Description</label>
                <textarea class="var-input" name="description" id="description-row"></textarea>

                <!-- Reimbursable -->
                <label for="reimbursable" class="form-labels">Reimbursable</label>
                <div class="column-form start">
                    <input class="radio" type="radio" name="reimbursable" id="reimbursable-yes-row" value="yes">
                    <label class="form-labels center no-margin" for="reimbursable-yes">Yes</label>

                    <input class="radio" type="radio" name="reimbursable" id="reimbursable-no-row" value="no" checked>
                    <label class="form-labels center no-margin" for="reimbursable-no">No</label>
                </div>
                <div class="column-form start">
                    <!-- file input/ photo input -->
                    <label for="attachment" class="file-label" id="file-label-row">Attach Receipt</label>
                    <input class="file-input" type="file" name="attachment" accept="image/*" id="file-input">
                </div>

                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModalExpense()">Delete</button> <!-- Paedit na lang din ng delete button to work with the delete expense-->
                    <button type="Edit" class="save">Edit</button> <!-- Paedit na lang din ng save button to work with the edit expense-->
                </div>
            </div>
        </form>
    </div>
</div>