/**
 * Form Validation - Client-side using jQuery
 * 
 * Validates forms before submission.
 * Server-side validation is the primary defense;
 * client-side is for better UX (instant feedback).
 */
$(document).ready(function () {

    // Helper: show error message
    function showError(field, message) {
        $(`#${field}-error`).text(message);
        $(`#${field}`).addClass('input-error');
    }

    // Helper: clear error
    function clearError(field) {
        $(`#${field}-error`).text('');
        $(`#${field}`).removeClass('input-error');
    }

    // Clear errors on input
    $('input, textarea').on('input', function () {
        clearError(this.id);
    });

    // Register form validation
    $('#register-form').on('submit', function (e) {
        let valid = true;
        const username = $('#username').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirm = $('#confirm_password').val();

        if (username.length < 3) {
            showError('username', 'Username must be at least 3 characters');
            valid = false;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Please enter a valid email');
            valid = false;
        }
        if (password.length < 6) {
            showError('password', 'Password must be at least 6 characters');
            valid = false;
        }
        if (password !== confirm) {
            showError('confirm', 'Passwords do not match');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    // Login form validation
    $('#login-form').on('submit', function (e) {
        let valid = true;
        if (!$('#email').val().trim()) {
            showError('email', 'Email is required');
            valid = false;
        }
        if (!$('#password').val()) {
            showError('password', 'Password is required');
            valid = false;
        }
        if (!valid) e.preventDefault();
    });

    // Upload form validation
    $('#upload-form').on('submit', function (e) {
        let valid = true;
        if (!$('#title').val().trim()) {
            showError('title', 'Title is required');
            valid = false;
        }
        if (!$('#ingredients').val().trim()) {
            showError('ingredients', 'Ingredients are required');
            valid = false;
        }
        if (!$('#instructions').val().trim()) {
            showError('instructions', 'Instructions are required');
            valid = false;
        }

        // Validate image file type
        const fileInput = $('#image')[0];
        if (fileInput && fileInput.files.length > 0) {
            const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowed.includes(fileInput.files[0].type)) {
                showError('image', 'Only JPEG, PNG, GIF, WebP allowed');
                valid = false;
            }
        }

        if (!valid) e.preventDefault();
    });
});
