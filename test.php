<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Scheduling</title>
    <link href="fullcalendar.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
        form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        label, input, select {
            display: block;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div id="calendar"></div>

    <form id="appointmentForm">
        <h2>Book Appointment</h2>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>
        <label for="time">Time:</label>
        <input type="time" id="time" name="time" required>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="purpose">Purpose:</label>
        <select id="purpose" name="purpose" required>
            <option value="" disabled selected>Select purpose</option>
            <option value="reservation">Reservation</option>
            <option value="consultation">Consultation</option>
            <!-- Add more options as needed -->
        </select>
        <input type="submit" value="Book Appointment">
    </form>

    <script src="moment.min.js"></script>
    <script src="jquery.min.js"></script>
    <script src="fullcalendar.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['dayGrid'],
                events: 'fetch_events.php', // Path to your PHP script that fetches events
                selectable: true,
                select: function(info) {
                    // When a date is selected, populate the date input field
                    document.getElementById('date').value = info.startStr;
                },
                eventRender: function(info) {
                    // Customize event rendering (optional)
                    var element = info.el;
                    // You can customize how events are displayed here
                }
            });
            calendar.render();

            // Form submission via AJAX
            document.getElementById('appointmentForm').addEventListener('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                fetch('schedule_appointment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert('Appointment booked successfully!');
                    calendar.refetchEvents(); // Refresh calendar events after booking
                    document.getElementById('appointmentForm').reset(); // Clear form fields
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });
    </script>
</body>
</html>
