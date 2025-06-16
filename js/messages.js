document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const newMessageBtn = document.getElementById('newMessageBtn');
    const newMessageModal = document.getElementById('newMessageModal');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const modalOverlays = document.querySelectorAll('.modal-overlay');
    const startChatBtn = document.getElementById('startChatBtn');
    
    // Open new message modal
    if (newMessageBtn) {
        newMessageBtn.addEventListener('click', function() {
            document.getElementById('newMessageModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close modal functions
    function closeAllModals() {
        modalOverlays.forEach(modal => {
            modal.classList.remove('active');
        });
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking close button or overlay
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeAllModals);
    });
    
    modalOverlays.forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeAllModals();
            }
        });
    });
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
    
    // Start new chat
    if (startChatBtn) {
        startChatBtn.addEventListener('click', function() {
            // Here you would typically handle starting a new chat
            closeAllModals();
            
            // Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'success-message';
            successMessage.innerHTML = `
                <i class="fas fa-check-circle"></i>
                Conversation started successfully!
                <button class="close-btn" onclick="this.parentElement.remove()">×</button>
            `;
            document.querySelector('.dashboard-container').insertBefore(successMessage, document.querySelector('.messages-header'));
            
            // Auto-remove message after 5 seconds
            setTimeout(() => {
                successMessage.remove();
            }, 5000);
        });
    }
    
    // Select conversation
    const conversationItems = document.querySelectorAll('.conversation-item');
    conversationItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            conversationItems.forEach(i => i.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
            
            // Here you would typically load the conversation messages
            // For demo, we'll just show a message
            const messagesList = document.querySelector('.messages-list');
            messagesList.innerHTML = `
                <div class="message received">
                    <div class="avatar">
                        <img src="images/default-avatar.png" alt="User">
                    </div>
                    <div class="message-content">
                        <p>Hello! How can I help you today?</p>
                        <span class="time">Just now</span>
                    </div>
                </div>
            `;
        });
    });
    
    // Send message
    const messageInput = document.querySelector('.message-input');
    const sendBtn = document.querySelector('.btn-send');
    
    function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            const messagesList = document.querySelector('.messages-list');
            const messageElement = document.createElement('div');
            messageElement.className = 'message sent';
            messageElement.innerHTML = `
                <div class="message-content">
                    <p>${message}</p>
                    <span class="time">Just now</span>
                </div>
                <div class="avatar">
                    <img src="images/default-avatar.png" alt="You">
                </div>
            `;
            messagesList.appendChild(messageElement);
            messageInput.value = '';
            
            // Auto-scroll to bottom
            messagesList.scrollTop = messagesList.scrollHeight;
            
            // Here you would typically send the message to the server
        }
    }
    
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }
    
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
    
    // Search functionality
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const conversations = document.querySelectorAll('.conversation-item');
            
            conversations.forEach(conversation => {
                const name = conversation.querySelector('h4').textContent.toLowerCase();
                const lastMessage = conversation.querySelector('.last-message').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                    conversation.style.display = 'flex';
                } else {
                    conversation.style.display = 'none';
                }
            });
        });
    }
});
