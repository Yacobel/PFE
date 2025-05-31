<?php
// Continue output buffering if needed
if (!ob_get_level()) {
    ob_start();
}
?>
<!-- Footer -->
<link rel="stylesheet" href="./style/footer.css">
<footer>
    <div class="footer-content">
        <div class="footer-columns">
            <!-- Left Column -->
            <div class="footer-column">
                <div class="footer-logo">
                    <a href="/">skill.</a>
                </div>
                <p class="footer-description">
                    Morocco's first platform by creators, for creators. We teach the strategies that turn your passion into profit through expert courses and growth tactics.
                </p>
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="contact-label">Have a question?</p>
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
                            <p class="contact-label">Contact us at</p>
                            <p class="contact-value">get@skill.ma</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Newsletter -->
            <div class="footer-column">
                <h3 class="footer-heading">Newsletter</h3>
                <p class="footer-description">
                    Be the first one to know about discounts, offers and events weekly in your mailbox.
                    <br>
                    Unsubscribe whenever you like with one click.
                </p>
                <div class="newsletter-form">
                    <input type="email" placeholder="Enter your email" class="newsletter-input">
                    <button class="btn btn-primary newsletter-btn">Submit</button>
                </div>
            </div>
        </div>

        <!-- Bottom Links - Modified to be in one line -->
        <div class="footer-bottom">
            <!-- All links in one line -->
            <div class="footer-nav">
                <a href="/about" class="footer-link">About Us</a>
                <a href="/programs" class="footer-link">Programs</a>
                <a href="/blog" class="footer-link">Blog</a>
                <a href="/creators" class="footer-link">Creators</a>
                <a href="/affiliate" class="footer-link">Affiliate</a>
                <a href="/contact" class="footer-link">Contact Us</a>
                <a href="/terms" class="footer-link">Terms Of Service</a>
                <a href="/privacy" class="footer-link">Privacy Policy</a>
            </div>

            <div class="footer-end">
                <p class="copyright">© 2024, All Rights Reserved</p>

                <!-- Social icons at the end -->
                <div class="social-links">
                    <a href="#" class="social-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                            <rect x="2" y="9" width="4" height="12"></rect>
                            <circle cx="4" cy="4" r="2"></circle>
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                        </svg>
                    </a>
                </div>

                <div class="language-selector">
                    <button class="language-btn active">En</button>
                    <button class="language-btn">Ar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Mode Badge -->
    <div class="test-mode-badge">
        <div class="test-mode-label">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            Test Mode
        </div>
        <div class="test-mode-count">5</div>
    </div>
</footer>