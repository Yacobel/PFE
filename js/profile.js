function saveProfile() {
    const form = document.getElementById('editProfileForm');
    const saveBtn = document.getElementById('saveProfileBtn');
    
    if (!form || !saveBtn) return;
    
    const originalBtnText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    const formData = new FormData(form);
    
    console.log('Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    fetch('api/update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const responseText = await response.text();
        
        try {
            const data = JSON.parse(responseText);
            
            if (!response.ok) {
                throw new Error(data.message || 'Server returned an error');
            }
            
            return data;
        } catch (e) {
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
            
            if (data.profile_picture) {
                const profilePic = document.getElementById('profilePicture');
                if (profilePic) {
                    const timestamp = new Date().getTime();
                    profilePic.src = data.profile_picture + (data.profile_picture.includes('?') ? '&' : '?') + 't=' + timestamp;
                }
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to update profile');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMessage = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
            ? error.message + ' (Check console for details)' 
            : 'An error occurred while updating your profile. Please try again.';
            
        showNotification('Error: ' + errorMessage, 'error');
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalBtnText;
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span class="message">${message}</span>
        <button class="close-btn">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('hide');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
    
    const closeBtn = notification.querySelector('.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            notification.classList.add('hide');
            setTimeout(() => notification.remove(), 300);
        });
    }
}

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
