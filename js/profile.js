// profile.js

const fileInput = document.getElementById('file-input');
const fileNameDisplay = document.getElementById('file-name');

if (fileInput && fileNameDisplay) {
    fileInput.addEventListener('change', function () {
        const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        fileNameDisplay.textContent = fileName;
    });
}
