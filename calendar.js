const calendar = document.getElementById('calendar');
const monthYearSpan = document.getElementById('month-year');
const prevMonthButton = document.getElementById('prev-month');
const nextMonthButton = document.getElementById('next-month');
const appointmentsEl = document.getElementById('appointments-table');
const selectedDateEl = document.getElementById('selected-date');
const appointmentsTableContainer = document.getElementById('appointments-table-container');
const paginationEl = document.getElementById('pagination');
const statusMessageSection = document.getElementById('status-message-section');

let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let currentPage = 1;
const limit = 10;
let appointmentCounts = {}; // Global variable for appointment counts

function renderCalendar() {
    const today = new Date();
    calendar.innerHTML = ''; // Clear previous calendar content

    // Add days of the week headers
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    daysOfWeek.forEach(day => {
        const headerCell = document.createElement('div');
        headerCell.className = 'calendar-header-cell';
        headerCell.textContent = day;
        calendar.appendChild(headerCell);
    });

    // Update month and year display
    monthYearSpan.textContent = `${currentYear} ${new Date(currentYear, currentMonth).toLocaleString('default', { month: 'long' })}`;

    // Determine the first day and last date of the month
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();

    // Render empty cells before the first day of the month
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'calendar-cell empty';
        calendar.appendChild(emptyCell);
    }

    // Render days of the month with appointment counts and color coding
    for (let day = 1; day <= lastDate; day++) {
        const cell = document.createElement('div');
        cell.className = 'calendar-cell';
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        cell.textContent = day;
        cell.dataset.date = dateStr;

        const count = appointmentCounts[dateStr] || {
            Amenity_1: 0,
            Amenity_2: 0,
            Amenity_3: 0,
            Amenity_4: 0,
            Amenity_5: 0,
            Amenity_6: 0
        };

        const colorGrid = document.createElement('div');
        colorGrid.className = 'color-grid';

        if (count.Amenity_1 > 0) {
            const colorCell = document.createElement('div');
            colorCell.className = 'color-cell';
            colorCell.style.backgroundColor = 'blue';
            colorCell.textContent = count.Amenity_1;
            colorGrid.appendChild(colorCell);
        }
        if (count.Amenity_2 > 0) {
            const colorCell = document.createElement('div');
            colorCell.className = 'color-cell';
            colorCell.style.backgroundColor = 'green';
            colorCell.textContent = count.Amenity_2;
            colorGrid.appendChild(colorCell);
        }
        if (count.Amenity_3 > 0) {
            const colorCell = document.createElement('div');
            colorCell.className = 'color-cell';
            colorCell.style.backgroundColor = 'coral';
            colorCell.textContent = count.Amenity_3;
            colorGrid.appendChild(colorCell);
        }
        if (count.Amenity_4 > 0) {
            const colorCell = document.createElement('div');
            colorCell.className = 'color-cell';
            colorCell.style.backgroundColor = 'purple';
            colorCell.textContent = count.Amenity_4;
            colorGrid.appendChild(colorCell);
        }
        if (count.Amenity_5 > 0) {
            const colorCell = document.createElement('div');
            colorCell.className = 'color-cell';
            colorCell.style.backgroundColor = 'pink';
            colorCell.textContent = count.Amenity_5;
            colorGrid.appendChild(colorCell);
        }
        if (count.Amenity_6 > 0) {
            const colorCell = document.createElement('div');
            colorCell.className = 'color-cell';
            colorCell.style.backgroundColor = 'gray';
            colorCell.textContent = count.Amenity_6;
            colorGrid.appendChild(colorCell);
        }

        cell.appendChild(colorGrid);

        // Highlight today's date and past dates
        const cellDate = new Date(currentYear, currentMonth, day);
        if (cellDate < today.setHours(0, 0, 0, 0)) {
            cell.classList.add('past-day');
        } else if (cellDate.getTime() === today.setHours(0, 0, 0, 0)) {
            cell.classList.add('today');
        }

        cell.addEventListener('click', function () {
            currentPage = 1;
            fetchAppointments(cell.dataset.date, currentPage, limit);
            statusMessageSection.style.display = 'none';
        });

        calendar.appendChild(cell);
    }

    // Fill remaining empty cells for next month's days
    const remainingCells = (7 - (firstDay + lastDate) % 7) % 7;
    for (let i = 0; i < remainingCells; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'calendar-cell empty';
        calendar.appendChild(emptyCell);
    }
}

