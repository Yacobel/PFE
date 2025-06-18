document.addEventListener("DOMContentLoaded", function () {
  const newMessageBtn = document.getElementById("newMessageBtn");
  const newMessageModal = document.getElementById("newMessageModal");
  const closeModalBtns = document.querySelectorAll(".close-modal");
  const modalOverlays = document.querySelectorAll(".modal-overlay");
  const startChatBtn = document.getElementById("startChatBtn");
  if (newMessageBtn) {
    newMessageBtn.addEventListener("click", function () {
      document.getElementById("newMessageModal").classList.add("active");
      document.body.style.overflow = "hidden";
    });
  }
  function closeAllModals() {
    modalOverlays.forEach((modal) => {
      modal.classList.remove("active");
    });
    document.body.style.overflow = "";
  }
  closeModalBtns.forEach((btn) => {
    btn.addEventListener("click", closeAllModals);
  });
  modalOverlays.forEach((overlay) => {
    overlay.addEventListener("click", function (e) {
      if (e.target === overlay) {
        closeAllModals();
      }
    });
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeAllModals();
    }
  });
  if (startChatBtn) {
    startChatBtn.addEventListener("click", function () {
      closeAllModals();
      const successMessage = document.createElement("div");
      successMessage.className = "success-message";
      successMessage.innerHTML = `
                <i class="fas fa-check-circle"></i>
                Conversation started successfully!
                <button class="close-btn" onclick="this.parentElement.remove()">×</button>
            `;
      document
        .querySelector(".dashboard-container")
        .insertBefore(
          successMessage,
          document.querySelector(".messages-header")
        );
      setTimeout(() => {
        successMessage.remove();
      }, 5000);
    });
  }
  const conversationItems = document.querySelectorAll(".conversation-item");
  conversationItems.forEach((item) => {
    item.addEventListener("click", function () {
      conversationItems.forEach((i) => i.classList.remove("active"));
      this.classList.add("active");
      const messagesList = document.querySelector(".messages-list");
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
  const messageInput = document.querySelector(".message-input");
  const sendBtn = document.querySelector(".btn-send");
  function sendMessage() {
    const message = messageInput.value.trim();
    if (message) {
      const messagesList = document.querySelector(".messages-list");
      const messageElement = document.createElement("div");
      messageElement.className = "message sent";
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
      messageInput.value = "";
      messagesList.scrollTop = messagesList.scrollHeight;
    }
  }
  if (sendBtn) {
    sendBtn.addEventListener("click", sendMessage);
  }
  if (messageInput) {
    messageInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        sendMessage();
      }
    });
  }
  const searchInput = document.querySelector(".search-bar input");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();
      const conversations = document.querySelectorAll(".conversation-item");
      conversations.forEach((conversation) => {
        const name = conversation.querySelector("h4").textContent.toLowerCase();
        const lastMessage = conversation
          .querySelector(".last-message")
          .textContent.toLowerCase();
        if (name.includes(searchTerm) || lastMessage.includes(searchTerm)) {
          conversation.style.display = "flex";
        } else {
          conversation.style.display = "none";
        }
      });
    });
  }
});
