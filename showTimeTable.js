function showTimetable(date) {
    fetch(`fetch_available_times.php?date=${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const times = data.map(slot => `<li>${slot.start_time} - ${slot.end_time}</li>`).join('');
                timetableContainer.innerHTML = `<p>Available Times for ${date}:</p><ul>${times}</ul>`;
            } else {
                timetableContainer.innerHTML = `<p>No available times for ${date}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching available times:', error);
            timetableContainer.innerHTML = `<p>Error fetching available times.</p>`;
        });

    selectedDateElement.textContent = `Selected Date: ${date}`;
    selectedDateElement.classList.remove('hidden');
}
