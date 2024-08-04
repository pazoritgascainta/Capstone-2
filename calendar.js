document.addEventListener('DOMContentLoaded', function () {
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

    function renderCalendar() {
        calendar.innerHTML = ''; // Clear previous calendar content

        // Add days of the week headers
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        daysOfWeek.forEach(day => {
            const headerCell = document.createElement('div');
            headerCell.className = 'calendar-header-cell';
            headerCell.textContent = day;
            calendar.appendChild(headerCell);
        });

        monthYearSpan.textContent = `${currentYear} ${new Date(currentYear, currentMonth).toLocaleString('default', { month: 'long' })}`; // Display current month and year

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
            cell.dataset.date = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            cell.addEventListener('click', function () {
                currentPage = 1;
                fetchAppointments(cell.dataset.date, currentPage, limit);
                statusMessageSection.style.display = 'none'; // Hide the section
            });
            calendar.appendChild(cell);
        }
    }

    function fetchAppointments(date, page, limit) {
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
                    paginationEl.innerHTML = ''; // Clear pagination since there are no appointments
                    return;
                }
    
                let html = '<tr><th>ID</th><th>Amenity</th><th>Date</th><th>Time Slot</th><th>Name</th><th>Email</th><th>Purpose</th><th>Homeowner ID</th><th>Status</th><th>Action</th></tr>';
                data.appointments.forEach(appointment => {
                    html += `<tr>
                        <td>${appointment.id}</td>
                        <td>${appointment.amenity}</td>
                        <td>${appointment.date}</td>
                        <td>${appointment.time_start} - ${appointment.time_end}</td>
                        <td>${appointment.name}</td>
                        <td>${appointment.email}</td>
                        <td>${appointment.purpose}</td>
                        <td>${appointment.homeowner_id}</td>
                        <td>${appointment.status}</td>
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
        paginationEl.innerHTML = ''; // Clear previous pagination

        const totalPages = Math.ceil(totalCount / limit);

        if (totalPages <= 1) {
            return; // No need to show pagination if there's only one page
        }

        // Previous button
        const prevButton = document.createElement('button');
        prevButton.textContent = '<';
        prevButton.disabled = page === 1;
        prevButton.addEventListener('click', function () {
            if (page > 1) {
                currentPage--;
                fetchAppointments(selectedDateEl.textContent, currentPage, limit);
            }
        });
        paginationEl.appendChild(prevButton);

        // Page input for user to change the page
        const pageInputForm = document.createElement('form');
        pageInputForm.method = 'GET';
        pageInputForm.action = 'admin_approval.php';
        pageInputForm.style.display = 'inline';

        const pageInput = document.createElement('input');
        pageInput.type = 'number';
        pageInput.name = 'page';
        pageInput.value = page;
        pageInput.min = 1;
        pageInput.max = totalPages;
        pageInput.style.width = '50px';
        pageInput.addEventListener('change', function () {
            currentPage = pageInput.value;
            fetchAppointments(selectedDateEl.textContent, currentPage, limit);
        });

        pageInputForm.appendChild(pageInput);
        paginationEl.appendChild(pageInputForm);

        // "of" text
        const ofText = document.createElement('span');
        ofText.textContent = ` of `;
        paginationEl.appendChild(ofText);

        // Last page link
        const lastPageLink = document.createElement('a');
        lastPageLink.href = `?page=${totalPages}`;
        lastPageLink.textContent = totalPages;
        lastPageLink.className = page === totalPages ? 'active' : '';
        lastPageLink.addEventListener('click', function (e) {
            e.preventDefault();
            currentPage = totalPages;
            fetchAppointments(selectedDateEl.textContent, currentPage, limit);
        });
        paginationEl.appendChild(lastPageLink);

        // Next button
        const nextButton = document.createElement('button');
        nextButton.textContent = '>';
        nextButton.disabled = page === totalPages;
        nextButton.addEventListener('click', function () {
            if (page < totalPages) {
                currentPage++;
                fetchAppointments(selectedDateEl.textContent, currentPage, limit);
            }
        });
        paginationEl.appendChild(nextButton);
    }

    prevMonthButton.addEventListener('click', function () {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar();
    });

    nextMonthButton.addEventListener('click', function () {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    });

    renderCalendar();
});
