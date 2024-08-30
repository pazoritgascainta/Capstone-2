// Enable editing of a form field
function enableEdit(fieldId, buttonId) {
    document.getElementById(fieldId).style.display = 'inline'; // Show the input field
    document.getElementById(buttonId).style.display = 'inline'; // Show the save button
    document.getElementById(fieldId + '_edit').style.display = 'none'; // Hide the edit button
}

// Disable editing of a form field
function disableEdit(fieldId, buttonId) {
    document.getElementById(fieldId).style.display = 'none'; // Hide the input field
    document.getElementById(buttonId).style.display = 'none'; // Hide the save button
    document.getElementById(fieldId + '_edit').style.display = 'inline'; // Show the edit button
}

// Show "Changes Saved!" message and keep the input field visible
function showSavedMessage(fieldId) {
    const saveButton = document.getElementById(`${fieldId}_save`);
    
    if (saveButton) {
        saveButton.style.display = 'none'; // Hide the save button
    }

    const message = document.createElement('span');
    message.id = 'success_message';
    message.innerText = 'Changes Saved!';
    message.style.color = 'green';
    message.style.fontWeight = 'bold';
    message.style.marginLeft = '10px';
    document.getElementById(fieldId).parentNode.appendChild(message);

    // Show the save button again after 2 seconds
    setTimeout(() => {
        message.remove();
        if (saveButton) {
            saveButton.style.display = 'inline'; // Re-show the save button
        }
    }, 2000);
}

// Handle form submission
document.querySelector('#profileForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            // Update profile image display if needed
            if (data.profile_image_url) {
                document.querySelector('#profile_image_display').src = data.profile_image_url;
            }
            // Optionally refresh or redirect
            window.location.href = 'useredit.php'; // or location.reload();
        } else {
            alert('Failed to update profile: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

// Save changes for non-image fields and show message
function saveChanges(fieldId) {
    var inputField = document.getElementById(fieldId);
    if (inputField) {
        // Ensure the input field remains visible
        inputField.style.display = 'inline';

        // Show success message and keep the input field visible
        showSavedMessage(fieldId);
    }
}
