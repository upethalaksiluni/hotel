// Additional JavaScript functionality if needed
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Name validation
        if (nameInput.value.trim().length < 2) {
            showError(nameInput, 'Name must be at least 2 characters long');
            isValid = false;
        }

        // Email validation
        if (!isValidEmail(emailInput.value)) {
            showError(emailInput, 'Please enter a valid email address');
            isValid = false;
        }

        // Password validation
        if (newPasswordInput.value || confirmPasswordInput.value) {
            if (newPasswordInput.value.length < 6) {
                showError(newPasswordInput, 'Password must be at least 6 characters long');
                isValid = false;
            }
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                showError(confirmPasswordInput, 'Passwords do not match');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm mt-1';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 3000);
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
});