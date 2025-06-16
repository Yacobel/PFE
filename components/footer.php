<?php
// Continue output buffering if needed
if (!ob_get_level()) {
    ob_start();
}
require_once __DIR__ . '/../config/languages.php';
?>
<!-- Footer -->
<link rel="stylesheet" href="./style/footer.css">
<footer>
    <div class="footer-content">
        <div class="footer-columns">
            <!-- Left Column -->
            <div class="footer-column">
                <div class="footer-logo">
                    <a href="/">ServeMatch</a>
                </div>
                <p class="footer-description">
                    <?php echo __("footer_description"); ?>
                </p>
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="contact-label"><?php echo __("have_question"); ?></p>
                            <p class="contact-value">+212667100710</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <div>
                            <p class="contact-label"><?php echo __("contact_us_at"); ?></p>
                            <p class="contact-value">get@task.ma</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Newsletter -->
            <div class="footer-column">
                <h3 class="footer-heading"><?php echo __("newsletter"); ?></h3>
                <p class="footer-description">
                    <?php echo __("newsletter_description"); ?>
                </p>
                <div class="newsletter-form">
                    <input type="email" placeholder="<?php echo __("enter_email"); ?>" class="newsletter-input">
                    <button class="btn btn-primary newsletter-btn"><?php echo __("submit"); ?></button>
                </div>
            </div>
        </div>

        <!-- Bottom Links -->
        <div class="footer-bottom">
            <div class="footer-nav">
                <a href="/about" class="footer-link"><?php echo __("about_us"); ?></a>
                <a href="/contact" class="footer-link"><?php echo __("contact_us"); ?></a>
                <a href="/terms" class="footer-link"><?php echo __("terms_of_service"); ?></a>
                <a href="/privacy" class="footer-link"><?php echo __("privacy_policy"); ?></a>
            </div>

            <div class="footer-end">
                <p class="copyright"><?php echo __("copyright"); ?></p>

            </div>
        </div>
    </div>

    <!-- Test Mode Badge -->

</footer>