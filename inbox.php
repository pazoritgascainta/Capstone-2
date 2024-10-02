<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_id = $_SESSION['admin_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$messagesPerPage = 20; // Number of messages per page
$offset = ($page - 1) * $messagesPerPage;

// Fetch messages for the admin with pagination
$sql = "SELECT id, message, date FROM admin_inbox WHERE admin_id = '$admin_id' ORDER BY date DESC LIMIT $offset, $messagesPerPage";
$result = $conn->query($sql);

// Calculate total pages
$totalMessagesResult = $conn->query("SELECT COUNT(*) AS totalMessages FROM admin_inbox WHERE admin_id = '$admin_id'");
$totalMessagesRow = $totalMessagesResult->fetch_assoc();
$totalPages = ceil($totalMessagesRow['totalMessages'] / $messagesPerPage);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="inbox.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1>ADMIN INBOX</h1>
        <div class="container">   
            <div class="inbox-container">
                <div class="inbox-list" id="inbox-list">
                    <!-- Messages will be dynamically added here -->
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="pagination" id="pagination">
                <!-- Pagination will be dynamically added here -->
            </div>

            <!-- Modal for message details -->
            <div id="message-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <div id="message-details"></div>
                </div>
            </div>

            <!-- Include the audio element for notification sound -->
            <audio id="notificationSound" src="notification-sound.mp3" preload="auto"></audio>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const inboxNotificationDot = document.getElementById('inboxNotificationDot');
                    const notificationSound = document.getElementById('notificationSound');
                    const inboxList = document.getElementById('inbox-list');
                    const pagination = document.getElementById('pagination');

                    function checkForNewMessages() {
                        fetch('check_admin_new_messages.php')
                            .then(response => response.json())
                            .then(data => {
                                if (data.newMessages) {
                                    console.log("New message detected!");
                                    inboxNotificationDot.style.display = 'block';
                                    notificationSound.play(); // Play notification sound
                                } else {
                                    inboxNotificationDot.style.display = 'none';
                                }
                            })
                            .catch(error => console.error('Error checking messages:', error));
                    }

                    function fetchAndRenderMessages(page = 1) {
                        fetch(`fetch_admin_inbox.php?page=${page}`)
                            .then(response => response.json())
                            .then(data => {
                                renderMessages(data.messages);
                                renderPagination(data.totalPages, page);
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
                                    <strong>Notification</strong>
                                </div>
                                <div>${message.message.substring(0, 50)}...</div>
                            `;
                            inboxItem.addEventListener('click', () => openModal(message));
                            inboxList.appendChild(inboxItem);
                        });
                    }

                    function renderPagination(totalPages, currentPage) {
                        pagination.innerHTML = '';
                        if (currentPage > 1) {
                            pagination.innerHTML += `<a href="#" data-page="${currentPage - 1}">Previous</a>`;
                        }
                        for (let i = 1; i <= totalPages; i++) {
                            pagination.innerHTML += `<a href="#" data-page="${i}" class="${i === currentPage ? 'active' : ''}">${i}</a>`;
                        }
                        if (currentPage < totalPages) {
                            pagination.innerHTML += `<a href="#" data-page="${currentPage + 1}">Next</a>`;
                        }

                        const links = pagination.querySelectorAll('a');
                        links.forEach(link => {
                            link.addEventListener('click', (e) => {
                                e.preventDefault();
                                const page = parseInt(link.getAttribute('data-page'));
                                fetchAndRenderMessages(page);
                            });
                        });
                    }

                    function openModal(message) {
                        const modalContent = document.getElementById('message-details');
                        const modal = document.getElementById('message-modal');
                        modalContent.innerHTML = `
                            <h2>Notification</h2>
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
            </script>
        </div>
    </div>
</body>
</html>

