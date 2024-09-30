document.addEventListener('DOMContentLoaded', function() {
    const calendar = document.getElementById('calendar');
    const monthYearSpan = document.getElementById('month-year');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');
    const timeslotContainer = document.getElementById('timeslot-container');
    const amenitySelect = document.getElementById('amenity');
    const selectedDateInput = document.getElementById('selected-date');
    const noTimeslotsMessage = document.getElementById('no-timeslots');
    const collapsibles = document.querySelectorAll('.collapsible');

    // Modal elements
    const modal = document.getElementById('timeslot-modal');
    const backdrop = document.querySelector('.modal-backdrop');
    const closeModal = document.querySelector('.modal .close');
    const timeslotForm = document.getElementById('timeslot-form');
    const timeslotErrorMessage = document.getElementById('timeslot-error-message');

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDate = '';

    // Function to fetch timeslots based on selected date and amenity
    function fetchTimeslots() {
        const amenityId = amenitySelect.value;
        const selectedDate = selectedDateInput.value;

        if (!amenityId || !selectedDate) {
            console.log('No amenity or date selected.');
            return;
        }

        console.log(`Fetching timeslots for Amenity ID: ${amenityId}, Date: ${selectedDate}`);

        fetch(`fetch_timeslots.php?date=${encodeURIComponent(selectedDate)}&amenity_id=${encodeURIComponent(amenityId)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Fetched timeslots:', data);
                handleTimeslots(data);
            })
            .catch(error => {
                console.error('Error fetching timeslots:', error);
            });
    }

    // Function to handle and display fetched timeslots
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

    // Function to render the calendar
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
            cell.dataset.date = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
            calendar.appendChild(cell);

            // Color the today's date
            if (cell.dataset.date === `${new Date().getFullYear()}-${(new Date().getMonth() + 1).toString().padStart(2, '0')}-${new Date().getDate().toString().padStart(2, '0')}`) {
                cell.classList.add('today');
            }
        }
    }

    // Function to handle date clicks
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

    // Function to validate timeslot selection
    function validateTimeslotSelection() {
        const checkboxes = document.querySelectorAll('#timeslot-container input[type="checkbox"]');
        const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

        if (!isChecked) {
            timeslotErrorMessage.style.display = 'block'; // Show error message
            return false; // Prevent form submission
        }

        timeslotErrorMessage.style.display = 'none'; // Hide error message if valid
        return true; // Allow form submission
    }

    // Function to show the modal
    function showModal() {
        modal.classList.add('show');
        backdrop.style.display = 'block'; // Show the backdrop
    }

    // Function to hide the modal
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
    };

    // Attach event listener to amenity select element
    amenitySelect.addEventListener('change', function() {
        console.log('Amenity selected:', this.value);
        document.getElementById('hidden-amenity-id').value = this.value; // Update hidden input
        fetchTimeslots(); // Fetch timeslots when an amenity is selected
    });

    // Event listener for calendar navigation buttons
    prevMonthButton.addEventListener('click', function() {
        currentMonth -= 1;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear -= 1;
        }
        renderCalendar();
        colorToday(); // Call the function again when the month changes
    });

    nextMonthButton.addEventListener('click', function() {
        currentMonth += 1;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear += 1;
        }
        renderCalendar();
        colorToday(); // Call the function again when the month changes
    });

    // Event listener for clicks on the calendar
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

function colorToday() {
    const todayString = `${new Date().getFullYear()}-${(new Date().getMonth() + 1).toString().padStart(2, '0')}-${new Date().getDate().toString().padStart(2, '0')}`;
    const calendarCells = document.querySelectorAll('.calendar-cell');

    calendarCells.forEach(cell => {
        if (cell.dataset.date === todayString) {
            cell.classList.add('today');
        }
    });
}
colorToday();