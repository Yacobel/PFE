document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu toggle
  const menuIcon = document.querySelector(".icone");
  const navLinks = document.querySelector(".nav-links");

  if (menuIcon && navLinks) {
    menuIcon.addEventListener("click", function () {
      navLinks.classList.toggle("mobile");
    });
  }

  // Simple FAQ functionality
  function setupFAQ() {
    const faqItems = document.querySelectorAll(".faq-item");

    faqItems.forEach((item) => {
      const question = item.querySelector(".faq-question");

      if (question) {
        question.addEventListener("click", function () {
          // If this item is already active, close it
          if (item.classList.contains("active")) {
            item.classList.remove("active");
          } else {
            // First close all items
            faqItems.forEach((faqItem) => {
              faqItem.classList.remove("active");
            });

            // Then open the clicked one
            item.classList.add("active");
          }
        });
      }
    });
  }

  // Initialize FAQ
  setupFAQ();
});

// Add global toggleFAQ function for inline HTML onclick events
function toggleFAQ(element) {
  // If the element is the question div, get its parent (the faq-item)
  const faqItem = element.classList.contains("faq-question")
    ? element.closest(".faq-item")
    : element;

  // Toggle the active class
  if (faqItem.classList.contains("active")) {
    faqItem.classList.remove("active");
  } else {
    // Close all other FAQs
    document.querySelectorAll(".faq-item").forEach((item) => {
      item.classList.remove("active");
    });

    // Open this one
    faqItem.classList.add("active");
  }
}
