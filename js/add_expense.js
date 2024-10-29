document.getElementById("reimbursable-yes").addEventListener("change", function() {
    document.getElementById("file-label").style.display = "flex";
    document.getElementById("file-input").style.display = "none";
});

document.getElementById("reimbursable-no").addEventListener("change", function() {
    document.getElementById("file-label").style.display = "none";
    document.getElementById("file-input").style.display = "none";
});

document.getElementById("file-label").addEventListener("click", function() {
    document.getElementById("file-input").click();
});