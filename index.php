<?php
require_once 'config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
  <?php
  $pageTitle = __("home");
  include 'components/head.php';
  ?>
  <link rel="stylesheet" href="./style/style.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer" />
  <style>

  </style>
</head>

<body>

  <div class="main-container">
    <!-- Include Header Component -->
    <?php include 'components/header.php'; ?>
    <div class="language-selector">
      <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">En</a>
      <a href="?lang=ar" class="<?php echo $lang === 'ar' ? 'active' : ''; ?>">Ar</a>
    </div>

    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-info">

        <div class="header-hero">
          <div class="headline">
            <h1><?php echo __("hero_title"); ?></h1>
          </div>
          <div class="description">
            <p>
              <?php echo __("hero_description"); ?>
            </p>
          </div>
        </div>

        <div class="hero-btn">
          <a href="./register.php"><?php echo __("add_service_now"); ?>
            <img src="./style/images/Clip path group.png" alt="<?php echo __("arrow_icon"); ?>" />
          </a>
        </div>
      </div>
    </div>




    <!-- Solutions Section -->
    <div class="solution">
      <div class="our-solution">
        <p><?php echo __("our_solutions"); ?></p>
      </div>
      <div class="headline">
        <h2><?php echo __("smart_way_title"); ?></h2>
        <p>
          <?php echo __("smart_way_description"); ?>
        </p>
      </div>
      <div class="cards">
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="<?php echo __("easy_posting"); ?>" />
          </div>
          <h3><?php echo __("easy_posting"); ?></h3>
          <p>
            <?php echo __("easy_posting_description"); ?>
          </p>
        </div>
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="<?php echo __("quick_matching"); ?>" />
          </div>
          <h3><?php echo __("quick_matching"); ?></h3>
          <p><?php echo __("quick_matching_description"); ?></p>
        </div>
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="<?php echo __("secure_payment"); ?>" />
          </div>
          <h3><?php echo __("secure_payment"); ?></h3>
          <p><?php echo __("secure_payment_description"); ?></p>
        </div>
      </div>
    </div>

    <!-- Advantages Section -->
    <div class="advantage">
      <div class="our-advantage">
        <p><?php echo __("why_choose_us"); ?></p>
      </div>
      <div class="headline">
        <h2><?php echo __("why_thousands_title"); ?></h2>
        <p>
          <?php echo __("why_thousands_description"); ?>
        </p>
      </div>

      <div class="advantage-cards">
        <div class="card1">
          <!-- <div class="images">
            <img
              src="./style/images/verification.png"
              alt="<?php echo __("verified_users_icon"); ?>" />
          </div> -->
          <div class="info-card">
            <h3><?php echo __("verified_users"); ?></h3>
            <p>
              <?php echo __("verified_users_description"); ?>
            </p>
          </div>
        </div>

        <div class="card2">
          <!-- <div class="images">
            <img src="./style/images/$.svg" alt="<?php echo __("affordable_prices_icon"); ?>" />
          </div> -->
          <div class="info-card">
            <h3><?php echo __("affordable_prices"); ?></h3>
            <p>
              <?php echo __("affordable_prices_description"); ?>
            </p>
          </div>
        </div>

        <div class="card3">
          <!-- <div class="images">
            <img src="./style/images/f.svg" alt="<?php echo __("community_icon"); ?>" />
          </div> -->
          <div class="info-card">
            <h3><?php echo __("cooperative_community"); ?></h3>
            <p>
              <?php echo __("cooperative_community_description"); ?>
            </p>
          </div>
        </div>

        <div class="card4">
          <!-- <div class="images">
            <img
              src="./style/images/contact.svg"
              alt="<?php echo __("new_opportunities_icon"); ?>" />
          </div> -->
          <div class="info-card">
            <h3><?php echo __("new_opportunities"); ?></h3>
            <p>
              <?php echo __("new_opportunities_description"); ?>
            </p>
          </div>
        </div>

        <div class="card5">
          <!-- <div class="images">
            <img
              src="./style/images/location.svg"
              alt="<?php echo __("service_anywhere_icon"); ?>" />
          </div> -->
          <div class="info-card">
            <h3><?php echo __("service_anywhere"); ?></h3>
            <p>
              <?php echo __("service_anywhere_description"); ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Services Section -->
    <div class="our-services">
      <div class="services-button-container">
        <button class="services-button">
          <img src="./style/images/aboute.png" alt="<?php echo __("services_icon"); ?>" />
          <?php echo __("available_services"); ?>
        </button>
      </div>

      <div class="heading-section">
        <h2 class="main-heading">
          <?php echo __("find_help_title"); ?>
        </h2>
        <p class="sub-heading">
          <?php echo __("find_help_description"); ?>
        </p>
      </div>

      <div class="services-grid">

        <div class="service-card">
          <div class="card-image">
            <img
              src="./style/images/Gradient+Image.png"
              alt="Delivery & Errands" />
          </div>
          <div class="card-content">
            <h3 class="card-title">Delivery & Errands</h3>
            <div class="price-container">
              <span class="current-price">$9.99</span>
            </div>
            <p class="card-description">
              Deep cleaning, regular tidying, or moving-out services.
            </p>

            <div class="card-details">
              <div class="detail-item">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>6+ Hours of work</span>
              </div>

              <div class="detail-item">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <line x1="18" y1="20" x2="18" y2="10"></line>
                  <line x1="12" y1="20" x2="12" y2="4"></line>
                  <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>Beginner</span>
              </div>
            </div>

            <button class="browse-button">Browse Work</button>
          </div>
        </div>

        <!-- Service Card 2 -->
        <div class="service-card">
          <div class="card-image">
            <img
              src="./style/images/Gradient+Image (1).png"
              alt="Delivery & Errands" />
          </div>
          <div class="card-content">
            <h3 class="card-title">Delivery & Errands</h3>
            <div class="price-container">
              <span class="current-price">$10.00</span>
            </div>
            <p class="card-description">
              Grocery runs, package delivery, or forgotten items.
            </p>

            <div class="card-details">
              <div class="detail-item">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>15+ Hours of Work</span>
              </div>

              <div class="detail-item">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <line x1="18" y1="20" x2="18" y2="10"></line>
                  <line x1="12" y1="20" x2="12" y2="4"></line>
                  <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>Beginner To Intermediate</span>
              </div>
            </div>

            <button class="browse-button">Browse Work</button>
          </div>
        </div>

        <!-- Service Card 3 -->
        <div class="service-card">
          <div class="card-image">
            <img
              src="./style/images/Gradient+Image.png"
              alt="Moving Assistance" />
          </div>
          <div class="card-content">
            <h3 class="card-title">Moving Assistance</h3>
            <div class="price-container">
              <span class="current-price">$20.00</span>
            </div>
            <p class="card-description">
              Packing, lifting, or full relocation support.
            </p>

            <div class="card-details">
              <div class="detail-item">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>3+ Hours of Work</span>
              </div>

              <div class="detail-item">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <line x1="18" y1="20" x2="18" y2="10"></line>
                  <line x1="12" y1="20" x2="12" y2="4"></line>
                  <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>Beginner</span>
              </div>
            </div>

            <button class="browse-button">Browse Work</button>
          </div>
        </div>

      </div>
    </div>



    <!-- How It Works Section -->
    <div class="how-it-works">
      <div class="header">
        <div class="pill-button"><?php echo __("how_it_works"); ?></div>
        <h1 class="title">
          <?php echo __("how_it_works_title"); ?>
        </h1>
        <p class="subtitle">
          <?php echo __("how_it_works_subtitle"); ?>
        </p>
      </div>

      <div class="cards-container">
        <!-- Card 1 -->
        <div class="card">

          <div class="card-info">
            <h3 class="card-title"><?php echo __("step_1_title"); ?></h3>
            <p class="card-text">
              <?php echo __("step_1_text"); ?>
            </p>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="card">

          <div class="card-info">
            <h3 class="card-title"><?php echo __("step_2_title"); ?></h3>
            <p class="card-text">
              <?php echo __("step_2_text"); ?>
            </p>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="card">

          <div class="card-info">
            <h3 class="card-title"><?php echo __("step_3_title"); ?></h3>
            <p class="card-text">
              <?php echo __("step_3_text"); ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="Quistion">
      <div style="text-align: center; margin-bottom: 2rem">
        <div class="notification">
          <?php echo __("need_help"); ?>
        </div>
      </div>

      <h1 class="heading">
        <span><?php echo __("have_questions"); ?></span>
        <span class="light"><?php echo __("in_mind"); ?></span>
      </h1>

      <p class="subheading">
        <?php echo __("questions_subtitle"); ?>
      </p>

      <div class="form-card">
        <div class="form-logo">ServeMatch</div>
        <form>
          <div class="form-row">
            <div class="form-col">
              <label for="firstName"><?php echo __("first_name"); ?></label>
              <input type="text" id="firstName" name="firstName" />
            </div>
            <div class="form-col">
              <label for="lastName"><?php echo __("last_name"); ?></label>
              <input type="text" id="lastName" name="lastName" />
            </div>
          </div>

          <div class="form-row">
            <div class="form-col">
              <label for="email"><?php echo __("email"); ?></label>
              <input type="email" id="email" name="email" />
            </div>
            <div class="form-col">
              <label for="phone"><?php echo __("phone_number"); ?></label>
              <div class="phone-input">
                <div class="country-code">+1</div>
                <input type="tel" id="phone" name="phone" placeholder="(201) 555-0123" />
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="message"><?php echo __("your_message"); ?></label>
            <textarea id="message" name="message"></textarea>
          </div>

          <button type="submit" class="submit-btn"><?php echo __("send_message"); ?></button>
        </form>
      </div>
    </div>

    <div class="FAQ">
      <div class="faq-container">
        <div class="faq-header">
          <button class="faq-button">
            <svg class="button-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <?php echo __("frequently_asked"); ?>
          </button>
        </div>

        <h2 class="faq-title"><?php echo __("faq_title"); ?></h2>
        <p class="faq-subtitle">
          <?php echo __("faq_subtitle"); ?>
        </p>

        <div class="faq-grid">
          <!-- FAQ Item 1 -->
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <div class="question-content">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="16"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <h3><?php echo __("how_platform_works"); ?></h3>
              </div>
              <button class="toggle-btn" type="button" aria-label="Toggle answer" onclick="event.stopPropagation(); toggleFAQ(this.parentNode)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
            <div class="faq-answer">
              <p><?php echo __("platform_works_answer"); ?></p>
            </div>
          </div>

          <!-- FAQ Item 2 -->
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <div class="question-content">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="16"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <h3><?php echo __("how_to_use_service"); ?></h3>
              </div>
              <button class="toggle-btn" type="button" aria-label="Toggle answer" onclick="event.stopPropagation(); toggleFAQ(this.parentNode)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
            <div class="faq-answer">
              <p><?php echo __("service_usage_answer"); ?></p>
            </div>
          </div>

          <!-- FAQ Item 3 -->
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <div class="question-content">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="16"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <h3><?php echo __("how_to_pay"); ?></h3>
              </div>
              <button class="toggle-btn" type="button" aria-label="Toggle answer" onclick="event.stopPropagation(); toggleFAQ(this.parentNode)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
            <div class="faq-answer">
              <p><?php echo __("payment_answer"); ?></p>
            </div>
          </div>

          <!-- FAQ Item 4 -->
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <div class="question-content">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="16"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <h3><?php echo __("how_to_verify_users"); ?></h3>
              </div>
              <button class="toggle-btn" type="button" aria-label="Toggle answer" onclick="event.stopPropagation(); toggleFAQ(this.parentNode)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
            <div class="faq-answer">
              <p><?php echo __("user_verification_answer"); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Include Footer Component -->
    <?php include 'components/footer.php'; ?>

    <script src="js/main.js"></script>
</body>

</html>