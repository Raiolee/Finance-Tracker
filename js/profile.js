// profile.js

const fileInput = document.getElementById('file-input');
const fileNameDisplay = document.getElementById('file-name');

if (fileInput && fileNameDisplay) {
    fileInput.addEventListener('change', function () {
        const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        fileNameDisplay.textContent = fileName;
    });
}

function validateImage(input) {
    const file = input.files[0];
    if (file) {
        const img = new Image();
        img.onload = function() {
            if (this.width !== this.height) {
                alert('Please upload a square image.');
                input.value = ''; // Clear the input if not square
            }
        };
        img.src = URL.createObjectURL(file);
    }
}