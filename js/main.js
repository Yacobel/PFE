document.addEventListener("DOMContentLoaded", function () {
  const menuIcon = document.querySelector(".icone");
  const navLinks = document.querySelector(".nav-links");
  if (menuIcon && navLinks) {
    menuIcon.addEventListener("click", function () {
      navLinks.classList.toggle("mobile");
    });
  }
  function setupFAQ() {
    const faqItems = document.querySelectorAll(".faq-item");
    faqItems.forEach((item) => {
      const question = item.querySelector(".faq-question");
      if (question) {
        question.addEventListener("click", function () {
          if (item.classList.contains("active")) {
            item.classList.remove("active");
          } else {
            faqItems.forEach((faqItem) => {
              faqItem.classList.remove("active");
            });
            item.classList.add("active");
          }
        });
      }
    });
  }
  setupFAQ();
});
function toggleFAQ(element) {
  const faqItem = element.classList.contains("faq-question")
    ? element.closest(".faq-item")
    : element;
  if (faqItem.classList.contains("active")) {
    faqItem.classList.remove("active");
  } else {
    document.querySelectorAll(".faq-item").forEach((item) => {
      item.classList.remove("active");
    });
    faqItem.classList.add("active");
  }
}
