
document.addEventListener('DOMContentLoaded', function() {
    const timeslotForm = document.getElementById('timeslot-form');

    function handleFormSubmit(event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(timeslotForm);

        fetch('book_appointment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Expect JSON response
        .then(data => {
            if (data.status === 'success') {
                alert(data.message); // Show success message as an alert
                timeslotForm.reset(); // Optionally reset the form
            } else {
                alert('Error: ' + data.message); // Show error message as an alert
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        });
    }

    timeslotForm.addEventListener('submit', handleFormSubmit);
});
