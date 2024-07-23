document.addEventListener('DOMContentLoaded', function() {
    const calendar = document.getElementById('calendar');
    const monthYearSpan = document.getElementById('month-year');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');
    const timeslotContainer = document.getElementById('timeslot-container'); // Container for checkboxes
    const amenitySelect = document.getElementById('amenity');
    const selectedDateInput = document.getElementById('selected-date');
    const noTimeslotsMessage = document.getElementById('no-timeslots');
    const collapsibles = document.querySelectorAll('.collapsible');

    // Modal elements
    const modal = document.getElementById('timeslot-modal');
    const backdrop = document.querySelector('.modal-backdrop'); // Modal backdrop
    const closeModal = document.querySelector('.modal .close');
    const timeslotForm = document.getElementById('timeslot-form');
    const timeslotErrorMessage = document.getElementById('timeslot-error-message');

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDate = '';

    function fetchTimeslots() {
        const amenityId = amenitySelect.value;
        const selectedDate = selectedDateInput.value;

        if (!amenityId || !selectedDate) return;

        fetch(`fetch_timeslots.php?date=${encodeURIComponent(selectedDate)}&amenity_id=${encodeURIComponent(amenityId)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                handleTimeslots(data);
            })
            .catch(error => {
                console.error('Error fetching timeslots:', error);
            });
    }

    function handleTimeslots(timeslots) {
        if (!timeslotContainer || !noTimeslotsMessage) {
            console.error('Timeslot container or noTimeslotsMessage element not found');
            return;
        }

        timeslotContainer.innerHTML = ''; // Clear previous checkboxes
        noTimeslotsMessage.style.display = 'none'; // Hide "no timeslots" message

        if (timeslots.length > 0) {
            timeslots.forEach(timeslot => {
                const div = document.createElement('div');

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'timeslot_ids[]';
                checkbox.value = timeslot.id;
                checkbox.id = `timeslot_${timeslot.id}`;

                const label = document.createElement('label');
                label.htmlFor = checkbox.id;
                label.textContent = `${timeslot.time_start} - ${timeslot.time_end}`;

                div.appendChild(checkbox);
                div.appendChild(label);
                timeslotContainer.appendChild(div);
            });
        } else {
            noTimeslotsMessage.style.display = 'block'; // Show "no timeslots" message
        }
    }

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
            cell.dataset.date = `${currentYear}-${currentMonth + 1}-${day}`;
            calendar.appendChild(cell);
        }
    }

    function handleDateClick(event) {
        selectedDate = event.target.dataset.date;
        if (selectedDateInput) {
            selectedDateInput.value = selectedDate; // Set hidden input value
        }

        // Check if amenity is selected before showing the modal
        if (amenitySelect.value) {
            fetchTimeslots(); // Fetch timeslots when a date is clicked
            showModal(); // Show the modal
        } else {
            alert('Please select an amenity first.'); // Alert user to select an amenity
        }
    }

    function validateTimeslotSelection() {
        var checkboxes = document.querySelectorAll('#timeslot-container input[type="checkbox"]');
        var atLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

        if (!atLeastOneChecked) {
            timeslotErrorMessage.style.display = 'block'; // Show error message
            return false; // Prevent form submission
        } else {
            timeslotErrorMessage.style.display = 'none'; // Hide error message if valid
        }
        return true; // Allow form submission
    }

    function showModal() {
        modal.classList.add('show');
        backdrop.style.display = 'block'; // Show the backdrop
    }

    function hideModal() {
        modal.classList.remove('show');
        backdrop.style.display = 'none'; // Hide the backdrop
    }

    // Close modal
    closeModal.onclick = hideModal;

    window.onclick = function(event) {
        if (event.target === backdrop) {
            hideModal();
        }
    }

    // Attach event listener to amenity select element
    amenitySelect.addEventListener('change', fetchTimeslots);

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

    // Ensure to add the event listener for date clicks
    calendar.addEventListener('click', function(event) {
        if (event.target.classList.contains('calendar-cell')) {
            handleDateClick(event);
        }
    });

    // Initialize collapsible sections
    collapsibles.forEach(collapsible => {
        collapsible.addEventListener('click', function() {
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    });

    // Attach validation to the form submit
    timeslotForm.onsubmit = validateTimeslotSelection;

    renderCalendar(); // Initial render of the calendar
});