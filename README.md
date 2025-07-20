# 🎵 Playlist Manager

A sophisticated multi-platform music streaming automation and playlist management system built with PHP. This application allows users to manage and automate music playback across multiple streaming platforms with intelligent scheduling and analytics.

## 🌟 Features

### 🎧 Multi-Platform Support
- **Spotify** - Full integration with Spotify Web API
- **Apple Music** - Apple MusicKit integration
- **YouTube Music** - YouTube Music API support
- **Amazon Music** - Amazon Music integration

### 🔐 User Management
- Secure user registration and authentication
- Team-based user organization
- Office location management
- User preferences and settings

### ⏰ Intelligent Scheduling
- Customizable play schedules
- Random day and time selection
- Time range configuration
- Automatic playlist management

### 📊 Analytics & Statistics
- Listening history tracking
- Platform usage statistics
- User behavior analytics
- Performance metrics

### 🌍 Multi-Language Support
- German (Deutsch)
- English
- Easy language switching
- Localized content

## 🏗️ Technology Stack

### Backend
- **PHP 8+** with modern OOP practices
- **MySQL Database** for user data and statistics
- **Composer** for dependency management
- **Session-based authentication** with security features

### Frontend
- **Modern CSS** with Tailwind CSS framework
- **Responsive design** with mobile-first approach
- **Font Awesome** icons
- **Chart.js** for data visualization

### APIs & Integrations
- **Spotify Web API** via `jwilsson/spotify-web-api-php`
- **Apple Music API** via `pouler/apple-music-api`
- **YouTube Music API** via `ytmusicapi`
- **Google APIs** for additional services

## 📁 Project Structure

```
website playlist-manager/
├── assets/
│   ├── css/
│   │   ├── main.css              # Custom styles
│   │   └── fontawesome-all.min.css
│   ├── js/
│   │   └── main.js               # Main JavaScript functionality
│   └── webfonts/                 # Font Awesome fonts
├── components/
│   ├── header.php                # Site header with navigation
│   ├── footer.php                # Site footer
│   └── language_switcher.php     # Language switching component
├── database/
│   └── schema.sql                # Database schema
├── images/                       # Static images and icons
├── script/
│   ├── inc_start.php             # Application initialization
│   ├── languages.php             # Language management system
│   ├── language_utils.php        # Language utility functions
│   ├── accounts.php              # User account management
│   ├── signup.php                # User registration
│   ├── logout.php                # User logout
│   ├── Spotify.php               # Spotify integration
│   ├── AppleMusic.php            # Apple Music integration
│   ├── vendor/                   # Composer dependencies
│   └── ytmusicapi/               # YouTube Music API
├── index.php                     # Homepage
├── login.php                     # Login page
├── signup.php                    # Registration page
├── account.php                   # User dashboard
├── spotify_play.php              # Spotify management
├── applemusic_play.php           # Apple Music management
├── amazon_play.php               # Amazon Music management
├── datenschutz.php               # Privacy policy
├── test_language.php             # Language testing page
├── LANGUAGE_SYSTEM.md            # Language system documentation
├── composer.json                 # PHP dependencies
└── README.md                     # This file
```

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer

### 1. Clone the Repository
```bash
git clone <repository-url>
cd website-playlist-manager
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Database Setup
```bash
# Import the database schema
mysql -u your_username -p < database/schema.sql
```

### 4. Configuration
Update the database connection settings in `script/inc_start.php`:
```php
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "your_database_name";
```

### 5. Platform API Setup

#### Spotify
1. Create a Spotify Developer account
2. Create a new application
3. Add your client ID and secret to the configuration

#### Apple Music
1. Register for Apple Developer Program
2. Create a MusicKit key
3. Configure the integration

#### YouTube Music
1. Set up Google Cloud Project
2. Enable YouTube Data API
3. Configure API credentials

### 6. Web Server Configuration
Ensure your web server is configured to serve PHP files and has the necessary permissions.

## 🔧 Configuration

### Environment Variables
Create a `.env` file in the root directory:
```env
DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password

SPOTIFY_CLIENT_ID=your_spotify_client_id
SPOTIFY_CLIENT_SECRET=your_spotify_client_secret

APPLE_MUSIC_KEY=your_apple_music_key
YOUTUBE_API_KEY=your_youtube_api_key
```

### Language Configuration
The application supports multiple languages. Language files are located in `script/languages.php`. To add a new language:

1. Add the language code to the `$supportedLanguages` array
2. Add translations to the `$translations` array
3. Update the language detection logic if needed

## 📖 Usage

### User Registration
1. Visit the signup page
2. Fill in your details (username, password, team, office)
3. Accept terms and conditions
4. Click "Create Account"

### Platform Connection
1. Log in to your account
2. Navigate to the desired platform page (Spotify, Apple Music, etc.)
3. Click "Connect" and follow the authorization process
4. Configure your playlist settings

### Schedule Management
1. Go to your account dashboard
2. Set your preferred playing days and times
3. Enable/disable random scheduling
4. Save your preferences

### Language Switching
- Use the language switcher in the header or footer
- Languages are automatically detected based on browser settings
- Language preference is saved in the session

## 🔒 Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **SQL Injection Prevention**: Prepared statements for all database queries
- **Session Security**: Secure session handling with proper cleanup
- **Input Validation**: Comprehensive input validation and sanitization
- **CSRF Protection**: Cross-site request forgery protection
- **XSS Prevention**: Output escaping and content security policies

## 🧪 Testing

### Language System Testing
Visit `test_language.php` to test the language switching functionality.

### Database Testing
Run the database schema to ensure all tables are created correctly.

### API Testing
Test platform integrations by connecting to each service.

## 📝 API Documentation

### Authentication Endpoints
- `POST /login.php` - User login
- `POST /signup.php` - User registration
- `GET /script/logout.php` - User logout

### Platform Endpoints
- `GET /spotify_play.php` - Spotify management
- `GET /applemusic_play.php` - Apple Music management
- `GET /amazon_play.php` - Amazon Music management

### User Management
- `GET /account.php` - User dashboard
- `GET /editaccount.php` - Account settings

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Check the documentation in `LANGUAGE_SYSTEM.md`
- Review the code comments
- Create an issue in the repository

## 🔄 Changelog

### Version 2.0.0 (Current)
- Complete language system revamp
- Enhanced security features
- Improved user interface
- Better error handling
- Comprehensive documentation

### Version 1.0.0
- Initial release
- Basic playlist management
- Multi-platform support
- User authentication

## 🙏 Acknowledgments

- Spotify Web API for music integration
- Apple MusicKit for Apple Music support
- YouTube Data API for YouTube Music
- Tailwind CSS for styling
- Font Awesome for icons
- Chart.js for data visualization

---

**Made with ❤️ for music lovers everywhere** 