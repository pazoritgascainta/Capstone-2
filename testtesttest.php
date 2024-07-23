document.addEventListener('DOMContentLoaded', function() {
    const calendar = document.querySelector('.calendar');
    const monthElement = calendar.querySelector('.month');
    const datesElement = calendar.querySelector('.dates');
    const prevMonthButton = document.getElementById('prevMonth');
    const nextMonthButton = document.getElementById('nextMonth');
    const selectedDateElement = document.getElementById('selectedDate');
    const timetableContainer = document.getElementById('timetable-container'); // Assuming this element exists

    // Initialize bookedDates array with PHP data
    let bookedDates = <?php echo json_encode($booked_dates); ?>;
    let currentDate = new Date(); // Initialize current date

    // Function to generate calendar
    function generateCalendar(year, month) {
        datesElement.innerHTML = ''; // Clear previous calendar dates

        currentDate = new Date(year, month, 1); // Set current date to first day of the specified month

        // Display month and year in the header
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

            const formattedDate = new Date(year, month, day).toISOString().split('T')[0]; // Format date for comparison

            // Check if the date is booked and add appropriate class
            if (bookedDates.includes(formattedDate)) {
                dateElement.classList.add('booked-date');
            }

            // Add click event to show timetable for the clicked day
            dateElement.addEventListener('click', function() {
                // Update selected date display
                selectedDateElement.textContent = `Selected Date: ${formattedDate}`;
                selectedDateElement.classList.remove('hidden');

                // Show timetable if date is booked, clear otherwise
                if (bookedDates.includes(formattedDate)) {
                    showTimetable(formattedDate);
                } else {
                    timetableContainer.innerHTML = '';
                }
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

    // Function to show timetable (dummy implementation, you can customize as needed)
    function showTimetable(date) {
        timetableContainer.innerHTML = `<p>Timetable for ${date}</p>`;
    }
});
