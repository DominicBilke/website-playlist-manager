<?php
require_once 'script/languages.php';
require_once 'script/language_utils.php';
?>
<footer class="footer bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center mb-4">
                    <i class="fas fa-music text-3xl text-purple-400 mr-3"></i>
                    <h3 class="text-xl font-bold">Playlist Manager</h3>
                </div>
                <p class="text-gray-300 mb-4 leading-relaxed">
                    <?php echo $lang->getCurrentLanguage() === 'de' 
                        ? 'Die fortschrittlichste Lösung für Musik-Streaming-Automatisierung und Playlist-Management. Unterstützt alle großen Musikplattformen mit intelligenter Planung und Analytics.'
                        : 'The most advanced solution for music streaming automation and playlist management. Supports all major music platforms with intelligent scheduling and analytics.'; ?>
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4"><?php echo $lang->get('quick_links'); ?></h4>
                <ul class="space-y-2">
                    <li>
                        <a href="index.php" class="footer-link">
                            <i class="fas fa-home mr-2"></i><?php echo $lang->get('home'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="account.php" class="footer-link">
                            <i class="fas fa-tachometer-alt mr-2"></i><?php echo $lang->get('dashboard'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="spotify_play.php" class="footer-link">
                            <i class="fab fa-spotify mr-2"></i><?php echo $lang->get('spotify'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="applemusic_play.php" class="footer-link">
                            <i class="fab fa-apple mr-2"></i><?php echo $lang->get('apple_music'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="youtube_play.php" class="footer-link">
                            <i class="fab fa-youtube mr-2"></i><?php echo $lang->get('youtube_music'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="amazon_play.php" class="footer-link">
                            <i class="fab fa-amazon mr-2"></i><?php echo $lang->get('amazon_music'); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support & Legal -->
            <div>
                <h4 class="text-lg font-semibold mb-4"><?php echo $lang->get('support'); ?></h4>
                <ul class="space-y-2">
                    <li>
                        <a href="help.php" class="footer-link">
                            <i class="fas fa-question-circle mr-2"></i><?php echo $lang->get('help_center'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="contact.php" class="footer-link">
                            <i class="fas fa-envelope mr-2"></i><?php echo $lang->get('contact'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="faq.php" class="footer-link">
                            <i class="fas fa-info-circle mr-2"></i><?php echo $lang->get('faq'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="privacy.php" class="footer-link">
                            <i class="fas fa-shield-alt mr-2"></i><?php echo $lang->get('privacy_policy'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="impressum.php" class="footer-link">
                            <i class="fas fa-gavel mr-2"></i><?php echo $lang->get('impressum_title'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="terms.php" class="footer-link">
                            <i class="fas fa-file-contract mr-2"></i><?php echo $lang->get('terms_of_service'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="border-t border-gray-800 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    <p>&copy; <?php echo date('Y'); ?> Playlist Manager. <?php echo $lang->getCurrentLanguage() === 'de' ? 'Alle Rechte vorbehalten.' : 'All rights reserved.'; ?></p>
                </div>
                
                <div class="flex items-center space-x-6">
                    <!-- Language Switcher -->
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-400 text-sm"><?php echo $lang->get('language'); ?>:</span>
                        <a href="<?php echo buildLanguageUrl('en'); ?>" class="language-link <?php echo $lang->getCurrentLanguage() === 'en' ? 'active' : ''; ?>">
                            EN
                        </a>
                        <span class="text-gray-600">|</span>
                        <a href="<?php echo buildLanguageUrl('de'); ?>" class="language-link <?php echo $lang->getCurrentLanguage() === 'de' ? 'active' : ''; ?>">
                            DE
                        </a>
                    </div>
                    
                    <!-- Back to Top -->
                    <button id="back-to-top" class="back-to-top-btn" aria-label="<?php echo $lang->get('back_to_top'); ?>">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer-specific styles */
.footer {
    margin-top: auto;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 50%;
    transition: all var(--transition-fast);
    text-decoration: none;
}

.social-link:hover {
    background-color: var(--primary-600);
    transform: translateY(-2px);
}

.footer-link {
    display: flex;
    align-items: center;
    color: var(--gray-300);
    text-decoration: none;
    transition: color var(--transition-fast);
    padding: 0.25rem 0;
}

.footer-link:hover {
    color: var(--primary-400);
}

.language-link {
    color: var(--gray-400);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
}

.language-link:hover,
.language-link.active {
    color: var(--primary-400);
}

.back-to-top-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    background-color: var(--primary-600);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: all var(--transition-fast);
    opacity: 0;
    visibility: hidden;
}

.back-to-top-btn.visible {
    opacity: 1;
    visibility: visible;
}

.back-to-top-btn:hover {
    background-color: var(--primary-700);
    transform: translateY(-2px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .footer .grid {
        gap: 2rem;
    }
    
    .footer .col-span-1.md\:col-span-2 {
        grid-column: span 1;
    }
}
</style>

<script>
// Footer functionality
document.addEventListener('DOMContentLoaded', function() {
    // Back to top button
    const backToTopBtn = document.getElementById('back-to-top');
    
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        
        // Smooth scroll to top
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Add loading animation to social links
    const socialLinks = document.querySelectorAll('.social-link');
    socialLinks.forEach((link, index) => {
        link.style.animationDelay = `${index * 100}ms`;
        link.classList.add('animate-fade-in');
    });
    
    // Add loading animation to footer links
    const footerLinks = document.querySelectorAll('.footer-link');
    footerLinks.forEach((link, index) => {
        link.style.animationDelay = `${index * 50}ms`;
        link.classList.add('animate-slide-in');
    });
});
</script> 