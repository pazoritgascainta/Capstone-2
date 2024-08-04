// main.js

import { renderCalendar, fetchAppointments, renderPagination } from './calendar.js';

document.addEventListener('DOMContentLoaded', function () {
    const calendar = document.getElementById('calendar');
    const monthYearSpan = document.getElementById('month-year');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');
    const appointmentsEl = document.getElementById('appointments-table');
    const selectedDateEl = document.getElementById('selected-date');
    const appointmentsTableContainer = document.getElementById('appointments-table-container');
    const paginationEl = document.getElementById('pagination');

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    const limit = 10;

    function fetchAndRenderAppointments(date, page) {
        fetchAppointments(date, page, limit, appointmentsEl, selectedDateEl, appointmentsTableContainer, renderPagination);
    }

    renderCalendar(calendar, monthYearSpan, currentMonth, currentYear, fetchAndRenderAppointments, limit);

    prevMonthButton.addEventListener('click', function () {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(calendar, monthYearSpan, currentMonth, currentYear, fetchAndRenderAppointments, limit);
    });

    nextMonthButton.addEventListener('click', function () {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(calendar, monthYearSpan, currentMonth, currentYear, fetchAndRenderAppointments, limit);
    });
});
