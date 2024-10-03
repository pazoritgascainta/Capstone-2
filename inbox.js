document.addEventListener('DOMContentLoaded', function() {
    const inboxList = document.getElementById('inbox-list');
    const pagination = document.getElementById('pagination');
    const notificationSound = document.getElementById('notificationSound');
    const inboxNotificationDot = document.getElementById('inboxNotificationDot');
    let notified = false; // Flag to track notification status

    // Function to fetch and render messages for a specific page
    function fetchAndRenderMessages(page = 1) {
        fetch(`fetch_admin_inbox.php?page=${page}`)
            .then(response => response.json())
            .then(data => {
                renderMessages(data.messages);  // Render the messages
                renderPagination(data.totalPages, data.currentPage);  // Update pagination
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    // Function to render messages in the inbox
    function renderMessages(messages) {
        inboxList.innerHTML = '';
        if (messages.length > 0) {
            messages.forEach(message => {
                const inboxItem = document.createElement('div');
                inboxItem.classList.add('inbox-item');
                inboxItem.innerHTML = `
                    <div>
                        <strong>Notification</strong> - ${new Date(message.date).toLocaleString()}
                    </div>
                    <div>${message.message.substring(0, 50)}...</div>
                `;
                // Add click event to open modal for full message
                inboxItem.addEventListener('click', () => openModal(message));
                inboxList.appendChild(inboxItem);
            });
        } else {
            inboxList.innerHTML = '<p>No messages found.</p>';
        }
    }

    // Function to render pagination links
    function renderPagination(totalPages, currentPage) {
        pagination.innerHTML = '';  // Clear existing pagination

        if (totalPages > 1) {
            // Previous button
            if (currentPage > 1) {
                pagination.innerHTML += `<a href="#" data-page="${currentPage - 1}">Previous</a>`;
            }

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                pagination.innerHTML += `<a href="#" data-page="${i}" class="${i === currentPage ? 'active' : ''}">${i}</a>`;
            }

            // Next button
            if (currentPage < totalPages) {
                pagination.innerHTML += `<a href="#" data-page="${currentPage + 1}">Next</a>`;
            }

            // Add event listeners to pagination links
            const links = pagination.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(link.getAttribute('data-page'));
                    fetchAndRenderMessages(page);  // Fetch and display messages for the clicked page
                });
            });
        }
    }

    // Function to open the modal with the full message content
    function openModal(message) {
        const modalContent = document.getElementById('message-details');
        const modal = document.getElementById('message-modal');
        modalContent.innerHTML = `
            <h2>Notification</h2>
            <p>${message.message}</p>
        `;
        modal.style.display = 'block';
    }

    // Initialize close modal functionality
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

    // Function to check for new messages
    function checkForNewMessages() {
        fetch('check_admin_new_messages.php')
            .then(response => response.json())
            .then(data => {
                if (data.newMessages && !notified) {
                    console.log("New message detected!");
                    inboxNotificationDot.style.display = 'block';
                    notificationSound.play();  // Play notification sound
                    notified = true;  // Set the notification flag to true
                } else {
                    inboxNotificationDot.style.display = 'none';
                    notified = false;  // Reset the notification flag
                }
            })
            .catch(error => console.error('Error checking messages:', error));
    }

    // Initialize the modal close functionality
    initCloseModal();

    // Fetch and render messages for the first page on page load
    fetchAndRenderMessages();

    // Periodically check for new messages (every 5 seconds)
    setInterval(() => {
        checkForNewMessages();
    }, 5000);
});
