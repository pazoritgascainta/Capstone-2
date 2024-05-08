document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('container');
    const loginBtn = document.getElementById('loginBtn');
    const exitBtn = document.getElementById('exitBtn');

    loginBtn.addEventListener('click', (event) => {
        event.preventDefault(); // Prevent default anchor behavior
        container.style.display = "block"; // Show the container
        exitBtn.style.display = "block"; // Show the exit button
    });

    exitBtn.addEventListener('click', () => {
        container.style.display = "none"; // Hide the container
        exitBtn.style.display = "none"; // Hide the exit button
    });
});
