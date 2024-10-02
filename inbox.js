
document.addEventListener('DOMContentLoaded', function() {
    const inboxNotificationDot = document.getElementById('inboxNotificationDot');
    const notificationSound = document.getElementById('notificationSound');
    const inboxList = document.getElementById('inbox-list');
    let notified = false; // Flag to track notification status

    function checkForNewMessages() {
        fetch('check_admin_new_messages.php')
            .then(response => response.json())
            .then(data => {
                if (data.newMessages) {
                    console.log("New message detected!");
                    inboxNotificationDot.style.display = 'block';
                    notificationSound.play(); // Play notification sound
                    notified = true; // Set flag to true after notification

                } else {
                    inboxNotificationDot.style.display = 'none';
                    notified = false; // Reset the flag if no new messages

                }
            })
            .catch(error => console.error('Error checking messages:', error));
    }

    function fetchAndRenderMessages() {
        fetch('fetch_admin_complaints.php')
            .then(response => response.json())
            .then(data => {
                renderMessages(data.messages);
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    function renderMessages(messages) {
        inboxList.innerHTML = '';
        messages.forEach(message => {
            const inboxItem = document.createElement('div');
            inboxItem.classList.add('inbox-item');
            inboxItem.innerHTML = `
                <div>
                <strong>Complaint</strong> - ${new Date(message.date).toLocaleString()} <!-- Display the date -->
                </div>
                <div>${message.message.substring(0, 50)}...</div>
            `;
            inboxItem.addEventListener('click', () => openModal(message));
            inboxList.appendChild(inboxItem);
        });
    }

    function openModal(message) {
        const modalContent = document.getElementById('message-details');
        const modal = document.getElementById('message-modal');
        modalContent.innerHTML = `
            <h2>Complaint</h2>
            <p>${message.message}</p>
        `;
        modal.style.display = 'block';
    }

    function initCloseModal() {
        const modal = document.getElementById('message-modal');
        const closeModal = document.getElementsByClassName('close')[0];

        closeModal.onclick = function () {
            modal.style.display = 'none';
        };

        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
    }

    // Initialize modal close functionality
    initCloseModal();

    // Check for new messages on page load
    checkForNewMessages();

    // Fetch and render messages on page load
    fetchAndRenderMessages();

    // Periodically check for new messages (every 5 seconds)
    setInterval(() => {
        checkForNewMessages();
    }, 5000);
});