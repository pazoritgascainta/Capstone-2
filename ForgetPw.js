const container = document.getElementById('container');
const forgetpwBtn = document.getElementById('forgetpw');
const exitBtn = document.getElementById('exitBtn');


exitBtn.addEventListener('click', () => {
    window.location.href = 'Homepage.php'; // Redirect to the login page
});
forgetpwBtn.addEventListener('click', () => {
    container.classList.add("active");
});