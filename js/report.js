document.getElementById('report-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    
    fetch('report.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Expecting a simple text response for now
    .then(result => {
        console.log(result);  // Check the result in the console
        alert(result);  // This should now say "Form submitted!"
    })
    .catch(error => console.error('Error:', error));
});
