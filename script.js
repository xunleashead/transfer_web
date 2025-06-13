// script.js

document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('input');

    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.style.borderColor = '#28a745';
        });

        input.addEventListener('blur', () => {
            input.style.borderColor = '#ccc';
        });
    });
});
