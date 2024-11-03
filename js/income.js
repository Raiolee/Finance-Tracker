var editDeleteModal = document.getElementById("editDeleteModal");
var editDeleteClose = editDeleteModal.getElementsByClassName("close")[0];

document.querySelectorAll('.btn-outline-light[data-id]').forEach(function(button) {
    button.addEventListener('click', function() {
        var id = this.getAttribute('data-id');
        var source = this.getAttribute('data-source');
        var total = this.getAttribute('data-total');
        var category = this.getAttribute('data-category');
        var bank = this.getAttribute('data-bank');

        document.getElementById('incomeId').value = id;
        document.getElementById('incomeSource').value = source;
        document.getElementById('incomeTotal').value = total;
        document.getElementById('incomeCategory').value = category;
        document.getElementById('incomeBank').value = bank;

        editDeleteModal.style.display = "block";
    });
});

editDeleteClose.onclick = function() {
    editDeleteModal.style.display = "none";
};

window.onclick = function(event) {
    if (event.target == editDeleteModal) {
        editDeleteModal.style.display = "none";
    }
};