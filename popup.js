function showTimetable(date) {
    // Hide the days of the week
    document.querySelector('.days').style.display = 'none';

    // Show the selected day's details in month navigation
    let dateObj = new Date(date);
    let month = dateObj.toLocaleString('default', { month: 'long' });
    let dayOfWeek = dateObj.toLocaleString('default', { weekday: 'long' });
    let dayOfMonth = dateObj.getDate();
    document.querySelector('.month').textContent = `${month} ${dayOfMonth}, ${dateObj.getFullYear()} ${dayOfWeek}`;

    // Fetch and display timetable for the selected date
    fetchTimetable(date);
}


function fetchTimetable(date) {
    // AJAX call to fetch appointments for the selected date
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_timetable.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('timetable-container').innerHTML = xhr.responseText;
        }
    };
    xhr.send('date=' + date);
}

