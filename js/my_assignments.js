/**
 * My Assignments Page JavaScript
 * Handles task cancellation, status updates, and modal functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the modal
    const modal = document.getElementById("cancellationModal");
    const span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    if (span) {
        span.onclick = function() {
            closeModal();
        };
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    };
});

/**
 * Show the cancellation modal
 * @param {number} taskId - The ID of the task to cancel
 */
function showCancellationModal(taskId) {
    const modal = document.getElementById("cancellationModal");
    document.getElementById("taskIdToCancel").value = taskId;
    document.getElementById("cancellationReason").value = "";
    modal.style.display = "block";
}

/**
 * Close the cancellation modal
 */
function closeModal() {
    const modal = document.getElementById("cancellationModal");
    modal.style.display = "none";
}

/**
 * Confirm cancellation and submit the form
 */
function confirmCancellation() {
    const taskId = document.getElementById("taskIdToCancel").value;
    const reason = document.getElementById("cancellationReason").value;

    fetch('api/update_task_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            task_id: taskId,
            status: 'cancelled',
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while cancelling the task');
    });

    closeModal();
}

/**
 * Update a task's status
 * @param {number} taskId - The ID of the task
 * @param {string} newStatus - The new status to set
 */
function updateTaskStatus(taskId, newStatus) {
    if (confirm('Are you sure you want to update this task\'s status?')) {
        fetch('api/update_task_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                task_id: taskId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the task status');
        });
    }
}
