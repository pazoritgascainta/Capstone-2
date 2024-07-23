 <link rel="stylesheet" href="dashbcss.css">  -
<div class="headnavbar">
    <nav>
        <img src="monique logo.png" width="120" height="30" alt="logo" id="logo-img">
        <ul>
            <li><a href='dashadmin.php'>Home</a></li>
            <li>
                <a href='#'>Notifications</a>
                <div class="sub-menu-wrap" id="notificationsMenu">
                    <div class="sub-menu">
                        <a href="inbox.php" class="sub-menu-link">
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
                    </div>
                </div>
            </li>
        </ul>
        <img src="profile.png" class="user-pic" onclick="toggleProfileMenu()">
        <div class="sub-menu-wrap" id="profileMenu">
            <div class="sub-menu">
                <a href="EditPro.php" class="sub-menu-link">
                    <img src="account.png" alt="">
                    <p>Edit Profile</p>
                    <span>></span>
                </a>
                <a href="settingsadmin.php" class="sub-menu-link">
                    <img src="settings.png" alt="">
                    <p>Settings</p>
                    <span>></span>
                </a>
                <a href="help.php" class="sub-menu-link">
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

<!-- Sidebar -->

<div class="sidebar">
    <div class="top">
        <div class="logo">
            <img src="monique logo.png" width="170" height="80" alt="monique" class="mnq-img">
        </div>
        <img src="menu.png" alt="menu" class="menu-img" id="btn">
    </div>
    <div class="user">
        <img src="profile.png" alt="monique" class="profile-img">
        <div>
            <p class="bold">John Doe</p>
            <p>Admin</p>
        </div>
    </div>
      <hr>

        <ul>
    <li>
        <a href="dashadmin.php">
            <img src="dashboard.png" alt="dashboard" class="sideimg">
            <span class="nav-item">Dashboard</span>
        </a>
        <span class="tooltip">Dashboard</span>
    </li>
    <li>
        <a href="homeowneradmin.php">
            <img src="homeowner.png" alt="homeonwer" class="sideimg">
            <span class="nav-item">Homeowner</span>
        </a>
        <span class="tooltip">Homeowner</span>
    </li>
    <li>
            <a href="admincomplaint.php">
                <img src="complaint.png" alt="complaints" class="sideimg">
                <span class="nav-item">Complaints</span>
            </a>
            <span class="tooltip">Complaints</span>
        </li>
    <li>
        <a href="billingadmin.php">
            <img src="bill.png" alt="billing" class="sideimg">
            <span class="nav-item">Billing</span>
        </a>
        <span class="tooltip">Billing</span>
    </li>
    <li>
        <a href="recordingadmin.php">
            <img src="record.png" alt="recording" class="sideimg">
            <span class="nav-item">Recording</span>
        </a>
        <span class="tooltip">Recording</span>
    </li>
    <li>
        <a href="appointmentadmin.php">
            <img src="schedule.png" alt="schedule" class="sideimg">
            <span class="nav-item">Appointment</span>
        </a>
        <span class="tooltip">Appointment</span>
    </li>
    <li>
        <a href="serviceadmin.php">
            <img src="service.png" alt="service" class="sideimg">
            <span class="nav-item">Service  </span>
        </a>
        <span class="tooltip">Service Requests</span>
    </li>
    <li>
        <a href="reportadmin.php">
            <img src="report.png" alt="report" class="sideimg">
            <span class="nav-item">Report</span>
        </a>
        <span class="tooltip">Report</span>
    </li>

</ul>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FOR SIDEBAR
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');
    let hr = document.querySelector('.sidebar hr');
    let logoImg = document.getElementById('logo-img');
    let navItems = document.querySelectorAll('.sidebar .nav-item');

    // Function to toggle the disable-hover class
    function updateHoverState() {
        navItems.forEach(item => {
            if (!sidebar.classList.contains('active')) {
                item.classList.add('disable-hover');
            } else {
                item.classList.remove('disable-hover');
            }
        });
    }

    // Initial state setup
    updateHoverState();

    btn.onclick = function () {
        sidebar.classList.toggle('active');
        hr.classList.toggle('active'); 
        logoImg.classList.toggle('hide-logo'); // PAG TOGGLE MAWAWALA LOGO
        updateHoverState(); // Update hover state on toggle
    }

    // FOR PROFILE MENU TOGGLE
    function toggleProfileMenu() {
        const profileMenu = document.getElementById("profileMenu");
        const notificationsMenu = document.getElementById("notificationsMenu");

        profileMenu.classList.toggle("open-menu");

        // FOR CLOSING NOTIF
        if (notificationsMenu.classList.contains("open-menu")) {
            notificationsMenu.classList.remove("open-menu");
        }
    }

    // FOR Notifications menu toggle 
    function toggleNotificationsMenu() {
        const notificationsMenu = document.getElementById("notificationsMenu");
        const profileMenu = document.getElementById("profileMenu");

        notificationsMenu.classList.toggle("open-menu");

        // FOR CLOSING MENU
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

        if (!profileMenu.contains(event.target) && profileMenu.classList.contains("open-menu")) {
            profileMenu.classList.remove("open-menu");
        }

        if (!notificationsMenu.contains(event.target) && notificationsMenu.classList.contains("open-menu")) {
            notificationsMenu.classList.remove("open-menu");
        }
    });
});

</script>


