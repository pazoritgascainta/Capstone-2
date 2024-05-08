const calendar = document.querySelector('.calendar');
const monthElement = calendar.querySelector('.month');
const datesElement = calendar.querySelector('.dates');

function generateCalendar(year, month) {
    monthElement.textContent = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });

    datesElement.innerHTML = '';

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDayIndex = new Date(year, month, 1).getDay();

    for (let i = 0; i < firstDayIndex; i++) {
        const dateElement = document.createElement('div');
        dateElement.classList.add('date', 'empty');
        datesElement.appendChild(dateElement);
    }

    for (let i = 1; i <= daysInMonth; i++) {
        const dateElement = document.createElement('div');
        dateElement.classList.add('date');
        dateElement.textContent = i;
        datesElement.appendChild(dateElement);
    }
}

const currentDate = new Date();
generateCalendar(currentDate.getFullYear(), currentDate.getMonth());

window.addEventListener('resize', () => {
    // Re-generate calendar to adjust for responsive design
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
});

document.addEventListener('DOMContentLoaded', function() {
    const popupButton = document.getElementById('popupButton');
    const overlay = document.querySelector('.overlay');
    const formContainer = document.querySelector('.form-container');

    popupButton.addEventListener('click', function() {
        formContainer.style.display = 'block'; // Show the form container
        document.body.style.overflow = 'hidden'; // Prevent scrolling on the body
        overlay.style.display = 'block'; // Show the overlay
    });

    overlay.addEventListener('click', function() {
        if (formContainer.style.display === 'block') {
            formContainer.style.display = 'none'; // Hide the form container
            document.body.style.overflow = 'auto'; // Enable scrolling on the body
            overlay.style.display = 'none'; // Hide the overlay
        }
    });

    formContainer.addEventListener('click', function(event) {
        event.stopPropagation(); // Prevent clicks on the form from closing the overlay
    });
});

const exitButton = document.getElementById('exitButton');
const formContainer = document.getElementById('formContainer');
const overlay = document.querySelector('.overlay');

exitButton.addEventListener('click', function() {
    formContainer.style.display = 'none'; // Hide the form container
    overlay.style.display = 'none'; // Hide the overlay
    document.body.style.overflow = 'auto'; // Enable scrolling on the body
});
