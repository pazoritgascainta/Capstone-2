// userservice.js

document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll(".button-group .btn");
    const serviceTypeInput = document.getElementById('type');

    buttons.forEach(button => {
        button.addEventListener("click", function() {
            // Set the hidden input value to the clicked button's value
            serviceTypeInput.value = this.value;
            // Remove active class from other buttons
            buttons.forEach(btn => btn.classList.remove("active"));
            // Add active class to the clicked button
            this.classList.add("active");
        });
    });

    // Optionally set the default value for service type on page load
    serviceTypeInput.value = buttons[0].value; // Set to the first button's value (Maintenance)
});
