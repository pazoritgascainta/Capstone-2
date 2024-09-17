<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Service</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="Serviceadmin.css">

</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="Container">
        <h1>St. Monique Service</h1>
    </div>
        
        <!-- Sidebar for categories -->
        <div class="servicesidebar">
            <div class="menu-item" onclick="loadRequests('maintenance')">Maintenance</div>
            <div class="menu-item" onclick="loadRequests('cleanup')">General Cleanup</div>
            <div class="menu-item" onclick="loadRequests('lawn')">Lawn Cleaning</div>
            <div class="menu-item" onclick="loadRequests('road')">Road Repair</div>
            <div class="menu-item" onclick="loadRequests('other')">Other</div>
        </div>

        <!-- Content Area for displaying requests -->
        <div class="content-area">
            <!-- Request cards will load here -->
        </div>
    </div>

<script>
  // Mock data for each category (you can replace this with an API call or dynamic content)
  const requests = {
    maintenance: [
      { title: "St. Monique Ph 2c4", name: "John Doe", block: "Block 5", date: "10 July" },
      { title: "Block 3 Repair", name: "Jane Smith", block: "Block 3", date: "15 August" }
    ],
    cleanup: [
      { title: "General Clean-up", name: "Alice Green", block: "Block 7", date: "5 July" },
      { title: "Waste Management", name: "Bob Brown", block: "Block 2", date: "20 August" }
    ],
    lawn: [
      { title: "Lawn Maintenance", name: "Carlos Lopez", block: "Block 9", date: "12 September" }
    ],
    road: [
      { title: "Road Repair", name: "Emily White", block: "Block 1", date: "9 August" }
    ],
    other: [
      { title: "Water Leak", name: "Tom Black", block: "Block 8", date: "29 June" }
    ]
  };

  // Function to load the requests for a category
  function loadRequests(category) {
    const contentArea = document.querySelector('.content-area');
    contentArea.innerHTML = ''; // Clear previous content

    // Get requests for selected category
    const categoryRequests = requests[category];

    if (categoryRequests && categoryRequests.length > 0) {
      categoryRequests.forEach(request => {
        const requestCard = `
          <div class="request-card">
            <div class="request-info">
              <h3>${request.title}</h3>
              <p>${request.name}, ${request.block}</p>
              <p>Posted: ${request.date}</p>
            </div>
            <div class="request-actions">
              <button class="approve-btn">Approve</button>
              <button class="edit-btn">Edit</button>
              <button class="delete-btn">Delete</button>
            </div>
          </div>
        `;
        contentArea.innerHTML += requestCard; // Add each card
      });
    } else {
      contentArea.innerHTML = '<p>No requests found for this category.</p>';
    }
  }

  // Load default category (e.g., Maintenance) on page load
  loadRequests('maintenance');
</script>


</body>

</html>