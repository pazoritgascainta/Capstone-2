<!DOCTYPE html>
<html lang="en">
<head>
    <title>Appointment</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="datecs.css">
</head>
<body>
<?php include 'usersidebar.php'; ?>
    <div class="main-content">
        <div class="Container">
            <h1>St. Monique Appointment Test Schedule</h1>
        </div>

        <div class="popup">
            <p>Book your appointment here:</p>
            <button id="popupButton">Book Appointment</button>
        </div>
        <div class="overlay"></div>

        <div class="form-container" id="formContainer">
            <div class="form">
                <button class="exit-button" id="exitButton">X</button>
                <h2>Book Appointment</h2>
                <form>
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <label for="purpose">Purpose of Visit:</label>
                    <select id="purpose" name="purpose" required>
                        <option value="" disabled selected>Select purpose</option>
                        <option value="checkup">Reservation</option>
                        <option value="consultation">Consultation</option>
                    </select>
                    <div class="button-container">
                        <button type="submit">Book</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="calendar body">
            <div class="month"></div>
            <div class="days">
                <div class="day">Sun</div>
                <div class="day">Mon</div>
                <div class="day">Tue</div>
                <div class="day">Wed</div>
                <div class="day">Thu</div>
                <div class="day">Fri</div>
                <div class="day">Sat</div>
            </div>
            <div class="dates"></div>
        </div>
    </div>
    <script src="date.js"></script>
</body>
</html>
