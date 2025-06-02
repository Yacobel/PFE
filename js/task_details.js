/**
 * task_details.js - JavaScript functionality for the task details page
 * Handles task actions, bidding, messaging, and payment processing
 */

document.addEventListener('DOMContentLoaded', function() {
    // Task Actions
    window.acceptTask = function(taskId) {
        if (confirm('Are you sure you want to accept this task?')) {
            fetch('api/accept_task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        task_id: taskId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Task accepted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while accepting the task');
                });
        }
    };

    window.rejectTask = function(taskId) {
        if (confirm('Are you sure you want to reject this task?')) {
            fetch('api/reject_task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        task_id: taskId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Task rejected successfully');
                        window.location.href = 'taskes.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting the task');
                });
        }
    };

    // Modal Functions
    window.openMessageModal = function() {
        document.getElementById('messageModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        // Set the recipient to the client by default
        const recipientIdElement = document.getElementById('recipient_id');
        if (recipientIdElement && recipientIdElement.value === '') {
            // The PHP value will be injected when the page loads
            // This is just a fallback in case the element exists but has no value
        }
    };
    
    window.openMessageModalWithRecipient = function(recipientId) {
        document.getElementById('messageModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        // Set the specific recipient
        document.getElementById('recipient_id').value = recipientId;
    };

    window.closeMessageModal = function() {
        document.getElementById('messageModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    };

    // Bid Form Submission
    const bidForm = document.getElementById('bidForm');
    if (bidForm) {
        bidForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const taskId = document.getElementById('task_id').value;
            const bidAmount = document.getElementById('bid_amount').value;
            const proposalText = document.getElementById('proposal_text').value;
            
            fetch('api/accept_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: taskId,
                    bid_amount: bidAmount,
                    proposal_text: proposalText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Your bid has been submitted successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting your bid');
            });
        });
    }
    
    // Message Form Submission
    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('message').value;
            const recipientId = document.getElementById('recipient_id').value;
            const taskId = document.querySelector('input[name="task_id"]').value || 
                           document.getElementById('task_id').value;

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

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            closeMessageModal();
        }
    };

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('messageModal').classList.contains('active')) {
            closeMessageModal();
        }
    });
    
    // Process Payment Function
    window.processPayment = function(taskId) {
        if (confirm('Are you sure you want to confirm task completion and process payment? This action cannot be undone.')) {
            fetch('api/process_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: taskId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show payment success modal
                    alert('Payment processed successfully! The task has been marked as completed.');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the payment');
            });
        }
    };
    
    // Cancel Task Function
    window.cancelTask = function(taskId) {
        if (confirm('Are you sure you want to cancel your assignment for this task? This will allow the client to select another executor.')) {
            const reason = document.getElementById('cancel_reason') ? 
                           document.getElementById('cancel_reason').value : '';
            
            fetch('api/cancel_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: taskId,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Task assignment cancelled successfully. The client can now select another executor.');
                    window.location.href = 'my_assignments.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while cancelling the task');
            });
        }
    };

    // Accept Bid Function (for clients)
    window.acceptBid = function(bidId, taskId) {
        if (confirm('Are you sure you want to accept this bid? This will assign the executor to your task.')) {
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
                    alert('Bid accepted successfully! The executor has been assigned to your task.');
                    window.location.reload();
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
});
