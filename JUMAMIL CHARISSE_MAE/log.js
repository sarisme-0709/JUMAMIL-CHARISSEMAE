document.getElementById('loginForm').addEventListener('submit', function(event) {
    const form = event.target;
    const useServer = form.dataset && form.dataset.server === '1';

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();

    const defaultEmail = "admin123@email.com";
    const defaultPassword = "admin123";

    if (!email || !password) {
        if (useServer) event.preventDefault();
        alert('Please fill in both fields.');
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        if (useServer) event.preventDefault();
        alert('Please enter a valid email address.');
        return;
    }

    if (password.length < 6) {
        if (useServer) event.preventDefault();
        alert('Password must be at least 6 characters long.');
        return;
    }

    if (useServer) {
        return;
    }

    event.preventDefault();
    if (email === defaultEmail && password === defaultPassword) {
        alert('Login successful!');
        window.location.href = 'Index.php'; 
    } else {
        alert('Invalid email or password.');
    }
});
