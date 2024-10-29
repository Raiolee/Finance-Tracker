document.getElementById("reimbursable-yes").addEventListener("change", function() {
    document.getElementById("file-label").style.display = "block";
    document.getElementById("file-input").style.display = "block";
});

document.getElementById("reimbursable-no").addEventListener("change", function() {
    document.getElementById("file-label").style.display = "none";
    document.getElementById("file-input").style.display = "none";
});