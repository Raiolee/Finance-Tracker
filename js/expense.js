// Get elements for reimbursable radio buttons and file input
const reimbursableYes = document.getElementById('reimbursable-yes');
const reimbursableNo = document.getElementById('reimbursable-no');
const fileLabel = document.getElementById('file-label');
const fileInput = document.getElementById('file-input');

// Show file input when "Yes" is selected
if (reimbursableYes) {
    reimbursableYes.addEventListener('change', function () {
        fileLabel.style.display = 'block';
        fileInput.style.display = 'inline-block';
    });
}

// Hide file input when "No" is selected
if (reimbursableNo) {
    reimbursableNo.addEventListener('change', function () {
        fileLabel.style.display = 'none';
        fileInput.style.display = 'none';
    });
}