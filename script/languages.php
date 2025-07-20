<?php
/**
 * Multilingual Support System
 * Supports German (de) and English (en)
 */

class LanguageManager {
    private $currentLanguage = 'en';
    private $translations = [];
    private $fallbackLanguage = 'en';
    
    public function __construct($language = null) {
        // Set language from session or parameter
        if ($language) {
            $this->currentLanguage = $language;
        } elseif (isset($_SESSION['language'])) {
            $this->currentLanguage = $_SESSION['language'];
        } elseif (isset($_GET['lang'])) {
            $this->currentLanguage = $_GET['lang'];
            $_SESSION['language'] = $this->currentLanguage;
        } else {
            // Detect language from browser
            $this->currentLanguage = $this->detectBrowserLanguage();
            $_SESSION['language'] = $this->currentLanguage;
        }
        
        // Load translations
        $this->loadTranslations();
    }
    
    private function detectBrowserLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($browserLanguages as $lang) {
                $lang = trim(explode(';', $lang)[0]);
                if (in_array($lang, ['de', 'de-DE', 'de-AT', 'de-CH'])) {
                    return 'de';
                } elseif (in_array($lang, ['en', 'en-US', 'en-GB', 'en-CA'])) {
                    return 'en';
                }
            }
        }
        return $this->fallbackLanguage;
    }
    
    private function loadTranslations() {
        $this->translations = [
            'en' => [
                // Navigation
                'dashboard' => 'Dashboard',
                'spotify' => 'Spotify',
                'apple_music' => 'Apple Music',
                'youtube_music' => 'YouTube Music',
                'amazon_music' => 'Amazon Music',
                'settings' => 'Settings',
                'sign_out' => 'Sign out',
                'team' => 'Team',
                
                // Authentication
                'login' => 'Login',
                'signup' => 'Sign Up',
                'email' => 'Email',
                'password' => 'Password',
                'confirm_password' => 'Confirm Password',
                'forgot_password' => 'Forgot Password?',
                'remember_me' => 'Remember me',
                'login_button' => 'Sign In',
                'signup_button' => 'Create Account',
                'already_have_account' => 'Already have an account?',
                'dont_have_account' => "Don't have an account?",
                
                // Common
                'save' => 'Save',
                'cancel' => 'Cancel',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'back' => 'Back',
                'next' => 'Next',
                'previous' => 'Previous',
                'loading' => 'Loading...',
                'error' => 'Error',
                'success' => 'Success',
                'warning' => 'Warning',
                'info' => 'Information',
                'close' => 'Close',
                'yes' => 'Yes',
                'no' => 'No',
                'ok' => 'OK',
                
                // Player Pages
                'player' => 'Player',
                'automated_playback' => 'Automated playlist playback with smart scheduling',
                'manual_playback' => 'Manual playlist playback with smart scheduling',
                'current_status' => 'Current Status',
                'playing_time' => 'Playing Time',
                'active_days' => 'Active Days',
                'live' => 'Live',
                'ready' => 'Ready',
                'playing' => 'Playing',
                'paused' => 'Paused',
                'initializing' => 'Initializing...',
                'reloading' => 'Reloading...',
                'error_occurred' => 'An error occurred',
                
                // Algorithm Information
                'automation_algorithm' => 'Automation Algorithm',
                'playing_time_defined' => 'Playing time defined in user account',
                'random_play_time' => 'Random play time: 61-600 seconds',
                'random_pause_time' => 'Random pause time: 0-600 seconds',
                'automatic_playback' => 'Automatic playback based on settings',
                'shuffle_repeat' => 'Shuffle and repeat all songs',
                'reload_playlist' => 'Reload playlist to remove preview mode',
                'requires_paid_account' => 'Requires paid Apple Music account',
                'login_paid_account' => 'Login with paid account to remove ads',
                'manual_control_required' => 'Manual play control required',
                'manual_pause_required' => 'Manual pause control required',
                'api_limitations' => 'Amazon Music API limitations',
                'opens_new_window' => 'Opens in new window/tab',
                'requires_unlimited' => 'Requires Amazon Music Unlimited',
                
                // Control Panel
                'control_panel' => 'Control Panel',
                'auto_play' => 'Auto Play',
                'automated_scheduling' => 'Automated scheduling',
                'shuffle' => 'Shuffle',
                'random_track_order' => 'Random track order',
                'repeat' => 'Repeat',
                'loop_all_tracks' => 'Loop all tracks',
                'manual_control' => 'Manual Control',
                'user_controlled_playback' => 'User-controlled playback',
                'statistics' => 'Statistics',
                'listening_time_tracking' => 'Listening time tracking',
                'scheduling' => 'Scheduling',
                'time_window_monitoring' => 'Time window monitoring',
                
                // Playlist Management
                'no_playlist_selected' => 'No Playlist Selected',
                'please_select_playlist' => 'Please select a playlist in your account settings.',
                'configure_playlist' => 'Configure Playlist',
                'reload_playlist_button' => 'Reload Playlist',
                'new_login' => 'New Login',
                'open_playlist' => 'Open Playlist',
                'start_playlist' => 'Start Playlist',
                
                // Amazon Music Specific
                'amazon_music_playlist' => 'Amazon Music Playlist',
                'click_to_open' => 'Click the button below to open your Amazon Music playlist in a new window.',
                'open_amazon_playlist' => 'Open Amazon Music Playlist',
                'important_note' => 'Important Note:',
                'amazon_limitations_text' => 'Due to Amazon Music API limitations, automatic playback control is not available. You\'ll need to manually control play/pause in the Amazon Music window. The system will still log your listening statistics during your scheduled time windows.',
                'amazon_limitations_title' => 'Amazon Music Limitations',
                'amazon_limitations_list' => [
                    'Amazon Music does not provide a public web API for automated playback control',
                    'Playback must be manually controlled in the Amazon Music window',
                    'The system can still track your listening statistics during scheduled time windows',
                    'This limitation is due to Amazon\'s API restrictions, not our system'
                ],
                
                // Status Messages
                'playlist_opened' => 'Playlist Opened',
                'allow_popups' => 'Please allow pop-ups for this site to open Amazon Music.',
                'statistics_logged' => 'Statistics logged',
                'failed_to_log' => 'Failed to log statistics',
                'paused_before_time' => 'Paused (before time)',
                'paused_after_time' => 'Paused (after time)',
                'paused_outside_schedule' => 'Paused (outside schedule)',
                'playing_seconds' => 'Playing ({seconds}s)',
                'paused_seconds' => 'Paused ({seconds}s)',
                
                // Time and Days
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
                'not_set' => 'Not set',
                
                // Account Management
                'account_settings' => 'Account Settings',
                'profile' => 'Profile',
                'personal_info' => 'Personal Information',
                'change_password' => 'Change Password',
                'current_password' => 'Current Password',
                'new_password' => 'New Password',
                'confirm_new_password' => 'Confirm New Password',
                'update_profile' => 'Update Profile',
                'account_deleted' => 'Account Deleted',
                'delete_account' => 'Delete Account',
                'delete_account_warning' => 'This action cannot be undone. All your data will be permanently deleted.',
                
                // Platform Management
                'connect_spotify' => 'Connect Spotify',
                'connect_apple_music' => 'Connect Apple Music',
                'connect_youtube' => 'Connect YouTube',
                'connect_amazon' => 'Connect Amazon Music',
                'disconnect' => 'Disconnect',
                'connected' => 'Connected',
                'not_connected' => 'Not Connected',
                
                // Legal Pages
                'impressum_title' => 'Imprint',
                'impressum_description' => 'Legal information and company details for Playlist Manager',
                'impressum_subtitle' => 'Legal information and company details',
                'impressum_legal_info' => 'Legal Information',
                'impressum_company' => 'Company',
                'impressum_contact' => 'Contact Information',
                'impressum_management' => 'Management',
                'impressum_managing_director' => 'Managing Director',
                'impressum_managing_director_desc' => 'Responsible for business operations',
                'impressum_supervisory_board' => 'Supervisory Board',
                'impressum_supervisory_board_desc' => 'Oversees company management',
                'impressum_registration' => 'Registration Information',
                'impressum_court' => 'Commercial Register Court',
                'impressum_registration_number' => 'Registration Number',
                'impressum_tax_id' => 'Tax ID',
                'impressum_vat_id' => 'VAT ID',
                'impressum_professional_info' => 'Professional Information',
                'impressum_professional_title' => 'Professional Title',
                'impressum_professional_title_desc' => 'Software development and music technology services',
                'impressum_professional_authority' => 'Supervisory Authority',
                'impressum_professional_authority_desc' => 'Local Chamber of Commerce',
                'impressum_professional_regulation' => 'Professional Regulations',
                'impressum_professional_regulation_desc' => 'Subject to German commercial law',
                'impressum_disclaimer' => 'Disclaimer',
                'impressum_disclaimer_content' => 'The information provided on this website is for general informational purposes only. While we strive to keep the information up to date and correct, we make no representations or warranties of any kind about the completeness, accuracy, reliability, suitability, or availability of the information, products, services, or related graphics contained on the website.',
                'impressum_liability' => 'Liability',
                'impressum_liability_content' => 'In no event will we be liable for any loss or damage including without limitation, indirect or consequential loss or damage, arising from loss of data or profits arising out of, or in connection with, the use of this website.',
                
                // Privacy Policy
                'privacy_title' => 'Privacy Policy',
                'privacy_description' => 'How we collect, use, and protect your personal data',
                'privacy_subtitle' => 'How we collect, use, and protect your personal data',
                'privacy_last_updated' => 'Last updated',
                'privacy_introduction' => 'Introduction',
                'privacy_introduction_content' => 'This Privacy Policy explains how Playlist Manager GmbH ("we", "our", or "us") collects, uses, and protects your personal information when you use our playlist management service. We are committed to protecting your privacy and ensuring the security of your personal data.',
                'privacy_controller' => 'Data Controller',
                'privacy_data_collection' => 'Data Collection',
                'privacy_personal_data' => 'Personal Data We Collect',
                'privacy_personal_data_desc' => 'We collect the following types of personal data:',
                'privacy_data_name' => 'Name and contact information',
                'privacy_data_email' => 'Email address',
                'privacy_data_usage' => 'Usage data and preferences',
                'privacy_data_playlists' => 'Playlist information and music preferences',
                'privacy_data_preferences' => 'Account settings and preferences',
                'privacy_automatically_collected' => 'Automatically Collected Data',
                'privacy_automatically_collected_desc' => 'We automatically collect the following data:',
                'privacy_data_ip' => 'IP address and device information',
                'privacy_data_browser' => 'Browser type and version',
                'privacy_data_device' => 'Device type and operating system',
                'privacy_data_cookies' => 'Cookies and usage analytics',
                'privacy_purpose' => 'Purpose of Data Processing',
                'privacy_purpose_account' => 'Account Management',
                'privacy_purpose_account_desc' => 'To create and manage your user account',
                'privacy_purpose_playlists' => 'Playlist Management',
                'privacy_purpose_playlists_desc' => 'To manage and sync your playlists across platforms',
                'privacy_purpose_analytics' => 'Analytics & Improvement',
                'privacy_purpose_analytics_desc' => 'To improve our service and user experience',
                'privacy_purpose_security' => 'Security & Fraud Prevention',
                'privacy_purpose_security_desc' => 'To protect against fraud and ensure security',
                'privacy_legal_basis' => 'Legal Basis for Processing',
                'privacy_consent' => 'Consent',
                'privacy_consent_desc' => 'You have given clear consent for us to process your personal data for specific purposes.',
                'privacy_contract' => 'Contract Performance',
                'privacy_contract_desc' => 'Processing is necessary for the performance of a contract with you.',
                'privacy_legitimate_interest' => 'Legitimate Interest',
                'privacy_legitimate_interest_desc' => 'Processing is necessary for our legitimate interests in providing and improving our service.',
                'privacy_data_sharing' => 'Data Sharing',
                'privacy_third_parties' => 'Third-Party Services',
                'privacy_third_parties_desc' => 'We integrate with the following music platforms:',
                'privacy_spotify_desc' => 'For Spotify playlist management and playback',
                'privacy_apple_desc' => 'For Apple Music playlist management and playback',
                'privacy_youtube_desc' => 'For YouTube Music playlist management and playback',
                'privacy_amazon_desc' => 'For Amazon Music playlist management and playback',
                'privacy_no_sale' => 'No Data Sale',
                'privacy_no_sale_desc' => 'We do not sell, trade, or otherwise transfer your personal data to third parties for commercial purposes.',
                'privacy_data_retention' => 'Data Retention',
                'privacy_account_data' => 'Account Data',
                'privacy_account_data_retention' => 'Retained until account deletion or 3 years of inactivity',
                'privacy_usage_data' => 'Usage Data',
                'privacy_usage_data_retention' => 'Retained for 2 years for analytics and improvement',
                'privacy_logs' => 'Server Logs',
                'privacy_logs_retention' => 'Retained for 90 days for security and troubleshooting',
                'privacy_cookies' => 'Cookies',
                'privacy_cookies_retention' => 'Session cookies deleted when browser closes, persistent cookies for 1 year',
                'privacy_your_rights' => 'Your Rights',
                'privacy_right_access' => 'Right of Access',
                'privacy_right_access_desc' => 'You have the right to access your personal data',
                'privacy_right_rectification' => 'Right of Rectification',
                'privacy_right_rectification_desc' => 'You have the right to correct inaccurate data',
                'privacy_right_erasure' => 'Right of Erasure',
                'privacy_right_erasure_desc' => 'You have the right to delete your personal data',
                'privacy_right_portability' => 'Right of Portability',
                'privacy_right_portability_desc' => 'You have the right to receive your data in a portable format',
                'privacy_contact_rights' => 'Exercising Your Rights',
                'privacy_contact_rights_desc' => 'To exercise any of these rights, please contact us:',
                'privacy_cookies_title' => 'Cookies',
                'privacy_cookies_desc' => 'We use cookies to enhance your experience on our website:',
                'privacy_cookies_essential' => 'Essential Cookies',
                'privacy_cookies_essential_desc' => 'Required for basic website functionality',
                'privacy_cookies_functional' => 'Functional Cookies',
                'privacy_cookies_functional_desc' => 'Enhance user experience and remember preferences',
                'privacy_cookies_analytics' => 'Analytics Cookies',
                'privacy_cookies_analytics_desc' => 'Help us understand how visitors use our website',
                'privacy_contact_title' => 'Contact Information',
                'privacy_contact_desc' => 'If you have any questions about this Privacy Policy or our data practices, please contact us:',
                'privacy_data_protection_officer' => 'Data Protection Officer',
                'privacy_supervisory_authority' => 'Supervisory Authority',
                'select_playlist' => 'Select Playlist',
                'current_playlist' => 'Current Playlist',
                'no_playlist' => 'No Playlist',
                
                // Error Messages
                'unauthorized' => 'Unauthorized',
                'database_error' => 'Database error',
                'configuration_error' => 'Configuration error',
                'api_error' => 'API error',
                'network_error' => 'Network error',
                'invalid_credentials' => 'Invalid credentials',
                'account_not_found' => 'Account not found',
                'playlist_not_found' => 'Playlist not found',
                
                // Success Messages
                'login_successful' => 'Login successful',
                'signup_successful' => 'Account created successfully',
                'settings_updated' => 'Settings updated successfully',
                'playlist_connected' => 'Playlist connected successfully',
                'playlist_disconnected' => 'Playlist disconnected successfully',
                
                // Language
                'language' => 'Language',
                'german' => 'German',
                'english' => 'English',
                'change_language' => 'Change Language',
                
                // Footer
                'copyright' => '© 2024 Playlist Manager. All rights reserved.',
                'privacy_policy' => 'Privacy Policy',
                'terms_of_service' => 'Terms of Service',
                'contact_support' => 'Contact Support',
                'quick_links' => 'Quick Links',
                'home' => 'Home',
                'support' => 'Support & Legal',
                'help_center' => 'Help Center',
                'contact' => 'Contact',
                'faq' => 'FAQ',
                'legal_notice' => 'Legal Notice',
                'back_to_top' => 'Back to top'
            ],
            
            'de' => [
                // Navigation
                'dashboard' => 'Dashboard',
                'spotify' => 'Spotify',
                'apple_music' => 'Apple Music',
                'youtube_music' => 'YouTube Music',
                'amazon_music' => 'Amazon Music',
                'settings' => 'Einstellungen',
                'sign_out' => 'Abmelden',
                'team' => 'Team',
                
                // Authentication
                'login' => 'Anmelden',
                'signup' => 'Registrieren',
                'email' => 'E-Mail',
                'password' => 'Passwort',
                'confirm_password' => 'Passwort bestätigen',
                'forgot_password' => 'Passwort vergessen?',
                'remember_me' => 'Angemeldet bleiben',
                'login_button' => 'Anmelden',
                'signup_button' => 'Konto erstellen',
                'already_have_account' => 'Haben Sie bereits ein Konto?',
                'dont_have_account' => 'Haben Sie noch kein Konto?',
                
                // Common
                'save' => 'Speichern',
                'cancel' => 'Abbrechen',
                'edit' => 'Bearbeiten',
                'delete' => 'Löschen',
                'back' => 'Zurück',
                'next' => 'Weiter',
                'previous' => 'Zurück',
                'loading' => 'Lädt...',
                'error' => 'Fehler',
                'success' => 'Erfolg',
                'warning' => 'Warnung',
                'info' => 'Information',
                'close' => 'Schließen',
                'yes' => 'Ja',
                'no' => 'Nein',
                'ok' => 'OK',
                
                // Player Pages
                'player' => 'Player',
                'automated_playback' => 'Automatisierte Wiedergabe mit intelligenter Planung',
                'manual_playback' => 'Manuelle Wiedergabe mit intelligenter Planung',
                'current_status' => 'Aktueller Status',
                'playing_time' => 'Spielzeit',
                'active_days' => 'Aktive Tage',
                'live' => 'Live',
                'ready' => 'Bereit',
                'playing' => 'Spielt',
                'paused' => 'Pausiert',
                'initializing' => 'Initialisiere...',
                'reloading' => 'Lade neu...',
                'error_occurred' => 'Ein Fehler ist aufgetreten',
                
                // Algorithm Information
                'automation_algorithm' => 'Automatisierungs-Algorithmus',
                'playing_time_defined' => 'Spielzeit wird im Benutzerkonto definiert',
                'random_play_time' => 'Zufällige Spielzeit: 61-600 Sekunden',
                'random_pause_time' => 'Zufällige Pausenzeit: 0-600 Sekunden',
                'automatic_playback' => 'Automatische Wiedergabe basierend auf Einstellungen',
                'shuffle_repeat' => 'Zufällige Wiedergabe und Wiederholung aller Songs',
                'reload_playlist' => 'Playlist neu laden, um Vorschaumodus zu entfernen',
                'requires_paid_account' => 'Erfordert kostenpflichtiges Apple Music Konto',
                'login_paid_account' => 'Mit kostenpflichtigem Konto anmelden, um Werbung zu entfernen',
                'manual_control_required' => 'Manuelle Wiedergabe-Steuerung erforderlich',
                'manual_pause_required' => 'Manuelle Pause-Steuerung erforderlich',
                'api_limitations' => 'Amazon Music API-Einschränkungen',
                'opens_new_window' => 'Öffnet in neuem Fenster/Tab',
                'requires_unlimited' => 'Erfordert Amazon Music Unlimited',
                
                // Control Panel
                'control_panel' => 'Steuerung',
                'auto_play' => 'Auto-Play',
                'automated_scheduling' => 'Automatisierte Planung',
                'shuffle' => 'Zufällige Wiedergabe',
                'random_track_order' => 'Zufällige Titelreihenfolge',
                'repeat' => 'Wiederholen',
                'loop_all_tracks' => 'Alle Titel wiederholen',
                'manual_control' => 'Manuelle Steuerung',
                'user_controlled_playback' => 'Benutzer-gesteuerte Wiedergabe',
                'statistics' => 'Statistiken',
                'listening_time_tracking' => 'Hörzeit-Verfolgung',
                'scheduling' => 'Planung',
                'time_window_monitoring' => 'Zeitfenster-Überwachung',
                
                // Playlist Management
                'no_playlist_selected' => 'Keine Playlist ausgewählt',
                'please_select_playlist' => 'Bitte wählen Sie eine Playlist in Ihren Kontoeinstellungen aus.',
                'configure_playlist' => 'Playlist konfigurieren',
                'reload_playlist_button' => 'Playlist neu laden',
                'new_login' => 'Neue Anmeldung',
                'open_playlist' => 'Playlist öffnen',
                'start_playlist' => 'Playlist starten',
                
                // Amazon Music Specific
                'amazon_music_playlist' => 'Amazon Music Playlist',
                'click_to_open' => 'Klicken Sie auf die Schaltfläche unten, um Ihre Amazon Music Playlist in einem neuen Fenster zu öffnen.',
                'open_amazon_playlist' => 'Amazon Music Playlist öffnen',
                'important_note' => 'Wichtiger Hinweis:',
                'amazon_limitations_text' => 'Aufgrund der Amazon Music API-Einschränkungen ist keine automatische Wiedergabe-Steuerung verfügbar. Sie müssen die Wiedergabe/Pause manuell im Amazon Music Fenster steuern. Das System protokolliert weiterhin Ihre Hörstatistiken während der geplanten Zeitfenster.',
                'amazon_limitations_title' => 'Amazon Music Einschränkungen',
                'amazon_limitations_list' => [
                    'Amazon Music bietet keine öffentliche Web-API für automatisierte Wiedergabe-Steuerung',
                    'Die Wiedergabe muss manuell im Amazon Music Fenster gesteuert werden',
                    'Das System kann weiterhin Ihre Hörstatistiken während geplanter Zeitfenster verfolgen',
                    'Diese Einschränkung liegt an Amazons API-Beschränkungen, nicht an unserem System'
                ],
                
                // Status Messages
                'playlist_opened' => 'Playlist geöffnet',
                'allow_popups' => 'Bitte erlauben Sie Pop-ups für diese Website, um Amazon Music zu öffnen.',
                'statistics_logged' => 'Statistiken protokolliert',
                'failed_to_log' => 'Fehler beim Protokollieren der Statistiken',
                'paused_before_time' => 'Pausiert (vor der Zeit)',
                'paused_after_time' => 'Pausiert (nach der Zeit)',
                'paused_outside_schedule' => 'Pausiert (außerhalb des Zeitplans)',
                'playing_seconds' => 'Spielt ({seconds}s)',
                'paused_seconds' => 'Pausiert ({seconds}s)',
                
                // Time and Days
                'monday' => 'Montag',
                'tuesday' => 'Dienstag',
                'wednesday' => 'Mittwoch',
                'thursday' => 'Donnerstag',
                'friday' => 'Freitag',
                'saturday' => 'Samstag',
                'sunday' => 'Sonntag',
                'not_set' => 'Nicht gesetzt',
                
                // Account Management
                'account_settings' => 'Kontoeinstellungen',
                'profile' => 'Profil',
                'personal_info' => 'Persönliche Informationen',
                'change_password' => 'Passwort ändern',
                'current_password' => 'Aktuelles Passwort',
                'new_password' => 'Neues Passwort',
                'confirm_new_password' => 'Neues Passwort bestätigen',
                'update_profile' => 'Profil aktualisieren',
                'account_deleted' => 'Konto gelöscht',
                'delete_account' => 'Konto löschen',
                'delete_account_warning' => 'Diese Aktion kann nicht rückgängig gemacht werden. Alle Ihre Daten werden dauerhaft gelöscht.',
                
                // Platform Management
                'connect_spotify' => 'Spotify verbinden',
                'connect_apple_music' => 'Apple Music verbinden',
                'connect_youtube' => 'YouTube verbinden',
                'connect_amazon' => 'Amazon Music verbinden',
                'disconnect' => 'Trennen',
                'connected' => 'Verbunden',
                'not_connected' => 'Nicht verbunden',
                'select_playlist' => 'Playlist auswählen',
                'current_playlist' => 'Aktuelle Playlist',
                'no_playlist' => 'Keine Playlist',
                
                // Error Messages
                'unauthorized' => 'Nicht autorisiert',
                'database_error' => 'Datenbankfehler',
                'configuration_error' => 'Konfigurationsfehler',
                'api_error' => 'API-Fehler',
                'network_error' => 'Netzwerkfehler',
                'invalid_credentials' => 'Ungültige Anmeldedaten',
                'account_not_found' => 'Konto nicht gefunden',
                'playlist_not_found' => 'Playlist nicht gefunden',
                
                // Success Messages
                'login_successful' => 'Anmeldung erfolgreich',
                'signup_successful' => 'Konto erfolgreich erstellt',
                'settings_updated' => 'Einstellungen erfolgreich aktualisiert',
                'playlist_connected' => 'Playlist erfolgreich verbunden',
                'playlist_disconnected' => 'Playlist erfolgreich getrennt',
                
                // Language
                'language' => 'Sprache',
                'german' => 'Deutsch',
                'english' => 'Englisch',
                'change_language' => 'Sprache ändern',
                
                // Footer
                'copyright' => '© 2024 Playlist Manager. Alle Rechte vorbehalten.',
                'privacy_policy' => 'Datenschutzrichtlinie',
                'terms_of_service' => 'Nutzungsbedingungen',
                'contact_support' => 'Support kontaktieren',
                'quick_links' => 'Schnelllinks',
                'home' => 'Startseite',
                'support' => 'Support & Rechtliches',
                'help_center' => 'Hilfecenter',
                'contact' => 'Kontakt',
                'faq' => 'FAQ',
                'legal_notice' => 'Rechtliche Hinweise',
                'back_to_top' => 'Nach oben',
                
                // Legal Pages
                'impressum_title' => 'Impressum',
                'impressum_description' => 'Rechtliche Informationen und Unternehmensdetails für Playlist Manager',
                'impressum_subtitle' => 'Rechtliche Informationen und Unternehmensdetails',
                'impressum_legal_info' => 'Rechtliche Informationen',
                'impressum_company' => 'Unternehmen',
                'impressum_contact' => 'Kontaktinformationen',
                'impressum_management' => 'Geschäftsführung',
                'impressum_managing_director' => 'Geschäftsführer',
                'impressum_managing_director_desc' => 'Verantwortlich für Geschäftsbetrieb',
                'impressum_supervisory_board' => 'Aufsichtsrat',
                'impressum_supervisory_board_desc' => 'Überwacht die Unternehmensführung',
                'impressum_registration' => 'Registerinformationen',
                'impressum_court' => 'Registergericht',
                'impressum_registration_number' => 'Registernummer',
                'impressum_tax_id' => 'Steuernummer',
                'impressum_vat_id' => 'USt-IdNr.',
                'impressum_professional_info' => 'Berufsrechtliche Informationen',
                'impressum_professional_title' => 'Berufsbezeichnung',
                'impressum_professional_title_desc' => 'Softwareentwicklung und Musiktechnologie-Dienstleistungen',
                'impressum_professional_authority' => 'Aufsichtsbehörde',
                'impressum_professional_authority_desc' => 'Zuständige Industrie- und Handelskammer',
                'impressum_professional_regulation' => 'Berufsrechtliche Regelungen',
                'impressum_professional_regulation_desc' => 'Unterliegt dem deutschen Handelsrecht',
                'impressum_disclaimer' => 'Haftungsausschluss',
                'impressum_disclaimer_content' => 'Die auf dieser Website bereitgestellten Informationen dienen nur zu allgemeinen Informationszwecken. Obwohl wir uns bemühen, die Informationen aktuell und korrekt zu halten, geben wir keine Zusicherungen oder Garantien jeglicher Art über die Vollständigkeit, Genauigkeit, Zuverlässigkeit, Eignung oder Verfügbarkeit der Informationen, Produkte, Dienstleistungen oder zugehörigen Grafiken auf der Website.',
                'impressum_liability' => 'Haftung',
                'impressum_liability_content' => 'In keinem Fall haften wir für Verluste oder Schäden einschließlich, aber nicht beschränkt auf, indirekte oder Folgeschäden, die sich aus Datenverlust oder Gewinnverlust ergeben, die aus der Nutzung dieser Website entstehen oder damit zusammenhängen.',
                
                // Privacy Policy
                'privacy_title' => 'Datenschutzrichtlinie',
                'privacy_description' => 'Wie wir Ihre persönlichen Daten sammeln, verwenden und schützen',
                'privacy_subtitle' => 'Wie wir Ihre persönlichen Daten sammeln, verwenden und schützen',
                'privacy_last_updated' => 'Zuletzt aktualisiert',
                'privacy_introduction' => 'Einleitung',
                'privacy_introduction_content' => 'Diese Datenschutzrichtlinie erklärt, wie Playlist Manager GmbH ("wir", "uns" oder "unser") Ihre persönlichen Informationen sammelt, verwendet und schützt, wenn Sie unseren Playlist-Management-Service nutzen. Wir sind verpflichtet, Ihre Privatsphäre zu schützen und die Sicherheit Ihrer persönlichen Daten zu gewährleisten.',
                'privacy_controller' => 'Verantwortlicher',
                'privacy_data_collection' => 'Datenerhebung',
                'privacy_personal_data' => 'Persönliche Daten, die wir sammeln',
                'privacy_personal_data_desc' => 'Wir sammeln die folgenden Arten von persönlichen Daten:',
                'privacy_data_name' => 'Name und Kontaktinformationen',
                'privacy_data_email' => 'E-Mail-Adresse',
                'privacy_data_usage' => 'Nutzungsdaten und Präferenzen',
                'privacy_data_playlists' => 'Playlist-Informationen und Musikpräferenzen',
                'privacy_data_preferences' => 'Kontoeinstellungen und Präferenzen',
                'privacy_automatically_collected' => 'Automatisch gesammelte Daten',
                'privacy_automatically_collected_desc' => 'Wir sammeln automatisch die folgenden Daten:',
                'privacy_data_ip' => 'IP-Adresse und Geräteinformationen',
                'privacy_data_browser' => 'Browsertyp und -version',
                'privacy_data_device' => 'Gerätetyp und Betriebssystem',
                'privacy_data_cookies' => 'Cookies und Nutzungsanalysen',
                'privacy_purpose' => 'Zweck der Datenverarbeitung',
                'privacy_purpose_account' => 'Kontoverwaltung',
                'privacy_purpose_account_desc' => 'Zur Erstellung und Verwaltung Ihres Benutzerkontos',
                'privacy_purpose_playlists' => 'Playlist-Verwaltung',
                'privacy_purpose_playlists_desc' => 'Zur Verwaltung und Synchronisierung Ihrer Playlists über Plattformen hinweg',
                'privacy_purpose_analytics' => 'Analysen & Verbesserung',
                'privacy_purpose_analytics_desc' => 'Zur Verbesserung unseres Services und der Benutzererfahrung',
                'privacy_purpose_security' => 'Sicherheit & Betrugsprävention',
                'privacy_purpose_security_desc' => 'Zum Schutz vor Betrug und zur Gewährleistung der Sicherheit',
                'privacy_legal_basis' => 'Rechtliche Grundlage für die Verarbeitung',
                'privacy_consent' => 'Einwilligung',
                'privacy_consent_desc' => 'Sie haben eine klare Einwilligung zur Verarbeitung Ihrer persönlichen Daten für bestimmte Zwecke gegeben.',
                'privacy_contract' => 'Vertragserfüllung',
                'privacy_contract_desc' => 'Die Verarbeitung ist für die Erfüllung eines Vertrags mit Ihnen erforderlich.',
                'privacy_legitimate_interest' => 'Berechtigtes Interesse',
                'privacy_legitimate_interest_desc' => 'Die Verarbeitung ist für unsere berechtigten Interessen an der Bereitstellung und Verbesserung unseres Services erforderlich.',
                'privacy_data_sharing' => 'Datenweitergabe',
                'privacy_third_parties' => 'Drittanbieter-Services',
                'privacy_third_parties_desc' => 'Wir integrieren die folgenden Musikplattformen:',
                'privacy_spotify_desc' => 'Für Spotify Playlist-Verwaltung und -wiedergabe',
                'privacy_apple_desc' => 'Für Apple Music Playlist-Verwaltung und -wiedergabe',
                'privacy_youtube_desc' => 'Für YouTube Music Playlist-Verwaltung und -wiedergabe',
                'privacy_amazon_desc' => 'Für Amazon Music Playlist-Verwaltung und -wiedergabe',
                'privacy_no_sale' => 'Kein Datenverkauf',
                'privacy_no_sale_desc' => 'Wir verkaufen, handeln oder übertragen Ihre persönlichen Daten nicht an Dritte zu kommerziellen Zwecken.',
                'privacy_data_retention' => 'Datenaufbewahrung',
                'privacy_account_data' => 'Kontodaten',
                'privacy_account_data_retention' => 'Aufbewahrt bis zur Kontolöschung oder 3 Jahre Inaktivität',
                'privacy_usage_data' => 'Nutzungsdaten',
                'privacy_usage_data_retention' => 'Aufbewahrt für 2 Jahre für Analysen und Verbesserungen',
                'privacy_logs' => 'Server-Logs',
                'privacy_logs_retention' => 'Aufbewahrt für 90 Tage für Sicherheit und Fehlerbehebung',
                'privacy_cookies' => 'Cookies',
                'privacy_cookies_retention' => 'Session-Cookies werden beim Schließen des Browsers gelöscht, persistente Cookies für 1 Jahr',
                'privacy_your_rights' => 'Ihre Rechte',
                'privacy_right_access' => 'Recht auf Auskunft',
                'privacy_right_access_desc' => 'Sie haben das Recht, auf Ihre persönlichen Daten zuzugreifen',
                'privacy_right_rectification' => 'Recht auf Berichtigung',
                'privacy_right_rectification_desc' => 'Sie haben das Recht, ungenaue Daten zu korrigieren',
                'privacy_right_erasure' => 'Recht auf Löschung',
                'privacy_right_erasure_desc' => 'Sie haben das Recht, Ihre persönlichen Daten zu löschen',
                'privacy_right_portability' => 'Recht auf Datenübertragbarkeit',
                'privacy_right_portability_desc' => 'Sie haben das Recht, Ihre Daten in einem übertragbaren Format zu erhalten',
                'privacy_contact_rights' => 'Ausübung Ihrer Rechte',
                'privacy_contact_rights_desc' => 'Um eines dieser Rechte auszuüben, kontaktieren Sie uns bitte:',
                'privacy_cookies_title' => 'Cookies',
                'privacy_cookies_desc' => 'Wir verwenden Cookies, um Ihre Erfahrung auf unserer Website zu verbessern:',
                'privacy_cookies_essential' => 'Notwendige Cookies',
                'privacy_cookies_essential_desc' => 'Erforderlich für grundlegende Website-Funktionalität',
                'privacy_cookies_functional' => 'Funktionale Cookies',
                'privacy_cookies_functional_desc' => 'Verbessern die Benutzererfahrung und merken sich Präferenzen',
                'privacy_cookies_analytics' => 'Analyse-Cookies',
                'privacy_cookies_analytics_desc' => 'Helfen uns zu verstehen, wie Besucher unsere Website nutzen',
                'privacy_contact_title' => 'Kontaktinformationen',
                'privacy_contact_desc' => 'Wenn Sie Fragen zu dieser Datenschutzrichtlinie oder unseren Datenpraktiken haben, kontaktieren Sie uns bitte:',
                'privacy_data_protection_officer' => 'Datenschutzbeauftragter',
                'privacy_supervisory_authority' => 'Aufsichtsbehörde'
            ]
        ];
    }
    
    public function get($key, $params = []) {
        $translation = $this->translations[$this->currentLanguage][$key] ?? 
                      $this->translations[$this->fallbackLanguage][$key] ?? 
                      $key;
        
        // Replace parameters
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $translation = str_replace('{' . $param . '}', $value, $translation);
            }
        }
        
        return $translation;
    }
    
    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }
    
    public function setLanguage($language) {
        if (in_array($language, ['de', 'en'])) {
            $this->currentLanguage = $language;
            $_SESSION['language'] = $language;
        }
    }
    
    public function getAvailableLanguages() {
        return array_keys($this->translations);
    }
    
    public function isRTL() {
        return false; // German and English are LTR languages
    }
    
    public function getLanguageName($code) {
        $names = [
            'de' => 'Deutsch',
            'en' => 'English'
        ];
        return $names[$code] ?? $code;
    }
}

// Global language instance
global $lang;
$lang = new LanguageManager();
?> 