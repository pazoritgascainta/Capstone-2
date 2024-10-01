document.addEventListener('DOMContentLoaded', function() {
    // FOR SIDEBAR
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

    document.querySelector('.user-pic').addEventListener('click', function(event) {
        toggleProfileMenu();
        event.stopPropagation();
    });

    document.querySelector('nav li:nth-child(2) a').addEventListener('click', function(event) {
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
    // NEW MESSAGE NOTIFICATION FUNCTIONALITY
    let notified = false; // Flag to track notification status

    function checkForNewMessages() {
        fetch('check_new_messages.php')
            .then(response => response.json())
            .then(data => {
                if (data.newMessages && !notified) {
                    console.log("New message detected!");
                    inboxNotificationDot.style.display = 'block';
                    notificationSound.play(); // Play notification sound
                    notified = true; // Set flag to true after notification
                } else if (!data.newMessages && notified) {
                    inboxNotificationDot.style.display = 'none'; // Hide the notification dot if there are no new messages
                    notified = false; // Reset the flag if no new messages
                }
            })
            .catch(error => console.error('Error checking messages:', error));
    }

    const inboxNotificationDot = document.getElementById('inboxNotificationDot');
    const notificationSound = document.getElementById('notificationSound');

    // Check for new messages on page load
    checkForNewMessages();

    // Periodically check for new messages (every 5 seconds)
    setInterval(() => {
        checkForNewMessages();
    }, 5000);
});