/**
 * task_executors.js - JavaScript functionality for the task executors page
 * Handles bid acceptance, messaging, and modal interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    window.acceptBid = function(bidId, taskId) {
        if (confirm('Are you sure you want to accept this bid? This will assign the task to this executor.')) {
            fetch('api/accept_bid.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    bid_id: bidId,
                    task_id: taskId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Bid accepted successfully! The task has been assigned to the executor.');
                    window.location.href = 'related_tasks.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while accepting the bid');
            });
        }
    };

    window.openMessageModal = function(recipientId) {
        const messageModal = document.getElementById('messageModal');
        if (messageModal) {
            messageModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            const recipientIdField = document.getElementById('recipient_id');
            if (recipientIdField) {
                recipientIdField.value = recipientId;
            }
        }
    };

    window.closeMessageModal = function() {
        const messageModal = document.getElementById('messageModal');
        if (messageModal) {
            messageModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    };

    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('message').value;
            const recipientId = document.getElementById('recipient_id').value;
            const taskId = document.querySelector('input[name="task_id"]').value;

            fetch('api/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: taskId,
                    recipient_id: recipientId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    closeMessageModal();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the message');
            });
        });
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            closeMessageModal();
        }
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('messageModal') && 
            document.getElementById('messageModal').classList.contains('active')) {
            closeMessageModal();
        }
    });
});
