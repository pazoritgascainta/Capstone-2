document.addEventListener('DOMContentLoaded', function() {
    // Fetch and display announcements

    fetch('fetch_announcements.php')
        .then(response => response.json())
        .then(data => {
            const inboxList = document.getElementById('inbox-list');
            inboxList.innerHTML = ''; // Clear existing messages

            data.forEach(announcement => {
                const inboxItem = document.createElement('div');
                inboxItem.classList.add('inbox-item');
                inboxItem.setAttribute('data-id', announcement.id); // Store the message id
                inboxItem.innerHTML = `
                    <div>
                        <strong>Announcement</strong> - ${announcement.date}
                    </div>
                    <div>${announcement.message}</div>
                `;
                
                inboxItem.addEventListener('click', function() {
                    // Open the message modal and display details
                    document.getElementById('message-details').innerText = announcement.message;
                    document.getElementById('message-modal').style.display = 'block';

                    // Mark the message as seen via AJAX
                    markMessageAsSeen(announcement.id);
                });

                inboxList.appendChild(inboxItem);
            });
        })
        .catch(error => console.error('Error fetching announcements:', error));
        
    const modal = document.getElementById('message-modal');
    const closeModalButton = document.querySelector('.close');

    // Close the modal when the close button is clicked
    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Close the modal when clicking outside of the modal content
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// Function to mark the message as seen
function markMessageAsSeen(messageId) {
    fetch('mark_message_seen.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messageId: messageId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Message marked as seen');
            checkForNewMessages(); // Recheck for new messages to hide notification dot if necessary
        } else {
            console.error('Failed to mark message as seen');
        }
    })
    .catch(error => console.error('Error:', error));
}
