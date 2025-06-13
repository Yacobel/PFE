// Save profile function
function saveProfile() {
    const form = document.getElementById('editProfileForm');
    const saveBtn = document.getElementById('saveProfileBtn');
    
    if (!form || !saveBtn) return;
    
    // Show loading state
    const originalBtnText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    // Create FormData object
    const formData = new FormData(form);
    
    // Log form data for debugging
    console.log('Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Submit form using fetch
    fetch('api/update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Success:', data);
        if (data.success) {
            showNotification('Profile updated successfully!', 'success');
            
            // Update profile picture if it was changed
            if (data.profile_picture) {
                const profilePic = document.getElementById('profilePicture');
                if (profilePic) {
                    // Add timestamp to prevent caching
                    const timestamp = new Date().getTime();
                    profilePic.src = data.profile_picture + (data.profile_picture.includes('?') ? '&' : '?') + 't=' + timestamp;
                }
            }
            
            // Reload the page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to update profile');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error: ' + error.message, 'error');
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalBtnText;
    });
}

// Show notification function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span class="message">${message}</span>
        <button class="close-btn">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove notification after 5 seconds
    setTimeout(() => {
        notification.classList.add('hide');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
    
    // Close button functionality
    const closeBtn = notification.querySelector('.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            notification.classList.add('hide');
            setTimeout(() => notification.remove(), 300);
        });
    }
}
