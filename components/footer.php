<?php
require_once 'script/languages.php';
require_once 'script/language_utils.php';
?>
<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="container">
        <div class="py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-music text-primary text-2xl mr-3"></i>
                        <span class="text-xl font-bold text-gray-900">Playlist Manager</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        <?php echo $lang->getCurrentLanguage() === 'de' 
                            ? 'Ihre zentrale Plattform für die Verwaltung und Wiedergabe von Musik-Playlists über alle beliebten Streaming-Dienste.'
                            : 'Your central platform for managing and playing music playlists across all popular streaming services.'; ?>
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4"><?php echo $lang->get('quick_links'); ?></h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="index.php" class="text-gray-600 hover:text-primary transition-colors">
                                <?php echo $lang->get('home'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="account.php" class="text-gray-600 hover:text-primary transition-colors">
                                <?php echo $lang->get('dashboard'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="player.php" class="text-gray-600 hover:text-primary transition-colors">
                                <?php echo $lang->get('music_player'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="spotify_play.php" class="text-gray-600 hover:text-primary transition-colors">
                                <?php echo $lang->get('spotify'); ?>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support & Legal -->
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4"><?php echo $lang->get('support'); ?></h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="help.php" class="text-gray-600 hover:text-primary transition-colors">
                                <?php echo $lang->get('help_center'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="contact.php" class="text-gray-600 hover:text-primary transition-colors">
                                <?php echo $lang->get('contact'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="faq.php" class="text-gray-600 hover:text-primary transition-colors">
                                <?php echo $lang->get('faq'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="privacy.php" class="text-gray-600 hover:text-primary transition-colors">
                                <?php echo $lang->get('privacy_policy'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="border-t border-gray-200 pt-6 mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-gray-600 text-sm mb-4 md:mb-0">
                        <?php echo $lang->get('copyright'); ?>
                    </div>
                    <div class="flex items-center space-x-6">
                        <a href="privacy.php" class="text-gray-600 hover:text-primary text-sm transition-colors">
                            <?php echo $lang->get('privacy_policy'); ?>
                        </a>
                        <a href="terms.php" class="text-gray-600 hover:text-primary text-sm transition-colors">
                            <?php echo $lang->get('terms_of_service'); ?>
                        </a>
                        <a href="datenschutz.php" class="text-gray-600 hover:text-primary text-sm transition-colors">
                            <?php echo $lang->get('legal_notice'); ?>
                        </a>
                    </div>
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