document.addEventListener('DOMContentLoaded', function() {
    // FOR SIDEBAR TOGGLE
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');
    let hr = document.querySelector('.sidebar hr');
    let logoImg = document.getElementById('logo-img');
    let navItems = document.querySelectorAll('.sidebar .nav-item');

    function updateHoverState() {
        navItems.forEach(item => {
            if (!sidebar.classList.contains('active')) {
                item.classList.add('disable-hover');
            } else {
                item.classList.remove('disable-hover');
            }
        });
    }

    updateHoverState();

    btn.onclick = function () {
        sidebar.classList.toggle('active');
        hr.classList.toggle('active');
        logoImg.classList.toggle('hide-logo');
        updateHoverState();
    }

    // Profile and notifications menu toggles
    function toggleProfileMenu() {
        const profileMenu = document.getElementById("profileMenu");
        const notificationsMenu = document.getElementById("notificationsMenu");

        profileMenu.classList.toggle("open-menu");

        if (notificationsMenu.classList.contains("open-menu")) {
            notificationsMenu.classList.remove("open-menu");
        }
    }

    function toggleNotificationsMenu() {
        const notificationsMenu = document.getElementById("notificationsMenu");
        const profileMenu = document.getElementById("profileMenu");

        notificationsMenu.classList.toggle("open-menu");

        if (profileMenu.classList.contains("open-menu")) {
            profileMenu.classList.remove("open-menu");
        }
    }

    // Event listeners for menu toggles
    document.querySelector('.user-pic').addEventListener('click', function(event) {
        toggleProfileMenu();
        event.stopPropagation();
    });

    document.querySelector('.nav-link.notifications-link').addEventListener('click', function(event) {
        toggleNotificationsMenu();
        event.preventDefault();
        event.stopPropagation();
    });

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

    // Inbox notifications
    const inboxNotificationDot = document.getElementById('inboxNotificationDot');
    const notificationSound = document.getElementById('notificationSound');
    let notified = false; // Flag to track notification status

    function checkForNewMessages() {
        fetch('check_admin_new_messages.php')
            .then(response => response.json())
            .then(data => {
                if (data.newMessages) {
                    console.log("New message detected!");
                    inboxNotificationDot.style.display = 'block'; // Show the notification dot
                    if (!notified && notificationSound) {
                        notificationSound.play().catch(error => console.log("Error playing sound:", error)); // Play the notification sound
                    }
                    notified = true; // Set the flag to true
                } else {
                    inboxNotificationDot.style.display = 'none'; // Hide the notification dot if no new messages
                    notified = false; // Reset the flag
                }
            })
            .catch(error => console.error('Error checking messages:', error));
    }
    setInterval(checkForNewMessages, 5000); // Check for new messages every 5 seconds
});
