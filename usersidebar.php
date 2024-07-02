<div class="headnavbar">
    <nav>
        <img src="monique logo.png" width="120" height="30" alt="logo" id="logo-img">
        <ul>
            <li><a href='dashuser.php'>Home</a></li>
            <li>
                <a href='#'>Notifications</a>
                <div class="sub-menu-wrap" id="notificationsMenu">
                    <div class="sub-menu">
                        <a href="userinbox.php" class="sub-menu-link">
                            <img src="inbox.png" alt="">
                            <p>Inbox</p>
                            <span>></span>
                        </a>
                        <!-- <a href="#messages.php" class="sub-menu-link">
                            <img src="messages.png" alt="">
                            <p>Messages</p>
                            <span>></span>
                        </a> -->
                        <!-- Add more submenu items as needed -->
                        <!-- <li>
                <a href="settingsuser.php">
                    <img src="settings.png" alt="settings" class="sideimg">
                    <span class="nav-item">Settings</span>
                </a>
                <span class="tooltip">Settings</span>
            </li>
            <li>
                <a href="Homepage.php">
                    <img src="logawt.png" alt="logout" class="sideimg">
                    <span class="nav-item">Logout</span>
                </a>
                <span class="tooltip">Logout</span>
            </li>
                    </div>
                </div>
            </li> -->
        </ul>
        <img src="profile.png" class="user-pic" onclick="toggleProfileMenu()">
        <div class="sub-menu-wrap" id="profileMenu">
            <div class="sub-menu">
                <a href="useredit.php" class="sub-menu-link">
                    <img src="account.png" alt="">
                    <p>Edit Profile</p>
                    <span>></span>
                </a>
                <a href="usersettings.php" class="sub-menu-link">
                    <img src="settings.png" alt="">
                    <p>Settings</p>
                    <span>></span>
                </a>
                <a href="userhelp.php" class="sub-menu-link">
                    <img src="help.png" alt="">
                    <p>Help</p>
                    <span>></span>
                </a>
                <a href="Homepage.php" class="sub-menu-link">
                    <img src="logawt.png" alt="">
                    <p>Logout</p>
                    <span>></span>
                </a>
                <!-- Add more submenu items as needed -->
            </div>
        </div>
    </nav>
</div>

<div class="sidebar">
        <div class="top">
            <div class="logo">
            <img src="Monique logo.png" width="170" height="80"alt="monique" class="mnq-img">
            </div>
            <img src="menu.png" alt="menu" class="menu-img" id="btn">
        </div>
        <div class="user">
            <img src="profile.png" alt="monique" class="profile-img">
            <div>
            <p class="bold">John Doe</p>
            <p>User</p>
        </div>
        </div>
        <hr>
        <ul>
            <li>
                <a href="dashuser.php">
                    <img src="dashboard.png" alt="dashboard" class="sideimg">
                    <span class="nav-item">Dashboard</span>
                </a>
                <span class="tooltip">Dashboard</span>
            </li>
            <li>
                <a href="usercomplaint.php">
                    <img src="complaint.png" alt="complaints" class="sideimg">
                    <span class="nav-item">Complaints</span>
                </a>
                <span class="tooltip">Complaints</span>
            </li>
            <li>
                <a href="payment.php">
                    <img src="bill.png" alt="billing" class="sideimg">
                    <span class="nav-item">Payment</span>
                </a>
                <span class="tooltip">Payment</span>
            </li>
            <li>
                <a href="appointmentuser.php">
                    <img src="schedule.png" alt="schedule" class="sideimg">
                    <span class="nav-item">Appointment</span>
                </a>
                <span class="tooltip">Appointment</span>
            </li>
            <li>
                <a href="serviceuser.php">
                    <img src="service.png" alt="service" class="sideimg">
                    <span class="nav-item">Service </span>
                </a>
                <span class="tooltip">Service Requests</span>
            </li>
        </ul>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle function
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');
    let hr = document.querySelector('.sidebar hr');
    let logoImg = document.getElementById('logo-img');
    let subMenu = document.getElementById("subMenu");

    btn.onclick = function () {
        sidebar.classList.toggle('active');
        hr.classList.toggle('active'); 
        logoImg.classList.toggle('hide-logo'); // Toggle the class to hide/show the logo
    }

    // Profile menu toggle function
    function toggleProfileMenu() {
        const profileMenu = document.getElementById("profileMenu");
        const notificationsMenu = document.getElementById("notificationsMenu");

        profileMenu.classList.toggle("open-menu");

        // Close notifications menu if open
        if (notificationsMenu.classList.contains("open-menu")) {
            notificationsMenu.classList.remove("open-menu");
        }
    }

    // Notifications menu toggle function
    function toggleNotificationsMenu() {
        const notificationsMenu = document.getElementById("notificationsMenu");
        const profileMenu = document.getElementById("profileMenu");

        notificationsMenu.classList.toggle("open-menu");

        // Close profile menu if open
        if (profileMenu.classList.contains("open-menu")) {
            profileMenu.classList.remove("open-menu");
        }
    }

    // Click event listener for user-pic to toggle profile menu
    document.querySelector('.user-pic').addEventListener('click', function(event) {
        toggleProfileMenu();
        event.stopPropagation(); // Prevent the click from bubbling to document
    });

    // Click event listener for Notifications to toggle notifications menu
    document.querySelector('nav li:nth-child(2) a').addEventListener('click', function(event) {
        toggleNotificationsMenu();
        event.preventDefault(); // Prevent default link behavior
        event.stopPropagation(); // Prevent the click from bubbling to document
    });

    // Close menus if user clicks outside of them
    document.addEventListener('click', function(event) {
        const profileMenu = document.getElementById("profileMenu");
        const notificationsMenu = document.getElementById("notificationsMenu");

        if (!event.target.closest('.user-pic') && profileMenu.classList.contains('open-menu')) {
            profileMenu.classList.remove('open-menu');
        }

        if (!event.target.closest('nav li:nth-child(2)') && notificationsMenu.classList.contains('open-menu')) {
            notificationsMenu.classList.remove('open-menu');
        }
    });
});
</script>

