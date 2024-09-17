document.addEventListener('DOMContentLoaded', function() {
    const calendar = document.getElementById('calendar');
    const monthYearSpan = document.getElementById('month-year');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');

document.addEventListener("DOMContentLoaded", function() {
    const today = new Date();

    // Render calendar logic here
    function renderCalendar(year, month) {
        const calendar = document.getElementById('calendar');
        calendar.innerHTML = ''; // Clear previous calendar

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Get the number of days in the previous month
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        // Add empty cells for the days of the previous month
        for (let i = firstDay; i > 0; i--) {
            const emptyCell = document.createElement('div');
            emptyCell.classList.add('calendar-cell', 'empty');
            emptyCell.innerText = daysInPrevMonth - i + 1; // Optionally show previous month's days
            calendar.appendChild(emptyCell);
        }

        // Fill in days of the current month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dayCell = document.createElement('div');
            dayCell.classList.add('calendar-cell');

            // Compare the current day with today's date
            if (date < today.setHours(0, 0, 0, 0)) {
                // Past dates (before today)
                dayCell.classList.add('past-day');
            } else if (date.getTime() === today.setHours(0, 0, 0, 0)) {
                // Today's date
                dayCell.classList.add('today');
            }

            dayCell.innerText = day;
            calendar.appendChild(dayCell);
        }

        // Fill remaining empty cells for next month's days to keep grid aligned
        const remainingCells = (7 - (firstDay + daysInMonth) % 7) % 7;
        for (let i = 1; i <= remainingCells; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.classList.add('calendar-cell', 'empty');
            emptyCell.innerText = i; // Optionally show next month's days
            calendar.appendChild(emptyCell);
        }
    }

    // Initial render
    const currentYear = today.getFullYear();
    const currentMonth = today.getMonth();
    renderCalendar(currentYear, currentMonth);

    // Navigation buttons
    const prevButton = document.getElementById('prev-month');
    const nextButton = document.getElementById('next-month');
    let displayedYear = currentYear;
    let displayedMonth = currentMonth;

    prevButton.addEventListener('click', function() {
        displayedMonth--;
        if (displayedMonth < 0) {
            displayedMonth = 11;
            displayedYear--;
        }
        renderCalendar(displayedYear, displayedMonth);
    });

    nextButton.addEventListener('click', function() {
        displayedMonth++;
        if (displayedMonth > 11) {
            displayedMonth = 0;
            displayedYear++;
        }
        renderCalendar(displayedYear, displayedMonth);
    });
});
   // Event listener for calendar navigation buttons
   prevMonthButton.addEventListener('click', function() {
    currentMonth -= 1;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear -= 1;
    }
    renderCalendar();
});

nextMonthButton.addEventListener('click', function() {
    currentMonth += 1;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear += 1;
    }
    renderCalendar();
});
});

