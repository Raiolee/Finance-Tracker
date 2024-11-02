<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="goalModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModalGoal()">&times;</span>
        <h3 class="header">Add to Goals</h3>
        <hr class="bottom-line">
        <form class="form-container" id="SavingForm" method="post" action="">
            <div class="big-divider full">
                <label class="form-labels" for="name">Name*</label>
                <input class="var-input" type="text" name="name" placeholder="Name" required> <!-- Palagay na lang sa value yung backend code to get the specific bank -->
                <label class="form-labels" name="bank-balance" for="start-date">Start Date*</label>
                <input class="var-input" type="date" name="start-date" required>
                <label class="form-labels" for="goal-category">Category*</label>
                <select class="var-input" name="goal-category" id="goal-category" required>
                    <option value="CHANGE ME">Option 1</option> <!-- Palagay na lang sa value yung backend code to get the specific goal -->
                </select>
                <label class="form-labels" for="target-amount">Target Amount*</label> <!-- Put error handling para di sosobra yung amount sa remaining balance -->
                <input class="var-input" type="number" id="bank-amount" name="target-amount" placeholder="0.00" required>
                <label class="form-labels" for="goal-description">Description*</label>
                <textarea class="var-input" name="goal-description" id="goal-description" placeholder="Description" required></textarea>
                
                <div class="btn-options">
                    <button type="button" class="link-btn cancel" onclick="closeModalGoal()">Cancel</button>
                    <button type="submit" class="save">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
