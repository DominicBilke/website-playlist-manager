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
                'welcome_back' => 'Welcome Back',
                'sign_in_to_continue' => 'Sign in to continue',
                'username_or_email' => 'Username or Email',
                'enter_username_or_email' => 'Enter your username or email',
                'enter_password' => 'Enter your password',
                'toggle_password' => 'Toggle password visibility',
                'or' => 'or',
                'continue_with_google' => 'Continue with Google',
                'continue_with_apple' => 'Continue with Apple',
                'back_to_home' => 'Back to Home',
                'please_fill_all_fields' => 'Please fill in all fields',
                'invalid_credentials' => 'Invalid username or password',
                'login_error' => 'Login error occurred',
                'logout_successful' => 'Logout successful',
                'username_too_short' => 'Username must be at least 3 characters long',
                'username_invalid' => 'Username can only contain letters, numbers, and underscores',
                'username_taken' => 'Username is already taken',
                'password_too_short' => 'Password must be at least 6 characters long',
                'team_number_required' => 'Team number is required',
                'office_required' => 'Office selection is required',
                'signup_successful' => 'Account created successfully',
                'signup_error' => 'Error creating account',
                'database_error' => 'Database error occurred',
                'please_enter_email' => 'Please enter your email address',
                'invalid_email_format' => 'Please enter a valid email address',
                'password_reset_sent' => 'If an account exists with this email, a password reset link has been sent',
                'password_reset_error' => 'An error occurred while processing your request',
                'enter_email_for_reset' => 'Enter your email address and we\'ll send you a link to reset your password',
                'enter_your_email' => 'Enter your email address',
                'send_reset_link' => 'Send Reset Link',
                'back_to_login' => 'Back to Login',
                
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
                
                // Home Page
                'get_started' => 'Get Started',
                'sign_in' => 'Sign In',
                'start_listening' => 'Start Listening',
                'connect' => 'Connect',
                'music_kit_integration' => 'MusicKit JS Integration',
                'advanced_controls' => 'Advanced Controls',
                'library_access' => 'Library Access',
                'iframe_integration' => 'IFrame Integration',
                'video_player' => 'Video Player',
                'playlist_control' => 'Playlist Control',
                'manual_control' => 'Manual Control',
                'external_player' => 'External Player',
                'welcome' => 'Welcome',
                
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
                'back_to_top' => 'Back to Top'
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
                'welcome_back' => 'Willkommen zurück',
                'sign_in_to_continue' => 'Melden Sie sich an, um fortzufahren',
                'username_or_email' => 'Benutzername oder E-Mail',
                'enter_username_or_email' => 'Geben Sie Ihren Benutzernamen oder E-Mail ein',
                'enter_password' => 'Geben Sie Ihr Passwort ein',
                'toggle_password' => 'Passwort-Sichtbarkeit umschalten',
                'or' => 'oder',
                'continue_with_google' => 'Mit Google fortfahren',
                'continue_with_apple' => 'Mit Apple fortfahren',
                'back_to_home' => 'Zurück zur Startseite',
                'please_fill_all_fields' => 'Bitte füllen Sie alle Felder aus',
                'invalid_credentials' => 'Ungültiger Benutzername oder Passwort',
                'login_error' => 'Anmeldefehler aufgetreten',
                'logout_successful' => 'Abmeldung erfolgreich',
                'username_too_short' => 'Benutzername muss mindestens 3 Zeichen lang sein',
                'username_invalid' => 'Benutzername darf nur Buchstaben, Zahlen und Unterstriche enthalten',
                'username_taken' => 'Benutzername ist bereits vergeben',
                'password_too_short' => 'Passwort muss mindestens 6 Zeichen lang sein',
                'team_number_required' => 'Team-Nummer ist erforderlich',
                'office_required' => 'Büro-Auswahl ist erforderlich',
                'signup_successful' => 'Konto erfolgreich erstellt',
                'signup_error' => 'Fehler beim Erstellen des Kontos',
                'database_error' => 'Datenbankfehler aufgetreten',
                'please_enter_email' => 'Bitte geben Sie Ihre E-Mail-Adresse ein',
                'invalid_email_format' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein',
                'password_reset_sent' => 'Falls ein Konto mit dieser E-Mail existiert, wurde ein Link zum Zurücksetzen des Passworts gesendet',
                'password_reset_error' => 'Beim Verarbeiten Ihrer Anfrage ist ein Fehler aufgetreten',
                'enter_email_for_reset' => 'Geben Sie Ihre E-Mail-Adresse ein und wir senden Ihnen einen Link zum Zurücksetzen Ihres Passworts',
                'enter_your_email' => 'Geben Sie Ihre E-Mail-Adresse ein',
                'send_reset_link' => 'Link zum Zurücksetzen senden',
                'back_to_login' => 'Zurück zur Anmeldung',
                
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
                'automated_playback' => 'Automatisierte Playlist-Wiedergabe mit intelligenter Planung',
                'manual_playback' => 'Manuelle Playlist-Wiedergabe mit intelligenter Planung',
                'current_status' => 'Aktueller Status',
                'playing_time' => 'Wiedergabezeit',
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
                'playing_time_defined' => 'Wiedergabezeit in Benutzerkonto definiert',
                'random_play_time' => 'Zufällige Wiedergabezeit: 61-600 Sekunden',
                'random_pause_time' => 'Zufällige Pausenzeit: 0-600 Sekunden',
                'automatic_playback' => 'Automatische Wiedergabe basierend auf Einstellungen',
                'shuffle_repeat' => 'Zufällige Wiedergabe und Wiederholung aller Songs',
                'reload_playlist' => 'Playlist neu laden, um Vorschaumodus zu entfernen',
                'requires_paid_account' => 'Erfordert kostenpflichtiges Apple Music Konto',
                'login_paid_account' => 'Mit kostenpflichtigem Konto anmelden, um Werbung zu entfernen',
                'manual_control_required' => 'Manuelle Wiedergabe-Steuerung erforderlich',
                'manual_pause_required' => 'Manuelle Pause-Steuerung erforderlich',
                'api_limitations' => 'Amazon Music API Einschränkungen',
                'opens_new_window' => 'Öffnet in neuem Fenster/Tab',
                'requires_unlimited' => 'Erfordert Amazon Music Unlimited',
                
                // Control Panel
                'control_panel' => 'Steuerungspanel',
                'auto_play' => 'Auto Play',
                'automated_scheduling' => 'Automatisierte Planung',
                'shuffle' => 'Zufällige Wiedergabe',
                'random_track_order' => 'Zufällige Titelreihenfolge',
                'repeat' => 'Wiederholen',
                'loop_all_tracks' => 'Alle Titel wiederholen',
                'manual_control' => 'Manuelle Steuerung',
                'user_controlled_playback' => 'Benutzer-gesteuerte Wiedergabe',
                'statistics' => 'Statistiken',
                'listening_time_tracking' => 'Hörzeit-Tracking',
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
                'amazon_limitations_text' => 'Aufgrund von Amazon Music API Einschränkungen ist keine automatische Wiedergabe-Steuerung verfügbar. Sie müssen die Wiedergabe/Pause manuell im Amazon Music Fenster steuern. Das System protokolliert weiterhin Ihre Hörstatistiken während Ihrer geplanten Zeitfenster.',
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
                
                // Home Page
                'get_started' => 'Jetzt starten',
                'sign_in' => 'Anmelden',
                'start_listening' => 'Mit dem Hören beginnen',
                'connect' => 'Verbinden',
                'music_kit_integration' => 'MusicKit JS Integration',
                'advanced_controls' => 'Erweiterte Steuerung',
                'library_access' => 'Bibliothek-Zugriff',
                'iframe_integration' => 'IFrame Integration',
                'video_player' => 'Video Player',
                'playlist_control' => 'Playlist-Steuerung',
                'manual_control' => 'Manuelle Steuerung',
                'external_player' => 'Externer Player',
                'welcome' => 'Willkommen',
                
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
                'back_to_top' => 'Nach oben'
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