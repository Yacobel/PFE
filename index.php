<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $pageTitle = "Home";
  include 'components/head.php';
  ?>
  <link rel="stylesheet" href="./style/style.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer" />
</head>

<body>
  <div class="main-container">
    <!-- Include Header Component -->
    <?php include 'components/header.php'; ?>

    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-info">
        <div class="rating">
          <p>
            Rated <span>4.9 ★</span> by Freelance Experts
            <img src="./style/images/rating.png" alt="Rating" />
          </p>
        </div>
        <div class="header-hero">
          <div class="headline">
            <h1>Get Your <span>Tasks</span> Done Anytime, Anywhere</h1>
          </div>
          <div class="description">
            <p>
              Post any task you need help with, and let skilled people in your
              area accept and complete it for you.
            </p>
          </div>
        </div>

        <div class="hero-btn">
          <a href="./register.php">Create Your Task Now
            <img src="./style/images/Clip path group.png" alt="Arrow icon" />
          </a>
        </div>
      </div>
    </div>

    <!-- Video Explanation Section -->
    <div class="video-explain">
      <div class="img">
        <!-- Video content will go here -->
      </div>
    </div>

    <!-- Solutions Section -->
    <div class="solution">
      <div class="our-solution">
        <p>Our Solutions</p>
      </div>
      <div class="headline">
        <h2>A Smarter Way to Get Things Done, From Start to Finish</h2>
        <p>
          Quickly describe what you need, set your location and deadline, and
          post your task. No complicated steps — just clarity and control.
        </p>
      </div>
      <div class="cards">
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="Simple Task Posting" />
          </div>
          <h3>Simple Task Posting</h3>
          <p>
            Easily describe your task and set your preferences in minutes.
          </p>
        </div>
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="Quick Matching" />
          </div>
          <h3>Quick Matching</h3>
          <p>Get connected with skilled taskers in your area quickly.</p>
        </div>
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="Secure Payments" />
          </div>
          <h3>Secure Payments</h3>
          <p>Pay only when your task is completed to your satisfaction.</p>
        </div>
      </div>
    </div>

    <!-- Advantages Section -->
    <div class="advantage">
      <div class="our-advantage">
        <p>Our Advantages</p>
      </div>
      <div class="headline">
        <h2>Why Thousands of Users Trust Us Every Day</h2>
        <p>
          Skill is designed to enhance your learning journey and advance your
          career. With top-tier resources and tailored support, we provide
          everything you need to achieve your objectives and beyond.
        </p>
      </div>

      <div class="advantage-cards">
        <div class="card1">
          <div class="images">
            <img
              src="./style/images/verification.png"
              alt="Learn from the Best Icon" />
          </div>
          <div class="info-card">
            <h3>Learn from the Best</h3>
            <p>
              Benefit from the expertise of top-notch instructors and industry
              professionals.
            </p>
          </div>
        </div>
        <div class="card2">
          <div class="images">
            <img src="./style/images/$.svg" alt="Affordable Learning Icon" />
          </div>
          <div class="info-card">
            <h3>Affordable Learning</h3>
            <p>
              Access high-quality courses at a price that fits your budget.
            </p>
          </div>
        </div>
        <div class="card3">
          <div class="images">
            <img src="./style/images/f.svg" alt="Join Our Community Icon" />
          </div>
          <div class="info-card">
            <h3>Join Our Community</h3>
            <p>
              Connect with fellow learners, share experiences, and grow
              together.
            </p>
          </div>
        </div>
        <div class="card4">
          <div class="images">
            <img
              src="./style/images/contact.svg"
              alt="Unlock Your Career Potential Icon" />
          </div>
          <div class="info-card">
            <h3>Unlock Your Career Potential</h3>
            <p>
              Gain the skills and knowledge to advance your career and open
              new opportunities.
            </p>
          </div>
        </div>
        <div class="card5">
          <div class="images">
            <img
              src="./style/images/location.svg"
              alt="Study Anytime, Anywhere Icon" />
          </div>
          <div class="info-card">
            <h3>Study Anytime, Anywhere</h3>
            <p>
              Enjoy the flexibility to learn at your own pace, whenever and
              wherever you want.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Services Section -->
    <div class="our-services">
      <div class="services-button-container">
        <button class="services-button">
          <img src="./style/images/aboute.png" alt="Services icon" />
          Our Services
        </button>
      </div>

      <div class="heading-section">
        <h2 class="main-heading">
          Find Help for Anything, Anytime Explore Our Most Popular Services
        </h2>
        <p class="sub-heading">
          We understand the value of your time, which is why we select only
          the most impactful courses. Each one is designed to provide the
          expertise and tools essential for achieving your goals.
        </p>
      </div>

      <div class="services-grid">
        <!-- Service Card 1 -->
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

    <!-- Testimonials Section -->
    <div class="testimonials-container">
      <!-- Header Section -->
      <div class="header">
        <!-- Logo and Counter -->
        <div class="branding">
          <div class="logo">
            <span>S</span>
          </div>
          <h1 class="counter">7.145<span>+</span></h1>
          <p class="subtitle">Our Ever Growing Community</p>
        </div>

        <!-- User Avatars -->
        <div class="users">
          <div class="avatar-group">
            <div class="avatar">
              <img
                src="https://randomuser.me/api/portraits/men/32.jpg"
                alt="User 1" />
            </div>
            <div class="avatar">
              <img
                src="https://randomuser.me/api/portraits/women/44.jpg"
                alt="User 2" />
            </div>
            <div class="avatar">
              <img
                src="https://randomuser.me/api/portraits/men/86.jpg"
                alt="User 3" />
            </div>
            <div class="avatar">
              <img
                src="https://randomuser.me/api/portraits/women/63.jpg"
                alt="User 4" />
            </div>
            <div class="avatar">
              <img
                src="https://randomuser.me/api/portraits/men/54.jpg"
                alt="User 5" />
            </div>
          </div>
          <div class="users-text">
            Join 11K+ Freelancers and start learning and earning more as an
            individual
          </div>
        </div>
      </div>

      <!-- Testimonial Grid -->
      <div class="testimonial-grid">
        <!-- Testimonial 1 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            تجربتي في مجموعة فريلانس كانت ممتعة ومفيدة للغاية. كنت قادرًا على
            التعلم من الخبراء وكسب دخل إضافي. منصة رائعة وتوفر محتوى مفيد
            جدًا.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Hossam Seddaui</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/men/32.jpg"
                  alt="Hossam Seddaui" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 2 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            فرص كانت ممتازة وأنا أوصي بشدة بالانضمام إلى هذه المنصة. حيث قمت
            بتطوير مهاراتي وكسب المزيد من المال.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Younes Zahiri</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/men/86.jpg"
                  alt="Younes Zahiri" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 3 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            I just landed my very first order for fewer days with Skill. I
            can't believe how challenging this was, and how I learned to kill
            it with effort and my heart. Start from scratch, between the line
            was how I've improved Skill's on a total magnitude. Thank you
            sooooo much
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Hajar Mendari</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/women/44.jpg"
                  alt="Hajar Mendari" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 4 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            It was an amazing and wonderful experience that allowed us to gain
            knowledge from diverse professionals. I highly encourage others to
            join to benefit from the members and their shared expertise.
            Everything has been great so far!
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Mohammed Arrousi</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/men/41.jpg"
                  alt="Mohammed Arrousi" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 5 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            Being part of Skill has been a game-changer! I've gained so much
            insight into managing freelance projects and turning them into
            bigger opportunities. The community is supportive, and the
            knowledge shared is invaluable. I definitely join for networking
            and learning.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Sara El Mansouri</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/women/63.jpg"
                  alt="Sara El Mansouri" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 6 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            لقد كانت تجربة رائعة للغاية لي في الفريلانس، وكان التعلم مفيدًا
            للغاية. أوصي بشدة زملائي المستقلين بالانضمام إلى هذه المنصة لأنها
            تساعد كثيرًا في تطوير المهارات وتحقيق دخل إضافي.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Hanae Qutbi</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/women/28.jpg"
                  alt="Hanae Qutbi" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 7 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            هذه المنصة كانت لي بمثابة تحول في طريقتي في كسب الدخل وتطوير
            مهاراتي. أتعامل مع فريق رائع من المحترفين وأتعلم منهم كل يوم. أنا
            ممتن جدًا لهذه الفرصة وأنصح الجميع بالانضمام إلى منصة سكيل.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Aymane Bouhlal</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/men/22.jpg"
                  alt="Aymane Bouhlal" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 8 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            Skill helped me unlock new strategies for improving my freelance
            workflow. From organizing tasks to delivering projects more
            efficiently, I've gained skills I never thought I'd master.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Amin Rachidi</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/men/54.jpg"
                  alt="Amin Rachidi" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 9 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            ฉันรู้สึกขอบคุณที่ได้เข้าร่วม Skill
            ที่ช่วยให้ฉันเติบโตในอาชีพฟรีแลนซ์.
            ได้เรียนรู้ทักษะใหม่ๆและการจัดการโปรเจกต์อย่างมีประสิทธิภาพ
            ซึ่งช่วยให้ฉันพัฒนาตัวเองได้อย่างมาก.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Sirin Boonma</h4>
                <p>Verified Skill Member</p>
              </div>
              <div class="user-avatar">
                <img
                  src="https://randomuser.me/api/portraits/women/76.jpg"
                  alt="Sirin Boonma" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- How It Works Section -->
    <div class="how-it-works">
      <div class="header">
        <div class="pill-button">How It Works</div>
        <h1 class="title">See How Our Approach<br />Drives You to Growth</h1>
        <p class="subtitle">
          Uncover the steps we take to enhance your learning experience<br />ensuring
          your growth every step of the way.
        </p>
      </div>

      <div class="cards-container">
        <!-- Card 1 -->
        <div class="card">
          <div class="card-icon">
            <img
              src="./style/images/Explore Illustration.png"
              alt="Explore Courses" />
          </div>
          <div class="card-info">
            <h3 class="card-title">Explore Courses</h3>
            <p class="card-text">
              Browse our extensive range of courses designed to match your
              interests and goals.
            </p>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="card">
          <div class="card-icon">
            <img
              src="./style/images/Learning Illustration.png"
              alt="Start Learning" />
          </div>
          <div class="card-info">
            <h3 class="card-title">Start Learning</h3>
            <p class="card-text">
              Purchase and begin learning at your own pace, anytime and
              anywhere.
            </p>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="card">
          <div class="card-icon">
            <img
              src="./style/images/Grow Illustration.png"
              alt="Achieve & Grow" />
          </div>
          <div class="card-info">
            <h3 class="card-title">Achieve & Grow</h3>
            <p class="card-text">
              Apply your new skills, advance your career, and reach your
              goals.
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="Earth">
      <!-- Earth image with light on left side -->

      <!-- Curved lines at bottom -->
      <div class="curved-lines">
        <div class="curved-line"></div>
        <div class="curved-line"></div>
        <div class="curved-line"></div>
      </div>

      <!-- Main content -->
      <div class="content">
        <button class="button">
          <!-- Cube icon -->
          <svg
            class="button-icon"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round">
            <path
              d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            <polyline points="3.29 7 12 12 20.71 7"></polyline>
            <line x1="12" y1="22" x2="12" y2="12"></line>
          </svg>
          <span class="button-text">Our Differentiations</span>
        </button>

        <h1>See How We Shine Among <br />Global Competitors</h1>

        <p>
          Explore the innovative features and community-driven approach that
          elevate our platform above the rest.
        </p>
      </div>
      <div class="earth-container">
        <img
          src="style/images/Global Map Illustrations.svg"
          alt="Earth with light on left side" />
      </div>
    </div>
    <div class="Quistion">
      <!-- Notification banner -->
      <div style="text-align: center; margin-bottom: 2rem">
        <div class="notification">
          Hey, Need one-time help? or just general ask 👋
        </div>
      </div>

      <!-- Heading -->
      <h1 class="heading">
        <span>Have a Questions </span>
        <span class="light">In Mind?</span>
      </h1>

      <!-- Subheading -->
      <p class="subheading">
        Fill out the form and let's talk about your career, wants goals and
        directions, or just ask questions about your needs
      </p>

      <!-- Form card -->
      <div class="form-card">
        <!-- Logo -->
        <div class="form-logo">skill.</div>

        <!-- Form -->
        <form>
          <!-- Name fields -->
          <div class="form-row">
            <div class="form-col">
              <label for="firstName">First Name</label>
              <input type="text" id="firstName" name="firstName" />
            </div>
            <div class="form-col">
              <label for="lastName">Last Name</label>
              <input type="text" id="lastName" name="lastName" />
            </div>
          </div>

          <!-- Contact fields -->
          <div class="form-row">
            <div class="form-col">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" />
            </div>
            <div class="form-col">
              <label for="phone">Phone Number</label>
              <div class="phone-input">
                <div class="country-code">+1</div>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  placeholder="(201) 555-0123" />
              </div>
            </div>
          </div>

          <!-- Message field -->
          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message"></textarea>
          </div>

          <!-- Submit button -->
          <button type="submit" class="submit-btn">Send Message</button>
        </form>
      </div>
    </div>
    <div class="FAQ">
      <div class="faq-container">
        <div class="faq-header">
          <button class="faq-button">
            <svg
              class="button-icon"
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
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            Frequently Asked Questions
          </button>
        </div>

        <h2 class="faq-title">Get Answers to Common Questions</h2>
        <p class="faq-subtitle">
          Find clear explanations for the most common inquiries about our
          platform, services, and how we can help you get your tasks
          completed.
        </p>

        <div class="faq-grid">
          <!-- FAQ Item 1 -->
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <div class="question-content">
                <svg
                  class="icon"
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
                  <line x1="12" y1="8" x2="12" y2="16"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <h3>How does the platform work?</h3>
              </div>
              <button
                class="toggle-btn"
                type="button"
                aria-label="Toggle answer"
                onclick="event.stopPropagation(); toggleFAQ(this.parentNode)">
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
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
            <div class="faq-answer">
              <p>
                Our platform connects people who need tasks completed with
                skilled individuals ready to help. Simply post your task, set
                your budget, and choose from qualified applicants who bid on
                your job. Once completed, you pay only when satisfied with the
                results.
              </p>
            </div>
          </div>

          <!-- FAQ Item 2 -->
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <div class="question-content">
                <svg
                  class="icon"
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
                  <line x1="12" y1="8" x2="12" y2="16"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <h3>How much does it cost to use the service?</h3>
              </div>
              <button
                class="toggle-btn"
                type="button"
                aria-label="Toggle answer"
                onclick="event.stopPropagation(); toggleFAQ(this.parentNode)">
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
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
            <div class="faq-answer">
              <p>
                Posting a task is completely free. We apply a small service
                fee (5-15% depending on the task type) only when your task is
                completed and you're ready to pay the tasker. This fee helps
                us maintain the platform and ensure quality service.
              </p>
            </div>
          </div>

          <!-- FAQ Item 3 -->
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <div class="question-content">
                <svg
                  class="icon"
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
                  <line x1="12" y1="8" x2="12" y2="16"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <h3>Is my payment secure?</h3>
              </div>
              <button
                class="toggle-btn"
                type="button"
                aria-label="Toggle answer"
                onclick="event.stopPropagation(); toggleFAQ(this.parentNode)">
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
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
            <div class="faq-answer">
              <p>
                Absolutely. We use industry-standard encryption and secure
                payment processing. Funds are held securely until you confirm
                the task is completed to your satisfaction, providing
                protection for both you and the tasker.
              </p>
            </div>
          </div>

          <!-- FAQ Item 4 -->
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <div class="question-content">
                <svg
                  class="icon"
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
                  <line x1="12" y1="8" x2="12" y2="16"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <h3>How are taskers verified?</h3>
              </div>
              <button
                class="toggle-btn"
                type="button"
                aria-label="Toggle answer"
                onclick="event.stopPropagation(); toggleFAQ(this.parentNode)">
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
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
            <div class="faq-answer">
              <p>
                All taskers undergo identity verification, background checks,
                and skills assessment before they can accept tasks.
                Additionally, our review and rating system helps maintain
                quality, as you can see feedback from previous clients before
                choosing a tasker.
              </p>
            </div>
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