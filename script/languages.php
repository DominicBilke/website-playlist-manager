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
                'create_account' => 'Create Account',
                'join_playlist_manager' => 'Join Playlist Manager',
                'username' => 'Username',
                'enter_username' => 'Enter your username',
                'enter_team' => 'Enter your team number',
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
                'back_to_top' => 'Back to Top',
                
                // Privacy Policy
                'privacy_title' => 'Privacy Policy',
                'privacy_subtitle' => 'How we collect, use, and protect your personal data',
                'privacy_description' => 'Privacy Policy for Playlist Manager - Learn how we handle your personal data',
                'privacy_last_updated' => 'Last Updated',
                'privacy_introduction' => 'Introduction',
                'privacy_introduction_content' => 'As the operator of this website and as a company, we come into contact with your personal data. This concerns all data that reveals something about you and by which you can be identified. In this privacy policy, we would like to explain how, for what purpose and on which legal basis we process your data.',
                'privacy_controller' => 'Data Controller',
                'privacy_general_info' => 'General Information',
                'privacy_ssl_encryption' => 'SSL or TLS Encryption',
                'privacy_ssl_encryption_desc' => 'When you enter your data on websites, place online orders or send e-mails via the Internet, you must always be prepared for unauthorized third parties to access your data. There is no complete protection against such access. However, we do our utmost to protect your data as best we can and to close security gaps as far as we can.',
                'privacy_ssl_encryption_how' => 'An important protection mechanism is the SSL or TLS encryption of our website, which ensures that data you transmit to us cannot be read by third parties. You can recognize the encryption by the lock icon in front of the Internet address entered in your browser and by the fact that our Internet address begins with https:// and not with http://.',
                'privacy_data_retention' => 'Data Retention',
                'privacy_data_retention_desc' => 'In some parts in this privacy policy, we inform you about how long we or the companies that process your data on our behalf will store your data. In the absence of such information, we store your data until the purpose of the data processing no longer applies, you object to the data processing or you revoke your consent to the data processing.',
                'privacy_data_retention_exceptions' => 'In the event of an objection or revocation, we may however continue to process your data if at least one of the following conditions applies: We have compelling legitimate grounds for continuing to process the data that override your interests, rights and freedoms; The data processing is necessary to assert, exercise or defend legal claims; We are required by law to retain your data.',
                'privacy_your_rights' => 'Your Rights',
                'privacy_objection' => 'Objection to Data Processing',
                'privacy_objection_important' => 'IF IT\'S STATED IN THIS PRIVACY STATEMENT THAT WE HAVE LEGITIMATE INTERESTS FOR THE PROCESSING OF YOUR DATA AND THAT THIS PROCESSING IS THEREFORE BASED ON ART. 6 PARA. 1 SENTENCE 1 LIT. F) GDPR, YOU HAVE THE RIGHT TO OBJECT IN ACCORDANCE WITH ART. 21 GDPR.',
                'privacy_objection_desc' => 'This also applies to profiling that is carried out on the basis of the aforementioned provision. The prerequisite is that you state reasons for the objection that arise from your particular situation. No reasons are required if the objection is directed against the use of your data for direct advertising.',
                'privacy_objection_exceptions' => 'The consequence of the objection is that we may no longer process your data. This only does not apply if we can demonstrate compelling legitimate grounds for the processing that override your interests, rights and freedoms, or if the processing is necessary for asserting, exercising or defending legal claims.',
                'privacy_withdrawal' => 'Withdrawal of Consent',
                'privacy_withdrawal_desc' => 'Many data processing operations are based on your consent. You may revoke your consent at any time without giving reasons (Art. 7 (3) GDPR). From the time of revocation, we may then no longer process your data.',
                'privacy_complaint' => 'Right to Complain',
                'privacy_complaint_desc' => 'If you believe that we are in breach of the General Data Protection Regulation (GDPR), you have the right to complain to a supervisory authority in accordance with Art. 77 GDPR.',
                'privacy_portability' => 'Data Portability',
                'privacy_portability_desc' => 'We must hand over data that we process automatically on the basis of your consent or in fulfillment of a contract to you or a third party in a common machine-readable format if you request this.',
                'privacy_correction' => 'Information, Deletion, and Correction',
                'privacy_correction_desc' => 'According to Art. 15 GDPR, you have the right to receive information free of charge about which of your personal data we have stored. If the data is incorrect, you have a right to rectification (Art. 16 GDPR), and under the conditions of Art. 17 GDPR you may demand that we delete the data.',
                'privacy_data_collection' => 'Data Collection',
                'privacy_cookies' => 'Use of Cookies',
                'privacy_cookies_desc' => 'Our website places cookies on your device. These are small text files that are used for various purposes. Some cookies are technically necessary for the website to function at all (necessary cookies). Others are needed to perform certain actions or functions on the site (functional cookies).',
                'privacy_cookies_necessary' => 'Necessary Cookies',
                'privacy_cookies_necessary_desc' => 'Technically required for website functionality',
                'privacy_cookies_functional' => 'Functional Cookies',
                'privacy_cookies_functional_desc' => 'Enable specific features and actions',
                'privacy_cookies_analytics' => 'Analytics Cookies',
                'privacy_cookies_analytics_desc' => 'Analyze user behavior and optimize',
                'privacy_server_logs' => 'Server Log Files',
                'privacy_server_logs_desc' => 'Server log files log all requests and accesses to our website and record error messages. They also include personal data, in particular your IP address. However, this is anonymized by the provider after a short time.',
                'privacy_server_logs_data' => 'Data collected in server logs:',
                'privacy_logs_browser' => 'Browser type and version',
                'privacy_logs_os' => 'Operating system used',
                'privacy_logs_referrer' => 'Referrer URL',
                'privacy_logs_hostname' => 'Host name of the accessing computer',
                'privacy_logs_time' => 'Time of the server request',
                'privacy_logs_ip' => 'IP address (anonymized if necessary)',
                'privacy_contact_registration' => 'Contact and Registration',
                'privacy_contact_methods' => 'Contact Methods',
                'privacy_contact_methods_desc' => 'You can send us a message by e-mail or fax or call us.',
                'privacy_contact_processing' => 'How we process your data',
                'privacy_contact_processing_desc' => 'We store your message as well as your contact details in order to be able to process your inquiry including follow-up questions. We do not pass on the data to other persons without your consent.',
                'privacy_registration' => 'Registration Function',
                'privacy_registration_desc' => 'In order to use certain functions or offers on our website, you must register. This requires you to provide your e-mail address and possibly other personal data.',
                'privacy_registration_purpose' => 'Purpose of registration data',
                'privacy_registration_purpose_desc' => 'We store the data you provide during registration and use it to provide you with the function or offer for which you have registered.',
                'privacy_third_party' => 'Third-Party Services',
                'privacy_youtube_desc' => 'You can watch YouTube videos on our website. In doing so, Google, as the provider of YouTube, collects and stores certain information about you. However, since we use YouTube in extended data protection mode, this only happens when you start a video.',
                'privacy_youtube_processing' => 'How YouTube processes your data',
                'privacy_youtube_processing_desc' => 'Google\'s servers are told which of our pages were visited from your device. If you are logged into your YouTube account while surfing, Google can assign the visit to our website to your personal profile.',
                'privacy_hosting' => 'Hosting and CDN',
                'privacy_hosting_desc' => 'Our website is hosted on a server of the following Internet service provider (hoster). The hoster stores all the data from our website. This includes all personal data that is collected automatically or through entering.',
                'privacy_contact_info' => 'Contact Information',
                'privacy_questions' => 'Questions about Privacy?',
                'privacy_contact_us' => 'If you have any questions about this privacy policy or how we handle your data, please contact us:',
                
                // Impressum
                'impressum_title' => 'Legal Notice',
                'impressum_subtitle' => 'Information about the company and legal requirements',
                'impressum_description' => 'Legal Notice for Playlist Manager - Company information and legal requirements',
                'impressum_legal_info' => 'Legal Information',
                'impressum_company' => 'Company',
                'impressum_contact' => 'Contact',
                'impressum_management' => 'Management',
                'impressum_managing_director' => 'Managing Director',
                'impressum_managing_director_desc' => 'Responsible for the management of the company',
                'impressum_supervisory_board' => 'Supervisory Board',
                'impressum_supervisory_board_desc' => 'Oversees the management of the company',
                'impressum_registration' => 'Registration Information',
                'impressum_court' => 'Register Court',
                'impressum_registration_number' => 'Registration Number',
                'impressum_tax_id' => 'Tax ID',
                'impressum_vat_id' => 'VAT ID',
                'impressum_professional_info' => 'Professional Information',
                'impressum_professional_title' => 'Professional Title',
                'impressum_professional_title_desc' => 'Information about professional qualifications and titles',
                'impressum_professional_authority' => 'Professional Authority',
                'impressum_professional_authority_desc' => 'Responsible professional authority for oversight',
                'impressum_professional_regulation' => 'Professional Regulation',
                'impressum_professional_regulation_desc' => 'Applicable professional regulations and standards',
                'impressum_disclaimer' => 'Disclaimer',
                'impressum_disclaimer_content' => 'The information provided on this website is for general informational purposes only. While we strive to keep the information up to date and correct, we make no representations or warranties of any kind, express or implied, about the completeness, accuracy, reliability, suitability or availability of the information, products, services, or related graphics contained on the website for any purpose.',
                'impressum_liability' => 'Liability',
                'impressum_liability_content' => 'In no event will we be liable for any loss or damage including without limitation, indirect or consequential loss or damage, arising from loss of data or profits arising out of, or in connection with, the use of this website.',
                
                // Admin Panel
                'admin_panel' => 'Admin Panel',
                'user_management' => 'User Management',
                'system_settings' => 'System Settings',
                'recent_activity' => 'Recent Activity',
                'total_users' => 'Total Users',
                'active_users' => 'Active Users',
                'admin_users' => 'Admin Users',
                'logged_in_as' => 'Logged in as',
                'user' => 'User',
                'role' => 'Role',
                'status' => 'Status',
                'actions' => 'Actions',
                'edit_user' => 'Edit User',
                'delete_user' => 'Delete User',
                'user_updated_successfully' => 'User updated successfully',
                'user_deleted_successfully' => 'User deleted successfully',
                'setting_updated_successfully' => 'Setting updated successfully',
                'update_error' => 'Update error occurred',
                'delete_error' => 'Delete error occurred',
                'access_denied' => 'Access denied',
                'account_locked' => 'Account locked due to too many failed login attempts',
                'passwords_dont_match' => 'Passwords do not match',
                'registration_disabled' => 'User registration is currently disabled',
                'email_taken' => 'Email address is already taken',
                'username' => 'Username',
                'enter_your_email' => 'Enter your email address',
                
                // Music Player
                'music_player' => 'Music Player',
                'spotify_player' => 'Spotify Player',
                'spotify_connected_successfully' => 'Spotify connected successfully',
                'select_playlist' => 'Select Playlist',
                'choose_playlist' => 'Choose a playlist',
                'play' => 'Play',
                'pause' => 'Pause',
                'now_playing' => 'Now Playing',
                'playlist_management' => 'Playlist Management',
                'create_playlist' => 'Create Playlist',
                'generate_playlist' => 'Generate Playlist',
                'import_playlist' => 'Import Playlist',
                'import' => 'Import',
                'time_range' => 'Time Range',
                'last_4_weeks' => 'Last 4 Weeks',
                'last_6_months' => 'Last 6 Months',
                'all_time' => 'All Time',
                'save_settings' => 'Save Settings',
                'quick_actions' => 'Quick Actions',
                'open_spotify' => 'Open Spotify',
                'refresh_playlists' => 'Refresh Playlists',
                'back_to_player' => 'Back to Player',
                'please_select_playlist' => 'Please select a playlist',
                'playback_error' => 'Playback error occurred',
                'create_playlist_feature' => 'Create playlist feature coming soon',
                'import_playlist_feature' => 'Import playlist feature coming soon',
                'next_track_feature' => 'Next track feature coming soon',
                'settings_saved' => 'Settings saved successfully',
                'open_player' => 'Open Player',
                'sign_up' => 'Sign Up',
                
                // Player Controls
                'playback_controls' => 'Playback Controls',
                'volume' => 'Volume',
                'mute' => 'Mute',
                'unmute' => 'Unmute',
                'shuffle_play' => 'Shuffle Play',
                'repeat_one' => 'Repeat One',
                'repeat_all' => 'Repeat All',
                'repeat_off' => 'Repeat Off',
                'previous_track' => 'Previous Track',
                'next_track' => 'Next Track',
                'seek_forward' => 'Seek Forward',
                'seek_backward' => 'Seek Backward',
                
                // Playlist Features
                'add_to_playlist' => 'Add to Playlist',
                'remove_from_playlist' => 'Remove from Playlist',
                'playlist_info' => 'Playlist Info',
                'playlist_tracks' => 'Playlist Tracks',
                'playlist_duration' => 'Playlist Duration',
                'playlist_owner' => 'Playlist Owner',
                'playlist_public' => 'Public Playlist',
                'playlist_private' => 'Private Playlist',
                'playlist_collaborative' => 'Collaborative Playlist',
                
                // User Interface
                'search' => 'Search',
                'search_placeholder' => 'Search for songs, artists, or playlists',
                'filter' => 'Filter',
                'sort_by' => 'Sort by',
                'sort_name' => 'Name',
                'sort_date' => 'Date',
                'sort_popularity' => 'Popularity',
                'sort_duration' => 'Duration',
                'sort_artist' => 'Artist',
                'sort_album' => 'Album',
                'ascending' => 'Ascending',
                'descending' => 'Descending',
                
                // Notifications
                'notification' => 'Notification',
                'notifications' => 'Notifications',
                'mark_as_read' => 'Mark as Read',
                'mark_all_as_read' => 'Mark All as Read',
                'no_notifications' => 'No notifications',
                'new_notification' => 'New notification',
                
                // Error Messages
                'error_occurred' => 'An error occurred',
                'try_again' => 'Try again',
                'connection_error' => 'Connection error',
                'timeout_error' => 'Timeout error',
                'server_error' => 'Server error',
                'not_found' => 'Not found',
                'unauthorized' => 'Unauthorized',
                'forbidden' => 'Forbidden',
                'too_many_requests' => 'Too many requests',
                
                // Success Messages
                'operation_successful' => 'Operation successful',
                'changes_saved' => 'Changes saved',
                'item_added' => 'Item added',
                'item_removed' => 'Item removed',
                'item_updated' => 'Item updated',
                'item_deleted' => 'Item deleted',
                
                // Confirmation Dialogs
                'confirm_action' => 'Confirm Action',
                'confirm_delete' => 'Confirm Delete',
                'confirm_logout' => 'Confirm Logout',
                'confirm_unsaved_changes' => 'Confirm Unsaved Changes',
                'are_you_sure' => 'Are you sure?',
                'this_action_cannot_be_undone' => 'This action cannot be undone',
                'you_have_unsaved_changes' => 'You have unsaved changes',
                
                // Time and Date
                'today' => 'Today',
                'yesterday' => 'Yesterday',
                'tomorrow' => 'Tomorrow',
                'this_week' => 'This Week',
                'last_week' => 'Last Week',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                'this_year' => 'This Year',
                'last_year' => 'Last Year',
                'minutes_ago' => 'minutes ago',
                'hours_ago' => 'hours ago',
                'days_ago' => 'days ago',
                'weeks_ago' => 'weeks ago',
                'months_ago' => 'months ago',
                'years_ago' => 'years ago',
                
                // Statistics
                'total_playtime' => 'Total Playtime',
                'total_tracks' => 'Total Tracks',
                'total_playlists' => 'Total Playlists',
                'favorite_artists' => 'Favorite Artists',
                'favorite_genres' => 'Favorite Genres',
                'listening_history' => 'Listening History',
                'recent_activity' => 'Recent Activity',
                'top_tracks' => 'Top Tracks',
                'top_artists' => 'Top Artists',
                'top_albums' => 'Top Albums',
                
                // Platform Specific
                'spotify_web_player' => 'Spotify Web Player',
                'apple_music_web_player' => 'Apple Music Web Player',
                'youtube_music_web_player' => 'YouTube Music Web Player',
                'amazon_music_web_player' => 'Amazon Music Web Player',
                'connect_account' => 'Connect Account',
                'disconnect_account' => 'Disconnect Account',
                'account_connected' => 'Account Connected',
                'account_disconnected' => 'Account Disconnected',
                'reconnect_account' => 'Reconnect Account',
                'account_expired' => 'Account Expired',
                'refresh_token' => 'Refresh Token',
                'token_refreshed' => 'Token Refreshed',
                'token_refresh_failed' => 'Token Refresh Failed',
                
                // Additional Player Interface
                'open_external' => 'Open External',
                'player_controls' => 'Player Controls',
                'select_platform' => 'Select Platform',
                'choose_platform' => 'Choose a platform',
                'please_select_platform' => 'Please select a platform',
                'open_settings' => 'Open Settings',
                
                // Admin Panel Additional
                'last_login' => 'Last Login'
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
                'create_account' => 'Konto erstellen',
                'join_playlist_manager' => 'Playlist Manager beitreten',
                'username' => 'Benutzername',
                'enter_username' => 'Geben Sie Ihren Benutzernamen ein',
                'enter_team' => 'Geben Sie Ihre Team-Nummer ein',
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
                'back_to_top' => 'Nach oben',
                
                // Privacy Policy
                'privacy_title' => 'Datenschutzrichtlinie',
                'privacy_subtitle' => 'Wie wir Ihre persönlichen Daten sammeln, verwenden und schützen',
                'privacy_description' => 'Datenschutzrichtlinie für Playlist Manager - Erfahren Sie, wie wir mit Ihren persönlichen Daten umgehen',
                'privacy_last_updated' => 'Zuletzt aktualisiert',
                'privacy_introduction' => 'Einleitung',
                'privacy_introduction_content' => 'Als Betreiber dieser Website und als Unternehmen kommen wir mit Ihren persönlichen Daten in Kontakt. Dies betrifft alle Daten, die etwas über Sie aussagen und durch die Sie identifiziert werden können. In dieser Datenschutzrichtlinie möchten wir erklären, wie, zu welchem Zweck und auf welcher rechtlichen Grundlage wir Ihre Daten verarbeiten.',
                'privacy_controller' => 'Verantwortlicher',
                'privacy_general_info' => 'Allgemeine Informationen',
                'privacy_ssl_encryption' => 'SSL oder TLS Verschlüsselung',
                'privacy_ssl_encryption_desc' => 'Wenn Sie Daten auf Websites eingeben, Online-Bestellungen aufgeben oder E-Mails über das Internet senden, müssen Sie immer damit rechnen, dass unbefugte Dritte auf Ihre Daten zugreifen. Es gibt keinen vollständigen Schutz vor einem solchen Zugriff. Wir tun jedoch unser Möglichstes, um Ihre Daten bestmöglich zu schützen und Sicherheitslücken soweit wie möglich zu schließen.',
                'privacy_ssl_encryption_how' => 'Ein wichtiger Schutzmechanismus ist die SSL oder TLS Verschlüsselung unserer Website, die sicherstellt, dass Daten, die Sie an uns übertragen, von Dritten nicht gelesen werden können. Sie erkennen die Verschlüsselung am Schloss-Symbol vor der in Ihrem Browser eingegebenen Internetadresse und daran, dass unsere Internetadresse mit https:// und nicht mit http:// beginnt.',
                'privacy_data_retention' => 'Datenspeicherung',
                'privacy_data_retention_desc' => 'In einigen Teilen dieser Datenschutzrichtlinie informieren wir Sie darüber, wie lange wir oder die Unternehmen, die Ihre Daten in unserem Auftrag verarbeiten, Ihre Daten speichern. In Ermangelung solcher Informationen speichern wir Ihre Daten, bis der Zweck der Datenverarbeitung nicht mehr zutrifft, Sie der Datenverarbeitung widersprechen oder Sie Ihre Einwilligung zur Datenverarbeitung widerrufen.',
                'privacy_data_retention_exceptions' => 'Im Falle eines Widerspruchs oder Widerrufs können wir Ihre Daten jedoch weiterhin verarbeiten, wenn mindestens eine der folgenden Bedingungen erfüllt ist: Wir haben zwingende berechtigte Gründe für die weitere Verarbeitung der Daten, die Ihre Interessen, Rechte und Freiheiten überwiegen; Die Datenverarbeitung ist zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen erforderlich; Wir sind gesetzlich verpflichtet, Ihre Daten aufzubewahren.',
                'privacy_your_rights' => 'Ihre Rechte',
                'privacy_objection' => 'Widerspruch gegen die Datenverarbeitung',
                'privacy_objection_important' => 'WENN IN DIESER DATENSCHUTZERKLÄRUNG STEHEN, DASS WIR BERECHTIGTE INTERESSEN FÜR DIE VERARBEITUNG IHRER DATEN HABEN UND DIESE VERARBEITUNG DAHER AUF ART. 6 ABS. 1 SATZ 1 LIT. F) DSGVO BERUHT, HABEN SIE DAS RECHT, GEMÄSS ART. 21 DSGVO WIDERSPRUCH EINZULEGEN.',
                'privacy_objection_desc' => 'Dies gilt auch für Profiling, das auf der Grundlage der vorgenannten Bestimmung durchgeführt wird. Voraussetzung ist, dass Sie Gründe für den Widerspruch angeben, die sich aus Ihrer besonderen Situation ergeben. Keine Gründe sind erforderlich, wenn sich der Widerspruch gegen die Verwendung Ihrer Daten für Direktwerbung richtet.',
                'privacy_objection_exceptions' => 'Die Folge des Widerspruchs ist, dass wir Ihre Daten nicht mehr verarbeiten dürfen. Dies gilt nur dann nicht, wenn wir zwingende berechtigte Gründe für die Verarbeitung nachweisen können, die Ihre Interessen, Rechte und Freiheiten überwiegen, oder wenn die Verarbeitung zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen erforderlich ist.',
                'privacy_withdrawal' => 'Widerruf der Einwilligung',
                'privacy_withdrawal_desc' => 'Viele Datenverarbeitungsvorgänge basieren auf Ihrer Einwilligung. Sie können diese Einwilligung jederzeit ohne Angabe von Gründen widerrufen (Art. 7 Abs. 3 DSGVO). Ab dem Zeitpunkt des Widerrufs dürfen wir Ihre Daten dann nicht mehr verarbeiten.',
                'privacy_complaint' => 'Beschwerderecht',
                'privacy_complaint_desc' => 'Wenn Sie der Ansicht sind, dass wir gegen die Datenschutz-Grundverordnung (DSGVO) verstoßen, haben Sie das Recht, gemäß Art. 77 DSGVO bei einer Aufsichtsbehörde Beschwerde einzulegen.',
                'privacy_portability' => 'Datenübertragbarkeit',
                'privacy_portability_desc' => 'Wir müssen Daten, die wir automatisch auf der Grundlage Ihrer Einwilligung oder zur Erfüllung eines Vertrags verarbeiten, Ihnen oder einem Dritten in einem gängigen maschinenlesbaren Format aushändigen, wenn Sie dies verlangen.',
                'privacy_correction' => 'Auskunft, Löschung und Berichtigung',
                'privacy_correction_desc' => 'Gemäß Art. 15 DSGVO haben Sie das Recht, unentgeltlich Auskunft darüber zu erhalten, welche Ihrer persönlichen Daten wir gespeichert haben. Wenn die Daten unrichtig sind, haben Sie ein Recht auf Berichtigung (Art. 16 DSGVO), und unter den Voraussetzungen des Art. 17 DSGVO können Sie verlangen, dass wir die Daten löschen.',
                'privacy_data_collection' => 'Datenerhebung',
                'privacy_cookies' => 'Verwendung von Cookies',
                'privacy_cookies_desc' => 'Unsere Website setzt Cookies auf Ihrem Gerät. Dies sind kleine Textdateien, die für verschiedene Zwecke verwendet werden. Einige Cookies sind technisch notwendig, damit die Website überhaupt funktioniert (notwendige Cookies). Andere werden benötigt, um bestimmte Aktionen oder Funktionen auf der Website auszuführen (funktionale Cookies).',
                'privacy_cookies_necessary' => 'Notwendige Cookies',
                'privacy_cookies_necessary_desc' => 'Technisch erforderlich für die Website-Funktionalität',
                'privacy_cookies_functional' => 'Funktionale Cookies',
                'privacy_cookies_functional_desc' => 'Ermöglichen spezifische Funktionen und Aktionen',
                'privacy_cookies_analytics' => 'Analyse-Cookies',
                'privacy_cookies_analytics_desc' => 'Analysieren das Nutzerverhalten und optimieren',
                'privacy_server_logs' => 'Server-Logdateien',
                'privacy_server_logs_desc' => 'Server-Logdateien protokollieren alle Anfragen und Zugriffe auf unsere Website und zeichnen Fehlermeldungen auf. Sie enthalten auch persönliche Daten, insbesondere Ihre IP-Adresse. Diese wird jedoch vom Anbieter nach kurzer Zeit anonymisiert.',
                'privacy_server_logs_data' => 'In Server-Logs gesammelte Daten:',
                'privacy_logs_browser' => 'Browser-Typ und Version',
                'privacy_logs_os' => 'Verwendetes Betriebssystem',
                'privacy_logs_referrer' => 'Referrer-URL',
                'privacy_logs_hostname' => 'Hostname des zugreifenden Computers',
                'privacy_logs_time' => 'Zeitpunkt der Serveranfrage',
                'privacy_logs_ip' => 'IP-Adresse (bei Bedarf anonymisiert)',
                'privacy_contact_registration' => 'Kontakt und Registrierung',
                'privacy_contact_methods' => 'Kontaktmethoden',
                'privacy_contact_methods_desc' => 'Sie können uns eine Nachricht per E-Mail oder Fax senden oder uns anrufen.',
                'privacy_contact_processing' => 'Wie wir Ihre Daten verarbeiten',
                'privacy_contact_processing_desc' => 'Wir speichern Ihre Nachricht sowie Ihre Kontaktdaten, um Ihre Anfrage einschließlich Nachfragen bearbeiten zu können. Wir geben die Daten nicht ohne Ihre Zustimmung an andere Personen weiter.',
                'privacy_registration' => 'Registrierungsfunktion',
                'privacy_registration_desc' => 'Um bestimmte Funktionen oder Angebote auf unserer Website zu nutzen, müssen Sie sich registrieren. Dafür müssen Sie Ihre E-Mail-Adresse und möglicherweise andere persönliche Daten angeben.',
                'privacy_registration_purpose' => 'Zweck der Registrierungsdaten',
                'privacy_registration_purpose_desc' => 'Wir speichern die Daten, die Sie bei der Registrierung angeben, und verwenden sie, um Ihnen die Funktion oder das Angebot zu Verfügung zu stellen, für das Sie sich registriert haben.',
                'privacy_third_party' => 'Drittanbieter-Dienste',
                'privacy_youtube_desc' => 'Sie können YouTube-Videos auf unserer Website ansehen. Dabei sammelt und speichert Google als Anbieter von YouTube bestimmte Informationen über Sie. Da wir YouTube im erweiterten Datenschutzmodus verwenden, geschieht dies nur, wenn Sie ein Video starten.',
                'privacy_youtube_processing' => 'Wie YouTube Ihre Daten verarbeitet',
                'privacy_youtube_processing_desc' => 'Den Servern von Google wird mitgeteilt, welche unserer Seiten von Ihrem Gerät aus besucht wurden. Wenn Sie beim Surfen in Ihrem YouTube-Konto angemeldet sind, kann Google den Besuch unserer Website Ihrem persönlichen Profil zuordnen.',
                'privacy_hosting' => 'Hosting und CDN',
                'privacy_hosting_desc' => 'Unsere Website wird auf einem Server des folgenden Internetdienstanbieters (Hoster) gehostet. Der Hoster speichert alle Daten von unserer Website. Dies umfasst alle persönlichen Daten, die automatisch oder durch Eingabe gesammelt werden.',
                'privacy_contact_info' => 'Kontaktinformationen',
                'privacy_questions' => 'Fragen zum Datenschutz?',
                'privacy_contact_us' => 'Wenn Sie Fragen zu dieser Datenschutzrichtlinie oder zur Verarbeitung Ihrer Daten haben, kontaktieren Sie uns bitte:',
                
                // Impressum
                'impressum_title' => 'Impressum',
                'impressum_subtitle' => 'Informationen über das Unternehmen und rechtliche Anforderungen',
                'impressum_description' => 'Impressum für Playlist Manager - Unternehmensinformationen und rechtliche Anforderungen',
                'impressum_legal_info' => 'Rechtliche Informationen',
                'impressum_company' => 'Unternehmen',
                'impressum_contact' => 'Kontakt',
                'impressum_management' => 'Geschäftsführung',
                'impressum_managing_director' => 'Geschäftsführer',
                'impressum_managing_director_desc' => 'Verantwortlich für die Geschäftsführung des Unternehmens',
                'impressum_supervisory_board' => 'Aufsichtsrat',
                'impressum_supervisory_board_desc' => 'Überwacht die Geschäftsführung des Unternehmens',
                'impressum_registration' => 'Registerinformationen',
                'impressum_court' => 'Registergericht',
                'impressum_registration_number' => 'Registernummer',
                'impressum_tax_id' => 'Steuernummer',
                'impressum_vat_id' => 'Umsatzsteuer-ID',
                'impressum_professional_info' => 'Berufliche Informationen',
                'impressum_professional_title' => 'Berufsbezeichnung',
                'impressum_professional_title_desc' => 'Informationen über berufliche Qualifikationen und Titel',
                'impressum_professional_authority' => 'Berufsaufsichtsbehörde',
                'impressum_professional_authority_desc' => 'Zuständige Berufsaufsichtsbehörde für die Überwachung',
                'impressum_professional_regulation' => 'Berufsordnung',
                'impressum_professional_regulation_desc' => 'Anwendbare Berufsordnungen und Standards',
                'impressum_disclaimer' => 'Haftungsausschluss',
                'impressum_disclaimer_content' => 'Die auf dieser Website bereitgestellten Informationen dienen nur zu allgemeinen Informationszwecken. Obwohl wir uns bemühen, die Informationen aktuell und korrekt zu halten, geben wir keine Zusicherungen oder Garantien jeglicher Art, ausdrücklich oder stillschweigend, über die Vollständigkeit, Genauigkeit, Zuverlässigkeit, Eignung oder Verfügbarkeit der Informationen, Produkte, Dienstleistungen oder zugehörigen Grafiken auf der Website für jeden Zweck.',
                'impressum_liability' => 'Haftung',
                'impressum_liability_content' => 'In keinem Fall haften wir für Verluste oder Schäden einschließlich, aber nicht beschränkt auf, indirekte oder Folgeschäden, die sich aus Datenverlust oder Gewinnverlust ergeben, die aus der Nutzung dieser Website entstehen oder damit zusammenhängen.',
                
                // Admin Panel
                'admin_panel' => 'Admin-Panel',
                'user_management' => 'Benutzerverwaltung',
                'system_settings' => 'Systemeinstellungen',
                'recent_activity' => 'Letzte Aktivitäten',
                'total_users' => 'Gesamte Benutzer',
                'active_users' => 'Aktive Benutzer',
                'admin_users' => 'Admin-Benutzer',
                'logged_in_as' => 'Angemeldet als',
                'user' => 'Benutzer',
                'role' => 'Rolle',
                'status' => 'Status',
                'actions' => 'Aktionen',
                'edit_user' => 'Benutzer bearbeiten',
                'delete_user' => 'Benutzer löschen',
                'user_updated_successfully' => 'Benutzer erfolgreich aktualisiert',
                'user_deleted_successfully' => 'Benutzer erfolgreich gelöscht',
                'setting_updated_successfully' => 'Einstellung erfolgreich aktualisiert',
                'update_error' => 'Aktualisierungsfehler aufgetreten',
                'delete_error' => 'Löschfehler aufgetreten',
                'access_denied' => 'Zugriff verweigert',
                'account_locked' => 'Konto aufgrund zu vieler fehlgeschlagener Anmeldeversuche gesperrt',
                'passwords_dont_match' => 'Passwörter stimmen nicht überein',
                'registration_disabled' => 'Benutzerregistrierung ist derzeit deaktiviert',
                'email_taken' => 'E-Mail-Adresse ist bereits vergeben',
                'username' => 'Benutzername',
                'enter_your_email' => 'Geben Sie Ihre E-Mail-Adresse ein',
                
                // Music Player
                'music_player' => 'Musik-Player',
                'spotify_player' => 'Spotify Player',
                'spotify_connected_successfully' => 'Spotify erfolgreich verbunden',
                'select_playlist' => 'Playlist auswählen',
                'choose_playlist' => 'Playlist wählen',
                'play' => 'Abspielen',
                'pause' => 'Pause',
                'now_playing' => 'Spielt jetzt',
                'playlist_management' => 'Playlist-Verwaltung',
                'create_playlist' => 'Playlist erstellen',
                'generate_playlist' => 'Playlist generieren',
                'import_playlist' => 'Playlist importieren',
                'import' => 'Importieren',
                'time_range' => 'Zeitraum',
                'last_4_weeks' => 'Letzte 4 Wochen',
                'last_6_months' => 'Letzte 6 Monate',
                'all_time' => 'Gesamte Zeit',
                'save_settings' => 'Einstellungen speichern',
                'quick_actions' => 'Schnellaktionen',
                'open_spotify' => 'Spotify öffnen',
                'refresh_playlists' => 'Playlists aktualisieren',
                'back_to_player' => 'Zurück zum Player',
                'please_select_playlist' => 'Bitte wählen Sie eine Playlist aus',
                'playback_error' => 'Wiedergabefehler aufgetreten',
                'create_playlist_feature' => 'Playlist-Erstellungsfunktion kommt bald',
                'import_playlist_feature' => 'Playlist-Importfunktion kommt bald',
                'next_track_feature' => 'Nächster-Track-Funktion kommt bald',
                'settings_saved' => 'Einstellungen erfolgreich gespeichert',
                'open_player' => 'Player öffnen',
                'sign_up' => 'Registrieren',
                
                // Player Controls
                'playback_controls' => 'Wiedergabe-Steuerung',
                'volume' => 'Lautstärke',
                'mute' => 'Stummschalten',
                'unmute' => 'Stummschaltung aufheben',
                'shuffle_play' => 'Zufällige Wiedergabe',
                'repeat_one' => 'Einen wiederholen',
                'repeat_all' => 'Alle wiederholen',
                'repeat_off' => 'Wiederholung aus',
                'previous_track' => 'Vorheriger Track',
                'next_track' => 'Nächster Track',
                'seek_forward' => 'Vorspulen',
                'seek_backward' => 'Zurückspulen',
                
                // Playlist Features
                'add_to_playlist' => 'Zur Playlist hinzufügen',
                'remove_from_playlist' => 'Von Playlist entfernen',
                'playlist_info' => 'Playlist-Info',
                'playlist_tracks' => 'Playlist-Tracks',
                'playlist_duration' => 'Playlist-Dauer',
                'playlist_owner' => 'Playlist-Besitzer',
                'playlist_public' => 'Öffentliche Playlist',
                'playlist_private' => 'Private Playlist',
                'playlist_collaborative' => 'Kollaborative Playlist',
                
                // User Interface
                'search' => 'Suchen',
                'search_placeholder' => 'Nach Songs, Künstlern oder Playlists suchen',
                'filter' => 'Filter',
                'sort_by' => 'Sortieren nach',
                'sort_name' => 'Name',
                'sort_date' => 'Datum',
                'sort_popularity' => 'Beliebtheit',
                'sort_duration' => 'Dauer',
                'sort_artist' => 'Künstler',
                'sort_album' => 'Album',
                'ascending' => 'Aufsteigend',
                'descending' => 'Absteigend',
                
                // Notifications
                'notification' => 'Benachrichtigung',
                'notifications' => 'Benachrichtigungen',
                'mark_as_read' => 'Als gelesen markieren',
                'mark_all_as_read' => 'Alle als gelesen markieren',
                'no_notifications' => 'Keine Benachrichtigungen',
                'new_notification' => 'Neue Benachrichtigung',
                
                // Error Messages
                'error_occurred' => 'Ein Fehler ist aufgetreten',
                'try_again' => 'Erneut versuchen',
                'connection_error' => 'Verbindungsfehler',
                'timeout_error' => 'Zeitüberschreitung',
                'server_error' => 'Serverfehler',
                'not_found' => 'Nicht gefunden',
                'unauthorized' => 'Nicht autorisiert',
                'forbidden' => 'Verboten',
                'too_many_requests' => 'Zu viele Anfragen',
                
                // Success Messages
                'operation_successful' => 'Vorgang erfolgreich',
                'changes_saved' => 'Änderungen gespeichert',
                'item_added' => 'Element hinzugefügt',
                'item_removed' => 'Element entfernt',
                'item_updated' => 'Element aktualisiert',
                'item_deleted' => 'Element gelöscht',
                
                // Confirmation Dialogs
                'confirm_action' => 'Aktion bestätigen',
                'confirm_delete' => 'Löschen bestätigen',
                'confirm_logout' => 'Abmeldung bestätigen',
                'confirm_unsaved_changes' => 'Ungespeicherte Änderungen bestätigen',
                'are_you_sure' => 'Sind Sie sicher?',
                'this_action_cannot_be_undone' => 'Diese Aktion kann nicht rückgängig gemacht werden',
                'you_have_unsaved_changes' => 'Sie haben ungespeicherte Änderungen',
                
                // Time and Date
                'today' => 'Heute',
                'yesterday' => 'Gestern',
                'tomorrow' => 'Morgen',
                'this_week' => 'Diese Woche',
                'last_week' => 'Letzte Woche',
                'this_month' => 'Dieser Monat',
                'last_month' => 'Letzter Monat',
                'this_year' => 'Dieses Jahr',
                'last_year' => 'Letztes Jahr',
                'minutes_ago' => 'Minuten her',
                'hours_ago' => 'Stunden her',
                'days_ago' => 'Tage her',
                'weeks_ago' => 'Wochen her',
                'months_ago' => 'Monate her',
                'years_ago' => 'Jahre her',
                
                // Statistics
                'total_playtime' => 'Gesamte Wiedergabezeit',
                'total_tracks' => 'Gesamte Tracks',
                'total_playlists' => 'Gesamte Playlists',
                'favorite_artists' => 'Lieblingskünstler',
                'favorite_genres' => 'Lieblingsgenres',
                'listening_history' => 'Hörverlauf',
                'recent_activity' => 'Letzte Aktivitäten',
                'top_tracks' => 'Top Tracks',
                'top_artists' => 'Top Künstler',
                'top_albums' => 'Top Alben',
                
                // Platform Specific
                'spotify_web_player' => 'Spotify Web Player',
                'apple_music_web_player' => 'Apple Music Web Player',
                'youtube_music_web_player' => 'YouTube Music Web Player',
                'amazon_music_web_player' => 'Amazon Music Web Player',
                'connect_account' => 'Konto verbinden',
                'disconnect_account' => 'Konto trennen',
                'account_connected' => 'Konto verbunden',
                'account_disconnected' => 'Konto getrennt',
                'reconnect_account' => 'Konto wieder verbinden',
                'account_expired' => 'Konto abgelaufen',
                'refresh_token' => 'Token erneuern',
                'token_refreshed' => 'Token erneuert',
                'token_refresh_failed' => 'Token-Erneuerung fehlgeschlagen',
                
                // Additional Player Interface
                'open_external' => 'Extern öffnen',
                'player_controls' => 'Player-Steuerung',
                'select_platform' => 'Plattform auswählen',
                'choose_platform' => 'Plattform wählen',
                'please_select_platform' => 'Bitte wählen Sie eine Plattform aus',
                'open_settings' => 'Einstellungen öffnen',
                
                // Admin Panel Additional
                'last_login' => 'Letzte Anmeldung'
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