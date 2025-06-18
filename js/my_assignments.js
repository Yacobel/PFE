document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("cancellationModal");
  const span = document.getElementsByClassName("close")[0];

  if (span) {
    span.onclick = function () {
      closeModal();
    };
  }

  window.onclick = function (event) {
    if (event.target == modal) {
      closeModal();
    }
  };
});

function showCancellationModal(taskId) {
  const modal = document.getElementById("cancellationModal");
  document.getElementById("taskIdToCancel").value = taskId;
  document.getElementById("cancellationReason").value = "";
  modal.style.display = "block";
}

function closeModal() {
  const modal = document.getElementById("cancellationModal");
  modal.style.display = "none";
}

function confirmCancellation() {
  const taskId = document.getElementById("taskIdToCancel").value;
  const reason = document.getElementById("cancellationReason").value;

  fetch("api/update_task_status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      task_id: taskId,
      status: "cancelled",
      reason: reason,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while cancelling the task");
    });

  closeModal();
}

function updateTaskStatus(taskId, newStatus) {
  if (confirm("Are you sure you want to update this task's status?")) {
    fetch("api/update_task_status.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        task_id: taskId,
        status: newStatus,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.reload();
        } else {
          alert("Error: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred while updating the task status");
      });
  }
}
