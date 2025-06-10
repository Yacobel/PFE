<!DOCTYPE html>
<html lang="ar">

<head>
  <?php
  $pageTitle = "الرئيسية";
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
            من عند خبراء الفريلانس <span>4.9 ★</span> تقييمنــا
            <img src="./style/images/rating.png" alt="تقييم" />
          </p>
        </div>
        <div class="header-hero">
          <div class="headline">
            <h1>خلي <span>المهام</span> يتدارو فأي وقت وفأي بلاصة</h1>
          </div>
          <div class="description">
            <p>
              زيد خدمتك، وخلي الناس القريبين ليك لي عندهم المهارات ياخدوها وينفذوها ليك.
            </p>
          </div>
        </div>

        <div class="hero-btn">
          <a href="./register.php">زيد خدمتك دابا!
            <img src="./style/images/Clip path group.png" alt="آيقونة السهم" />
          </a>
        </div>
      </div>
    </div>

    <!-- Video Explanation Section -->
    <div class="video-explain">
      <div class="img">
        <!-- غادي يكون هنا فيديو الشرح -->
      </div>
    </div>

    <!-- Solutions Section -->
    <div class="solution">
      <div class="our-solution">
        <p>الحلول ديالنا:</p>
      </div>
      <div class="headline">
        <h2>طريقة ذكية باش تدير المهام ديالك من الأول حتى للآخر</h2>
        <p>
          وصف الخدمة ديالك بسرعة، حدد المدينة والوقت، وزيدها فالموقع. بلا ما تدوخ، كلشي واضح وساهل.
        </p>
      </div>
      <div class="cards">
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="نشر المهمة بسهولة" />
          </div>
          <h3>نشر المهمة بسهولة</h3>
          <p>
            فدقايق، كتب شنو خاصك وخلي الناس تعرف الشروط ديالك.
          </p>
        </div>
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="مطابقة سريعة" />
          </div>
          <h3>مطابقة سريعة</h3>
          <p>كاتلقى ناس قريبين ليك ومهرة باش يديرو خدمتك بسرعة.</p>
        </div>
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/1.png" alt="دفع آمن" />
          </div>
          <h3>دفع آمن</h3>
          <p>ما كتخلص حتى تكمل الخدمة و تكون راضي عليها.</p>
        </div>
      </div>
    </div>


    <!-- Advantages Section -->
    <div class="advantage">
      <div class="our-advantage">
        <p>علاش تختار منصتنا؟</p>
      </div>
      <div class="headline">
        <h2>علاش آلاف الناس كيعتمدوا علينا كل نهار؟</h2>
        <p>
          المنصة ديالنا كتوفر ليك طريقة سهلة وذكية باش تلقى أو تعرض خدمات في أي مدينة فالمغرب، بأمان وفعالية.
        </p>
      </div>

      <div class="advantage-cards">
        <div class="card1">
          <div class="images">
            <img
              src="./style/images/verification.png"
              alt="Icon التحقق من المستخدمين" />
          </div>
          <div class="info-card">
            <h3>مستخدمين متحقق منهم</h3>
            <p>
              كندير تحقق من الهوية باش نضمنو الأمان والثقة بين الناس اللي كيطلبو وكيعرضو الخدمات.
            </p>
          </div>
        </div>

        <div class="card2">
          <div class="images">
            <img src="./style/images/$.svg" alt="Icon الأسعار المناسبة" />
          </div>
          <div class="info-card">
            <h3>ثمن مناسب للجميع</h3>
            <p>
              تفاوض مباشرة مع الشخص اللي غادي يدير الخدمة، واختار العرض اللي يريحك.
            </p>
          </div>
        </div>

        <div class="card3">
          <div class="images">
            <img src="./style/images/f.svg" alt="Icon المجتمع" />
          </div>
          <div class="info-card">
            <h3>مجتمع متعاون</h3>
            <p>
              تواصل مع الناس فمدينتك، شارك التجارب ديالك، وخدم يد في يد مع محترفين.
            </p>
          </div>
        </div>

        <div class="card4">
          <div class="images">
            <img
              src="./style/images/contact.svg"
              alt="Icon الفرص الجديدة" />
          </div>
          <div class="info-card">
            <h3>فرص جديدة للخدمة</h3>
            <p>
              سواء كنت كتشوف على خدمة ولا عندك مهارة، المنصة كتفتح ليك بيبان جديدة.
            </p>
          </div>
        </div>

        <div class="card5">
          <div class="images">
            <img
              src="./style/images/location.svg"
              alt="Icon الخدمة في أي بلاصة" />
          </div>
          <div class="info-card">
            <h3>خدمة فين ما كنتي</h3>
            <p>
              تقدر تطلب أو تعرض خدمة فمدينة أخرى، بلا ما تكون حاضر، وكلشي كيتم عبر المنصة.
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
          الخدمات المتوفرة
        </button>
      </div>

      <div class="heading-section">
        <h2 class="main-heading">
          لقا المساعدة في أي خدمة، ف أي وقت! استكشف الخدمات الأكثر طلباً
        </h2>
        <p class="sub-heading">
          حنا عارفين أن الوقت ديالك ثمين، وهادشي علاش كنجمعو لك أفضل الخدمات بطريقة سهلة وآمنة باش توصل لحل سريع وفعّال.
        </p>
      </div>

      <div class="services-grid">
        <!-- Service Card 1 -->
        <div class="service-card">
          <div class="card-image">
            <img src="./style/images/Gradient+Image.png" alt="خدمات التنظيف" />
          </div>
          <div class="card-content">
            <h3 class="card-title">خدمات التنظيف</h3>
            <div class="price-container">
              <span class="current-price">ابتداءً من 100 درهم</span>
            </div>
            <p class="card-description">
              تنظيف شامل، ترتيب يومي، أو تنظيف عند الخروج من الشقة.
            </p>

            <div class="card-details">
              <div class="detail-item">
                <!-- Icon: Clock -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>6+ ساعات عمل</span>
              </div>

              <div class="detail-item">
                <!-- Icon: Bar Chart -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="20" x2="18" y2="10"></line>
                  <line x1="12" y1="20" x2="12" y2="4"></line>
                  <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>مبتدئ</span>
              </div>
            </div>

            <button class="browse-button">شوف الخدمات</button>
          </div>
        </div>

        <!-- نفس الهيكل للبطاقات الأخرى -->
        <!-- Service Card 2 -->
        <div class="service-card">
          <div class="card-image">
            <img src="./style/images/Gradient+Image (1).png" alt="التوصيل وقضاء الحاجيات" />
          </div>
          <div class="card-content">
            <h3 class="card-title">التوصيل وقضاء الحاجيات</h3>
            <div class="price-container">
              <span class="current-price">ابتداءً من 90 درهم</span>
            </div>
            <p class="card-description">
              جلب المشتريات، توصيل الطرود، أو أشياء منسية.
            </p>

            <div class="card-details">
              <div class="detail-item">
                <!-- Icon: Clock -->
                <svg xmlns="http://www.w3.org/2000/svg" ...>
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>15+ ساعة عمل</span>
              </div>

              <div class="detail-item">
                <!-- Icon: Bar Chart -->
                <svg xmlns="http://www.w3.org/2000/svg" ...>
                  <line x1="18" y1="20" x2="18" y2="10"></line>
                  <line x1="12" y1="20" x2="12" y2="4"></line>
                  <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>من مبتدئ حتى متوسط</span>
              </div>
            </div>

            <button class="browse-button">شوف الخدمات</button>
          </div>
        </div>

        <!-- Service Card 3 -->
        <div class="service-card">
          <div class="card-image">
            <img src="./style/images/Gradient+Image.png" alt="مساعدة في التنقل" />
          </div>
          <div class="card-content">
            <h3 class="card-title">مساعدة في التنقل</h3>
            <div class="price-container">
              <span class="current-price">ابتداءً من 200 درهم</span>
            </div>
            <p class="card-description">
              التغليف، الحمل، أو المساعدة في الانتقال الكامل.
            </p>

            <div class="card-details">
              <div class="detail-item">
                <!-- Icon: Clock -->
                <svg xmlns="http://www.w3.org/2000/svg" ...>
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>3+ ساعات عمل</span>
              </div>

              <div class="detail-item">
                <!-- Icon: Bar Chart -->
                <svg xmlns="http://www.w3.org/2000/svg" ...>
                  <line x1="18" y1="20" x2="18" y2="10"></line>
                  <line x1="12" y1="20" x2="12" y2="4"></line>
                  <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>مبتدئ</span>
              </div>
            </div>

            <button class="browse-button">شوف الخدمات</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Testimonials Section -->
    <!-- Testimonials Section -->
    <div class="testimonials-container">
      <!-- Header Section -->
      <div class="header">
        <div class="branding">
          <div class="logo">
          </div>
          <h1 class="counter">7.145<span>+</span></h1>
          <p class="subtitle">مستخدمين تّقو فينا</p>
        </div>

        <!-- User Avatars -->
        <div class="users">
          <div class="avatar-group">
            <div class="avatar">
              <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User 1" />
            </div>
            <div class="avatar">
              <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User 2" />
            </div>
            <div class="avatar">
              <img src="https://randomuser.me/api/portraits/men/86.jpg" alt="User 3" />
            </div>
            <div class="avatar">
              <img src="https://randomuser.me/api/portraits/women/63.jpg" alt="User 4" />
            </div>
            <div class="avatar">
              <img src="https://randomuser.me/api/portraits/men/54.jpg" alt="User 5" />
            </div>
          </div>
          <div class="users-text">
            أكثر من 11.000 مستخدم خدمو معنا بكل ثقة، كن واحد منهم!
          </div>
        </div>
      </div>

      <!-- Testimonial Grid -->
      <div class="testimonial-grid">
        <!-- Testimonial 1 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            تجربتي مع هاد المنصة كانت زوينة بزاف! لقيت ناس عندهم كفاءة ودارو لي المهام فبلاستي وأنا مرتاح.
            صراحة الخدمة ساهلة وتعاونات معايا بزاف فتنظيم الوقت.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Hossam Seddaui</h4>
                <p>عضو موثوق</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Hossam Seddaui" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 2 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            لقيت فرص كثيرة هنا، طلبت شي خدم فمدينة بعيدة، وجا واحد خدمني فنهار واحد. المنصة ساهلة فالتعامل وكتربط بين الناس بشكل مباشر.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Younes Zahiri</h4>
                <p>عضو موثوق</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/men/86.jpg" alt="Younes Zahiri" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 3 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            أول مرة نجرب نطلب شي خدمة عبر الإنترنت، ومتفاجئة قدّاش الخدمة كانت احترافية! الشخص اللي خدمني تواصل معايا، تفاهمنا، وكلشي داز مزيان.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Hajar Mendari</h4>
                <p>عضوة موثوقة</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Hajar Mendari" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 4 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            من بعد ما جربت المنصة، وليت كنستافد من خدمات بلا ما نتقلق على التنقل. تواصل سريع وتنفيذ دقيق فمدة قصيرة. كنصح بيها بزاف!
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Mohammed Arrousi</h4>
                <p>عضو موثوق</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/men/41.jpg" alt="Mohammed Arrousi" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 5 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            بغيت واحد يشد طرد من الدار ويصيفطو لي، ولقيت شخص فمدينتي تكلف بيها بسرعة. فكرة المنصة كتخدم بزاف ديال الناس وخدماتها مزيانة بزاف.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Sara El Mansouri</h4>
                <p>عضوة موثوقة</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/women/63.jpg" alt="Sara El Mansouri" />
              </div>
            </div>
          </div>
        </div>
        <!-- Testimonial 6 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            الخدمة اللي خديتها كانت أكثر من رائعة، جا عندي السيد حتى لباب الدار وخدمني بحرفية عالية.
            مابقيتش مضطر نطلب من صحابي شي حاجة، دابا عندي حل مضمون.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Abdelhak El Idrissi</h4>
                <p>عضو موثوق</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Abdelhak El Idrissi" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 7 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            استعملت هاد الخدمة باش نرسل وثائق من الرباط لمراكش، وكان التواصل سهل والشخص اللي خدمني كان محترم بزاف.
            تنشكر الفريق على المصداقية.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Salma Bennani</h4>
                <p>عضوة موثوقة</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/women/78.jpg" alt="Salma Bennani" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 8 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            خدمت كمنفذ ديال الخدمات على هاد الموقع، ووليت كنربح مدخول محترم كل سيمانة. المنصة كتعاون حتى الناس اللي بغاو يخدمو.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Khalid Taoussi</h4>
                <p>عضو منفذ للخدمات</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/men/23.jpg" alt="Khalid Taoussi" />
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 9 -->
        <div class="testimonial-card">
          <p class="testimonial-text">
            كان عندي موعد مهم وماقدرتش نحضر، طلبت شي حد يمشي بلاصتي يوصل الوثائق، وكلشي داز بخير.
            المنصة كتوفر راحة البال فعلاً.
          </p>
          <div>
            <div class="rating">
              <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
            </div>
            <div class="user-info">
              <div class="user-details">
                <h4>Ilham Rami</h4>
                <p>عضوة موثوقة</p>
              </div>
              <div class="user-avatar">
                <img src="https://randomuser.me/api/portraits/women/91.jpg" alt="Ilham Rami" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- How It Works Section -->
    <div class="how-it-works">
      <div class="header">
        <div class="pill-button">كيفاش خدام الموقع</div>
        <h1 class="title">
          ساهل تطلب الخدمة ديالك<br />
          وتلقى اللي ينجزها ليك بسرعة
        </h1>
        <p class="subtitle">
          غير دير طلب الخدمة ديالك فدقائق،<br />
          والناس القريبين ليك اللي عندهم المهارات غادي يشوفوه ويقبلوه وينفذوه.
        </p>
      </div>

      <div class="cards-container">
        <!-- Card 1 -->
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/Explore Illustration.png" alt="Create Task" />
          </div>
          <div class="card-info">
            <h3 class="card-title">دير طلب الخدمة ديالك</h3>
            <p class="card-text">
              عَبّي التفاصيل ديال الخدمة اللي محتاجها بسهولة وبسرعة.
            </p>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/Learning Illustration.png" alt="Find Provider" />
          </div>
          <div class="card-info">
            <h3 class="card-title">الناس يشوفو طلبك</h3>
            <p class="card-text">
              المهنيين والقريبين ليك غادي يشوفو طلبك ويعرضو عليك الخدمة ديالهم.
            </p>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="card">
          <div class="card-icon">
            <img src="./style/images/Grow Illustration.png" alt="Get Task Done" />
          </div>
          <div class="card-info">
            <h3 class="card-title">خدمتك تدار بسرعة</h3>
            <p class="card-text">
              تواصل، تفاوض، وخليك مرتاح حتى يكتمل تنفيذ الخدمة ديالك بنجاح.
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
          <span class="button-text">شنو كيميزنا</span>
        </button>

        <h1>شوف كيفاش كنتميزو<br /> وسط المنافسة العالمية</h1>

        <p>
          اكتشف الميزات المبتكرة والطريقة الجماعية ديالنا اللي كتخلّي منصتنا فوق الباقي.
        </p>
      </div>

      <div class="earth-container">
        <img
          src="style/images/Global Map Illustrations.svg"
          alt="خريطة العالم مع الضوء على الجانب الأيسر" />
      </div>

    </div>
    <div class="Quistion">
      <!-- Notification banner -->
      <div style="text-align: center; margin-bottom: 2rem">
        <div class="notification">
          سلام! محتاج مساعدة مرة وحدة؟ ولا عندك شي سؤال عام؟ 👋
        </div>
      </div>

      <!-- Heading -->
      <h1 class="heading">
        <span>عندك شي أسئلة</span>
        <span class="light">فبالك؟</span>
      </h1>

      <!-- Subheading -->
      <p class="subheading">
        عمر الفورم وخلي نهضرو على المسار ديالك، الأهداف ديالك، أو غير سول على اللي محتاج.
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
              <label for="firstName">السمية اللولة</label>
              <input type="text" id="firstName" name="firstName" />
            </div>
            <div class="form-col">
              <label for="lastName">السمية الثانية</label>
              <input type="text" id="lastName" name="lastName" />
            </div>
          </div>

          <!-- Contact fields -->
          <div class="form-row">
            <div class="form-col">
              <label for="email">الإيميل</label>
              <input type="email" id="email" name="email" />
            </div>
            <div class="form-col">
              <label for="phone">النمرة ديال التليفون</label>
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
            <label for="message">الرسالة ديالك</label>
            <textarea id="message" name="message"></textarea>
          </div>

          <!-- Submit button -->
          <button type="submit" class="submit-btn">صيفط الرسالة</button>
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
            الأسئلة المتكررة
          </button>
        </div>

        <h2 class="faq-title">جاوبنا على الأسئلة اللي كتجي بزاف</h2>
        <p class="faq-subtitle">
          تلقى عندنا شروحات واضحة على الأسئلة اللي كيتسولوها الناس على الموقع، الخدمات، وكيفاش نعاونك تكمل المهام ديالك.
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
                <h3>كيفاش كيخدم الموقع؟</h3>
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
                الموقع كيربط الناس اللي محتاجين شي مهام يتدارو مع ناس عندهم مهارات جاهزين يعاونوهم. غير عَمّر المهمة ديالك، حدّد الميزانية ديالك، واختار من بين الناس اللي غادي يطلبو يخدموها. من بعد ما تكمل المهمة وتكون راضي، كتخلص.
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
                <h3>شحال كتخلص باش تستعمل الخدمة؟</h3>
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
                نشر المهمة فالموقع مجاني. كنطبقو عمولة صغيرة (من 5 حتى 15٪ على حسب نوع المهمة) غير منين المهمة تكمل ونتا مستعد تخلص. هاد العمولة كتعاوننا نحافظو على الموقع ونضمنو الجودة.
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
                <h3>هل الدفع ديالي آمن؟</h3>
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
                بطبيعة الحال. كنستعملو تشفير حديث وأنظمة دفع آمنة. الفلوس كتكون محمية حتى تأكد أن المهمة تسالات وانت راضي، باش نحميوك ونحميو اللي خدم معاك.
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
                <h3>كيفاش كنحققو من هوية الناس اللي كيخدمو؟</h3>
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
                كنطلبو من الناس اللي بغاو يخدمو يديرو تحقق من الهوية (بطاقة وطنية أو وثيقة رسمية)، وكنراجعو التقييمات ديالهم والتجارب السابقة باش نضمنو الجودة والثقة.
              </p>
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