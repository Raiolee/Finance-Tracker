<link rel="stylesheet" href="../Styles/modal-styles.scss">
<!-- Modal Structure -->
<div id="goalRowModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeGoalRowModal()">&times;</span>
        <h3 class="header">Add to Goals</h3>
        <hr class="bottom-line">
        <form class="form-container" id="goalRowForm" method="POST" action="">
            <div class="big-divider full">
                <label class="form-labels" for="goalNameRow">Name*</label>
                <input class="var-input" type="text" name="goalNameRow" placeholder="Name" id="goalNameRow" required> <!-- Palagay na lang sa value yung backend code to get the specific bank -->
                
                <label class="form-labels" name="bank-balance" for="goalStartRow">Start Date*</label>
                <input class="var-input" type="date" name="goalStartRow" id="goalStartRow" required>
                
                <label class="form-labels" for="goalCategoryRow">Category*</label>
                <select class="var-input" name="goalCategoryRow" id="goalCategoryRow" required>
                    <option value="Travels">Travels</option>
                    <option value="Miscellaneous">Miscellaneous</option>
                    <option value="Others">Others</option>
                </select>
                
                <label class="form-labels" for="goalAmountRow">Target Amount*</label> <!-- Put error handling para di sosobra yung amount sa remaining balance -->
                <input class="var-input" type="number" id="goalAmountRow" name="goalAmountRow" placeholder="0.00" required>
                
                <label class="form-labels" for="goalDescriptionRow">Description*</label>
                <textarea class="var-input" name="goalDescriptionRow" id="goalDescriptionRow" placeholder="Description" required></textarea>
                
                <div class="btn-options">
                    <button type="button" class="link-btn cancel">Delete</button>
                    <button type="submit" class="save" name="submit-form">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
