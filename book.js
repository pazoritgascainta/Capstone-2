document.addEventListener('DOMContentLoaded', function() {
    const popupButton = document.getElementById('popupButton');
    const overlay = document.querySelector('.overlay');
    const formContainer = document.querySelector('.form-container');
    const exitButton = document.getElementById('exitButton');

    // Handle form pop-up and submission
    popupButton.addEventListener('click', function() {
        formContainer.style.display = 'block';
        document.body.style.overflow = 'hidden';
        overlay.style.display = 'block';
    });

    overlay.addEventListener('click', function() {
        if (formContainer.style.display === 'block') {
            formContainer.style.display = 'none';
            document.body.style.overflow = 'auto';
            overlay.style.display = 'none';
        }
    });

    formContainer.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    exitButton.addEventListener('click', function() {
        formContainer.style.display = 'none';
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    // Function to open appointment form
    function openAppointmentForm(date) {
        // Example: Pre-fill form fields with selected date
        const dateInput = document.getElementById('appointmentDate');
        dateInput.value = date;

        // Show form and overlay
        formContainer.style.display = 'block';
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    // Example form submission handling (replace with actual form submission logic)
    const appointmentForm = document.getElementById('appointmentForm');
    appointmentForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Example: Send appointment data to backend
        const formData = new FormData(appointmentForm);
        const appointmentData = {};
        formData.forEach((value, key) => {
            appointmentData[key] = value;
        });

        // Example: Process appointment data (replace with actual AJAX call)
        console.log('Appointment Data:', appointmentData);

        // Close form and overlay
        formContainer.style.display = 'none';
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Example: Update bookedDates array after successful booking
        bookedDates.push(appointmentData.date); // Assuming 'date' is the key for date in appointmentData
    });
});

