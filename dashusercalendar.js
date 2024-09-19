const calendar = document.getElementById('calendar');
const monthYearSpan = document.getElementById('month-year');
const prevMonthBtn = document.getElementById('prev-month');
const nextMonthBtn = document.getElementById('next-month');

let currentDate = new Date();

// Function to render the calendar
function renderCalendar() {
    calendar.innerHTML = ''; // Clear previous calendar content

    // Get the current month and year
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth();

    // Add days of the week headers
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    daysOfWeek.forEach(day => {
        const headerCell = document.createElement('div');
        headerCell.className = 'calendar-header-cell';
        headerCell.textContent = day;
        calendar.appendChild(headerCell);
    });

    // Display current month and year
    monthYearSpan.textContent = `${new Date(currentYear, currentMonth).toLocaleString('default', { month: 'long' })} ${currentYear}`;

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();

    // Render empty cells before the first day of the month
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'calendar-cell empty';
        calendar.appendChild(emptyCell);
    }

    // Render days of the month
    for (let day = 1; day <= lastDate; day++) {
        const cell = document.createElement('div');
        cell.className = 'calendar-cell';
        cell.textContent = day;
        cell.dataset.date = `${currentYear}-${currentMonth + 1}-${day}`;
        calendar.appendChild(cell);
    }
}

// Handle previous and next month navigation
prevMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

nextMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

// Render the current month's calendar on load
renderCalendar();
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