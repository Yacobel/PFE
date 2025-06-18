const createTaskModal = document.getElementById("createTaskModal");
const taskDeadline = document.getElementById("ire ");

if (createTaskModal) {
  createTaskModal.style.display = "none";
}

if (taskDeadline) {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0");
  const dd = String(today.getDate()).padStart(2, "0");
  const hh = String(today.getHours()).padStart(2, "0");
  const min = String(today.getMinutes()).padStart(2, "0");
  taskDeadline.min = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
}

function openCreateTaskModal() {
  if (createTaskModal) {
    createTaskModal.style.display = "flex";
    createTaskModal.classList.add("active");
    document.body.style.overflow = "hidden";
  }
}

function closeCreateTaskModal() {
  if (createTaskModal) {
    createTaskModal.classList.remove("active");
    setTimeout(() => {
      createTaskModal.style.display = "none";
    }, 300);
    document.body.style.overflow = "auto";
    document.getElementById("createTaskForm").reset();
    const preview = document.querySelector("#imagePreview img");
    const placeholder = document.querySelector("#imagePreview .placeholder");
    if (preview && placeholder) {
      preview.style.display = "none";
      placeholder.style.display = "flex";
    }
  }
}

function handleImagePreview(input) {
  const preview = document.querySelector("#imagePreview img");
  const placeholder = document.querySelector("#imagePreview .placeholder");

  if (preview && placeholder && input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = "block";
      placeholder.style.display = "none";
    };

    reader.readAsDataURL(input.files[0]);
  } else if (preview && placeholder) {
    preview.style.display = "none";
    placeholder.style.display = "flex";
  }
}

function validateTaskForm(event) {
  const form = event.target;
  const categoryId = form.querySelector("#category").value;

  if (!categoryId) {
    event.preventDefault();
    showNotification("Please select a category", "error");
    return false;
  }

  const deadline = new Date(form.querySelector("#taskDeadline").value);
  const now = new Date();

  if (deadline <= now) {
    event.preventDefault();
    showNotification("Deadline must be in the future", "error");
    return false;
  }

  const budget = parseFloat(form.querySelector("#taskBudget").value);
  if (budget <= 0) {
    event.preventDefault();
    showNotification("Budget must be greater than 0", "error");
    return false;
  }

  const imageInput = form.querySelector("#taskImage");
  if (!imageInput.files || !imageInput.files[0]) {
    event.preventDefault();
    showNotification("Please select an image", "error");
    return false;
  }

  return true;
}

document.addEventListener("DOMContentLoaded", function () {
  if (createTaskModal) {
    createTaskModal.style.display = "none";
  }

  if (createTaskModal) {
    createTaskModal.addEventListener("click", function (e) {
      if (e.target === this) {
        closeCreateTaskModal();
      }
    });
  }

  document.addEventListener("keydown", function (e) {
    if (
      e.key === "Escape" &&
      createTaskModal &&
      createTaskModal.classList.contains("active")
    ) {
      closeCreateTaskModal();
    }
  });

  const createTaskForm = document.getElementById("createTaskForm");
  if (createTaskForm) {
    createTaskForm.addEventListener("submit", validateTaskForm);
  }

  const taskImage = document.getElementById("taskImage");
  if (taskImage) {
    taskImage.addEventListener("change", function () {
      handleImagePreview(this);
    });
  }

  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has("success")) {
    showNotification("Task created successfully!", "success");
  } else if (urlParams.has("error")) {
    showNotification(decodeURIComponent(urlParams.get("error")), "error");
  }
});

function showNotification(message, type = "success") {
  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas fa-${
        type === "success" ? "check-circle" : "exclamation-circle"
      }"></i>
      <span>${message}</span>
    </div>
    <button onclick="this.parentElement.remove()">×</button>
  `;
  document.body.appendChild(notification);

  setTimeout(() => {
    notification.remove();
  }, 5000);
}
