document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('timeslot-form').addEventListener('submit', handleFormSubmit);
});

function handleFormSubmit(event) {
    event.preventDefault();

    var formData = new FormData(event.target);

    fetch('manage_timeslots.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json(); // Parse JSON from the response
    })
    .then(data => {
        if (data.status === 'success') {
            alert(data.message); // Display success message
        } else {
            alert('An error occurred: ' + data.message); // Display error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while generating timeslots: ' + error.message);
    });
}
