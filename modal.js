// Function to open the modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
    }
}

// Function to close the modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling when modal is closed
    }
}

// Event listeners for modal close buttons
document.addEventListener('DOMContentLoaded', () => {
    const closeButtons = document.querySelectorAll('.modal-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            closeModal(button.closest('.modal').id);
        });
    });

    // Example: Close modal when clicking outside of it
    window.addEventListener('click', (event) => {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            const modalContent = modal.querySelector('.modal-content');
            if (modal.style.display === 'block' && !modalContent.contains(event.target)) {
                closeModal(modal.id);
            }
        });
    });
});

// Example function to open the modal with dynamic content
function openBookingModal(amenityId, date) {
    openModal('timeslot-modal');
    document.getElementById('hidden-amenity-id').value = amenityId;
    document.getElementById('selected-date').value = date;
}
