// Enable editing of a form field
function enableEdit(fieldId, buttonId) {
    const fieldElement = document.getElementById(fieldId);
    const saveButton = document.getElementById(buttonId);
    const editButton = document.getElementById(fieldId + '_edit'); // Check if this is correct

    if (fieldElement && saveButton && editButton) {
        fieldElement.style.display = 'inline'; // Show the file input
        saveButton.style.display = 'inline'; // Show the save button
        editButton.style.display = 'none'; // Hide the edit button
    } else {
        console.error('Element(s) not found:', {
            fieldElement: fieldElement ? 'Found' : 'Not Found',
            saveButton: saveButton ? 'Found' : 'Not Found',
            editButton: editButton ? 'Found' : 'Not Found'
        });
    }
}

// Disable editing of a form field
function disableEdit(fieldId, buttonId) {
    const fieldElement = document.getElementById(fieldId);
    const saveButton = document.getElementById(buttonId);
    const editButton = document.getElementById(fieldId + '_edit');

    if (fieldElement && saveButton && editButton) {
        fieldElement.style.display = 'none'; // Hide the file input
        saveButton.style.display = 'none'; // Hide the save button
        editButton.style.display = 'inline'; // Show the edit button
    } else {
        console.error('Element(s) not found:', {
            fieldElement: fieldElement ? 'Found' : 'Not Found',
            saveButton: saveButton ? 'Found' : 'Not Found',
            editButton: editButton ? 'Found' : 'Not Found'
        });
    }
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

    fetch('update_admin_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            // Update the profile image display
            const imageDisplay = document.getElementById('profile_image_display');
            if (data.profile_image_url) {
                imageDisplay.src = data.profile_image_url;
            }
            // Optionally refresh or redirect
            // window.location.href = 'editpro.php'; // or location.reload();
        } else {
            alert('Failed to update profile: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

// Save changes for the profile image field
function saveChanges(fieldId) {
    const inputField = document.getElementById(fieldId);

    if (inputField) {
        // Ensure the input field remains visible
        inputField.style.display = 'inline';

        // Show success message and keep the input field visible
        showSavedMessage(fieldId);

        // Optionally disable editing after saving
        disableEdit(fieldId, fieldId + '_save');
    } else {
        console.error('Element not found:', fieldId);
    }
}

