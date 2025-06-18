document.addEventListener("DOMContentLoaded", function () {
  window.previewImage = function (input) {
    const preview = input.parentElement.querySelector(".image-preview");
    const img = preview.querySelector("img");
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        if (img) {
          img.src = e.target.result;
          img.style.display = "block";
        } else {
          const newImg = document.createElement("img");
          newImg.src = e.target.result;
          newImg.alt = "Preview";
          newImg.style.maxWidth = "100%";
          newImg.style.maxHeight = "200px";
          preview.innerHTML = "";
          preview.appendChild(newImg);
        }
      };
      reader.readAsDataURL(input.files[0]);
    }
  };
  window.openCreateTaskModal = function () {
    const modal = document.getElementById("createTaskModal");
    if (modal) {
      modal.classList.add("active");
      document.body.style.overflow = "hidden";
      const form = document.getElementById("createTaskForm");
      if (form) {
        form.reset();
        const errors = form.querySelectorAll(".alert-error");
        errors.forEach((error) => error.remove());
      }
    }
  };
  window.closeCreateTaskModal = function () {
    const modal = document.getElementById("createTaskModal");
    if (modal) {
      modal.classList.remove("active");
      document.body.style.overflow = "auto";
    }
  };
  const setupCreateTaskForm = function () {
    const createTaskForm = document.getElementById("createTaskForm");
    if (createTaskForm) {
      createTaskForm.addEventListener("submit", async function (e) {
        e.preventDefault();
        const existingErrors = this.querySelectorAll(".alert-error");
        existingErrors.forEach((error) => error.remove());
        const submitBtn = this.querySelector('button[type="submit"]');
        if (!submitBtn) {
          return;
        }
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML =
          '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;
        try {
          const formData = new FormData(this);
          const requiredFields = [
            "title",
            "description",
            "category_id",
            "budget",
            "deadline",
          ];
          for (const field of requiredFields) {
            const value = formData.get(field);
            if (!value || value.trim() === "") {
              throw new Error(`${field.replace("_", " ")} is required`);
            }
          }
          const budget = parseFloat(formData.get("budget"));
          if (isNaN(budget) || budget <= 0) {
            throw new Error("Budget must be a positive number");
          }
          const deadline = new Date(formData.get("deadline"));
          const today = new Date();
          if (deadline <= today) {
            throw new Error("Deadline must be in the future");
          }
          this.submit();
          return;
        } catch (error) {
          const errorDiv = document.createElement("div");
          errorDiv.className = "alert alert-error";
          errorDiv.textContent =
            error.message || "An error occurred. Please try again.";
          const modalBody = document.querySelector(".modal-body");
          if (modalBody) {
            modalBody.insertBefore(errorDiv, modalBody.firstChild);
          }
        } finally {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        }
      });
    }
    const submitBtn = document.querySelector('button[form="createTaskForm"]');
    if (submitBtn) {
      submitBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const form = document.getElementById("createTaskForm");
        if (form) {
          const submitEvent = new Event("submit", { cancelable: true });
          form.dispatchEvent(submitEvent);
        }
      });
    }
  };
  window.openPaymentModal = function (taskId, taskTitle, budget) {
    document.getElementById("payment_task_id").value = taskId;
    document.getElementById("payment_task_title").textContent = taskTitle;
    document.getElementById("payment_amount").textContent =
      "$" + budget.toFixed(2);
    document.getElementById("paymentModal").classList.add("active");
    document.body.style.overflow = "hidden";
  };
  window.closePaymentModal = function () {
    document.getElementById("paymentModal").classList.remove("active");
    document.body.style.overflow = "auto";
    document.getElementById("paymentForm").reset();
  };
  window.redirectToPayment = function (event) {
    event.preventDefault();
    const taskId = document.getElementById("payment_task_id").value;
    if (!taskId) {
      alert("Invalid task ID. Please try again.");
      return false;
    }
    window.location.href = "process_payment.php?task_id=" + taskId;
    return false;
  };
  window.openEditTaskModal = function (taskData) {
    document.getElementById("edit_task_id").value = taskData.id;
    document.getElementById("edit_title").value = taskData.title;
    document.getElementById("edit_description").value = taskData.description;
    document.getElementById("edit_category").value = taskData.category_id;
    document.getElementById("edit_budget").value = taskData.budget;
    document.getElementById("edit_deadline").value = taskData.deadline;
    document.getElementById("edit_location").value = taskData.location || "";
    const imagePreview = document.getElementById("current_image_preview");
    if (taskData.image_url) {
      imagePreview.innerHTML = `
                <img src="${taskData.image_url}" alt="Current task image" style="max-width: 100%; max-height: 200px;">
            `;
    } else {
      imagePreview.innerHTML = `<span class="placeholder">No image currently set</span>`;
    }
    document.getElementById("editTaskModal").classList.add("active");
  };
  window.closeEditTaskModal = function () {
    document.getElementById("editTaskModal").classList.remove("active");
  };
  const setupEditTaskForm = function () {
    const editTaskForm = document.getElementById("editTaskForm");
    if (editTaskForm) {
      editTaskForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("api/update_task.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              window.location.reload();
            } else {
              alert("Error updating task: " + data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Error updating task. Please try again.");
          });
      });
    }
  };
  const setupImagePreview = function () {
    const editImage = document.getElementById("edit_image");
    if (editImage) {
      editImage.addEventListener("change", function (e) {
        const preview = document.getElementById("current_image_preview");
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            preview.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px;">
                        `;
          };
          reader.readAsDataURL(file);
        }
      });
    }
  };
  window.onclick = function (event) {
    if (event.target.classList.contains("modal-overlay")) {
      if (event.target.id === "editTaskModal") {
        closeEditTaskModal();
      } else if (event.target.id === "createTaskModal") {
        closeCreateTaskModal();
      } else if (event.target.id === "paymentModal") {
        closePaymentModal();
      }
    }
  };
  window.deleteTask = function (taskId) {
    if (
      confirm(
        "Are you sure you want to delete this task? This action cannot be undone."
      )
    ) {
      fetch("api/delete_task.php?id=" + taskId)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            window.location.reload();
          } else {
            alert("Error deleting task: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Error deleting task. Please try again.");
        });
    }
  };
  setupCreateTaskForm();
  setupEditTaskForm();
  setupImagePreview();
});
