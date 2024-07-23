document.addEventListener('DOMContentLoaded', function() {
    const calendar = document.querySelector('.calendar');
    const monthElement = calendar.querySelector('.month');
    const datesElement = calendar.querySelector('.dates');
    const prevMonthButton = document.getElementById('prevMonth');
    const nextMonthButton = document.getElementById('nextMonth');
    const selectedDateElement = document.getElementById('selectedDate');
    const timetableContainer = document.getElementById('timetable-container');

    let currentDate = new Date(); // Initialize current date

    // Function to generate calendar
    function generateCalendar(year, month) {
        datesElement.innerHTML = ''; // Clear previous calendar dates

        currentDate = new Date(year, month, 1); // Set current date to first day of the specified month

        monthElement.textContent = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });

        const daysInMonth = new Date(year, month + 1, 0).getDate(); // Number of days in the current month
        const firstDayIndex = new Date(year, month, 1).getDay(); // Index of the first day of the month (0-6)

        // Create empty placeholders for days before the first day of the month
        for (let i = 0; i < firstDayIndex; i++) {
            const dateElement = document.createElement('div');
            dateElement.classList.add('date', 'empty');
            datesElement.appendChild(dateElement);
        }

        // Create date elements for each day in the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateElement = document.createElement('div');
            dateElement.classList.add('date');
            dateElement.textContent = day;

            const formattedDate = new Date(year, month, day).toISOString().split('T')[0];

            dateElement.addEventListener('click', function() {
                showTimetable(formattedDate);
                selectedDateElement.textContent = `Selected Date: ${formattedDate}`;
                selectedDateElement.classList.remove('hidden');
            });

            datesElement.appendChild(dateElement);
        }
    }

    // Event listeners for month navigation
    prevMonthButton.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });

    nextMonthButton.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });

    // Initial calendar generation for the current month
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());

    function showTimetable(date) {
        fetch(`fetch_available_times.php?date=${encodeURIComponent(date)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (Array.isArray(data) && data.length > 0) {
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
    }
});
