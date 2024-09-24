 // Function to format date as "Month Day, Year"
    function formatDate(date) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString(undefined, options);
    }

    // Set current date in "Month Day, Year" format
    document.getElementById('current-date').textContent = formatDate(new Date());

    // Function to get current month and year
    function getCurrentMonth() {
        const today = new Date();
        const options = { month: 'long', year: 'numeric' }; // Display full month name and year
        return today.toLocaleDateString(undefined, options); // E.g., "September 2024"
    }

    // Get the anchor tag by ID and append the current month
    document.getElementById("billing-link").textContent += getCurrentMonth();

    // // Refresh button functionality (You can add actual refresh logic here)
    // document.getElementById("refresh-button").addEventListener("click", function() {
    //     alert("Refresh Rates clicked!");
    // });

    // File upload handler
    document.getElementById("upload-button").addEventListener("click", function() {
        const fileInput = document.getElementById("upload-file");
        if (fileInput.files.length === 0) {
            alert("No file selected!");
        } else {
            alert("File uploaded: " + fileInput.files[0].name);
        }
    });