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
    .then(async response => {
        // First, get the response text to handle both JSON and HTML responses
        const responseText = await response.text();
        
        try {
            // Try to parse as JSON
            const data = JSON.parse(responseText);
            
            if (!response.ok) {
                throw new Error(data.message || 'Server returned an error');
            }
            
            return data;
        } catch (e) {
            // If we can't parse as JSON, log the raw response for debugging
            console.error('Failed to parse JSON response. Raw response:', responseText);
            throw new Error('Invalid response from server. Please try again.');
        }
    })
    .then(data => {
        console.log('Success:', data);
        
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response format from server');
        }
        
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
        // Show more detailed error message in development
        const errorMessage = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
            ? error.message + ' (Check console for details)' 
            : 'An error occurred while updating your profile. Please try again.';
            
        showNotification('Error: ' + errorMessage, 'error');
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

// Handle Switch Role Button
const switchRoleBtn = document.querySelector('.btn-switch');
if (switchRoleBtn) {
    switchRoleBtn.addEventListener('click', (event) => {
        event.preventDefault();
        const url = switchRoleBtn.getAttribute('href');
        if (url) {
            window.location.href = url;
        }
    });
}

// Handle Language Switch Button
const langSwitchBtn = document.querySelector('.btn-lang-switch');
if (langSwitchBtn) {
    langSwitchBtn.addEventListener('click', (event) => {
        event.preventDefault();
        const url = langSwitchBtn.getAttribute('href');
        if (url) {
            window.location.href = url;
        }
    });
}