function fetchAppointmentCounts(year, month) {
    return fetch(`fetch_appointment_counts.php?year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            appointmentCounts = data; // Store counts globally
            renderCalendar(); // Render the calendar after fetching counts
        })
        .catch(error => {
            console.error('Error fetching appointment counts:', error);
        });
}

function fetchAppointments(date, page = 1, limit = 10) {
    fetch(`fetch_appointments.php?date=${date}&page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                appointmentsEl.innerHTML = '<tr><td colspan="10">Error fetching appointments</td></tr>';
                return;
            }

            if (data.appointments.length === 0) {
                appointmentsEl.innerHTML = '<tr><td colspan="10">No appointments available for the selected date</td></tr>';
                appointmentsTableContainer.style.display = 'block';
                selectedDateEl.textContent = date;
                paginationEl.innerHTML = '';
                return;
            }

            let html = '<tr><th>ID</th><th>Amenity</th><th>Date</th><th>Time Slot</th><th>Name</th><th>Email</th><th>Purpose</th><th>Homeowner ID</th><th>Status</th><th>Action</th></tr>';
            data.appointments.forEach(appointment => {
                html += `<tr>
                    <td>${appointment.id}</td>
                    <td>${appointment.amenity || 'N/A'}</td>
                    <td>${appointment.date}</td>
                    <td>${appointment.time_start || 'N/A'} - ${appointment.time_end || 'N/A'}</td>
                    <td>${appointment.name || 'N/A'}</td>
                    <td>${appointment.email || 'N/A'}</td>
                    <td>${appointment.purpose || 'N/A'}</td>
                    <td>${appointment.homeowner_id || 'N/A'}</td>
                    <td>${appointment.status || 'N/A'}</td>
                    <td class="centered-actions">
                        <form method="POST" action="admin_approval.php" style="display: inline;">
                            <input type="hidden" name="appointment_id" value="${appointment.id}">
                            <input type="hidden" name="new_status" value="Accepted">
                            <button type="submit">Accept</button>
                        </form>
                        <form method="POST" action="admin_approval.php" style="display: inline;">
                            <input type="hidden" name="appointment_id" value="${appointment.id}">
                            <input type="hidden" name="new_status" value="Rejected">
                            <button type="submit">Reject</button>
                        </form>
                    </td>
                </tr>`;
            });
            appointmentsEl.innerHTML = html;
            appointmentsTableContainer.style.display = 'block';
            selectedDateEl.textContent = date;

            renderPagination(data.total_count, page, limit);
        })
        .catch(error => {
            console.error('Error fetching appointments:', error);
            appointmentsEl.innerHTML = '<tr><td colspan="10">Error fetching appointments</td></tr>';
        });
}

function renderPagination(totalCount, page, limit) {
    const totalPages = Math.ceil(totalCount / limit);
    paginationEl.innerHTML = '';

    if (totalPages > 1) {
        if (page > 1) {
            paginationEl.innerHTML += `<form method="GET" action="" style="display: inline;">
                <input type="hidden" name="page" value="${page - 1}">
                <button type="submit">Previous</button>
            </form>`;
        }

        paginationEl.innerHTML += `<span>Page ${page} of ${totalPages}</span>`;

        if (page < totalPages) {
            paginationEl.innerHTML += `<form method="GET" action="" style="display: inline;">
                <input type="hidden" name="page" value="${page + 1}">
                <button type="submit">Next</button>
            </form>`;
        }
    }
}

prevMonthButton.addEventListener('click', function () {
    if (currentMonth === 0) {
        currentMonth = 11;
        currentYear--;
    } else {
        currentMonth--;
    }
    fetchAppointmentCounts(currentYear, currentMonth + 1);
});

nextMonthButton.addEventListener('click', function () {
    if (currentMonth === 11) {
        currentMonth = 0;
        currentYear++;
    } else {
        currentMonth++;
    }
    fetchAppointmentCounts(currentYear, currentMonth + 1);
});

// Initial load
fetchAppointmentCounts(currentYear, currentMonth + 1);
