// Chart.js code for generating charts
const ctxVisitor = document.getElementById('visitorChart').getContext('2d');
const visitorChart = new Chart(ctxVisitor, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [
            {
                label: 'Loyal Customers',
                data: [200, 190, 220, 210, 250, 240, 230, 220, 260, 280, 270, 300],
                borderColor: '#9b59b6',
                fill: false,
            },
            {
                label: 'New Customers',
                data: [150, 170, 160, 180, 200, 220, 210, 190, 230, 250, 240, 260],
                borderColor: '#e74c3c',
                fill: false,
            },
            {
                label: 'Unique Customers',
                data: [170, 180, 190, 210, 230, 250, 240, 230, 270, 290, 280, 310],
                borderColor: '#2ecc71',
                fill: false,
            }
        ]
    }
});

const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctxRevenue, {
    type: 'bar',
    data: {
        labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        datasets: [
            {
                label: 'Online Sales',
                data: [12000, 14000, 18000, 16000, 20000, 22000, 25000],
                backgroundColor: '#3498db',
            },
            {
                label: 'Offline Sales',
                data: [8000, 10000, 12000, 15000, 18000, 14000, 20000],
                backgroundColor: '#2ecc71',
            }
        ]
    }
});

const ctxSatisfaction = document.getElementById('satisfactionChart').getContext('2d');
const satisfactionChart = new Chart(ctxSatisfaction, {
    type: 'line',
    data: {
        labels: ['Last Month', 'This Month'],
        datasets: [
            {
                label: 'Satisfaction Score',
                data: [3.004, 4.504],
                borderColor: '#1abc9c',
                fill: false,
            }
        ]
    }
});

const ctxTarget = document.getElementById('targetChart').getContext('2d');
const targetChart = new Chart(ctxTarget, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Reality Sales',
                data: [8000, 8500, 9000, 8200, 8800, 8900],
                backgroundColor: '#f39c12',
            },
            {
                label: 'Target Sales',
                data: [12000, 11000, 11500, 13000, 12500, 14000],
                backgroundColor: '#e67e22',
            }
        ]
    }
});

document.getElementById('announcementForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const announcementInput = document.getElementById('announcementInput').value;
    const tableBody = document.getElementById('announcementTableBody');

    if (announcementInput) {
        const row = document.createElement('tr');

        const announcementCell = document.createElement('td');
        announcementCell.textContent = announcementInput;

        const dateCell = document.createElement('td');
        dateCell.textContent = new Date().toLocaleDateString();

        row.appendChild(announcementCell);
        row.appendChild(dateCell);

        tableBody.appendChild(row);

        // Clear input after submission
        document.getElementById('announcementInput').value = '';
    }
});

function openEditForm(announcementId) {
    var content = document.querySelector('input[name="content"][value="' + announcementId + '"]').value;
    document.getElementById('editAnnouncementId').value = announcementId;
    document.getElementById('editContent').value = content;
    document.getElementById('editForm').style.display = 'block';
}
   


