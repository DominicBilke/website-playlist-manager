# Playlist Manager

A modern, multi-platform music playlist management system with support for Spotify, Apple Music, YouTube Music, and Amazon Music.

## 🚀 Features

- **Multi-Platform Support**: Connect and manage playlists across Spotify, Apple Music, YouTube Music, and Amazon Music
- **Modern Design System**: Clean, responsive interface with a comprehensive component library
- **User Authentication**: Secure login/registration system with role-based access control
- **Admin Panel**: Complete administrative interface for user and system management
- **Multi-Language Support**: Full English and German language support
- **Real-time Playback Control**: Control music playback across all platforms
- **Analytics & Statistics**: Track listening habits and playlist performance
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Custom CSS with modern design system
- **Icons**: Font Awesome 6.0
- **APIs**: Spotify Web API, Apple MusicKit, YouTube IFrame API

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- SSL certificate (recommended for production)
- Composer (for dependency management)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd playlist-manager
   ```

2. **Set up the database**
   - Create a MySQL database
   - Import the schema from `database/schema.sql`
   - Update database credentials in `script/database.php`

3. **Configure the application**
   - Update database connection settings in `script/database.php`
   - Set up platform API credentials (see Platform Configuration below)
   - Configure your web server to point to the project directory

4. **Set permissions**
   ```bash
   chmod 755 -R .
   chmod 777 -R script/vendor/
   ```

5. **Test the installation**
   - Visit `test_project.php` in your browser to verify all components are working
   - Default admin credentials: `admin` / `admin123`

## 🔧 Platform Configuration

### Spotify
1. Create a Spotify Developer account
2. Create a new application
3. Add your redirect URI: `https://yourdomain.com/spotify_play.php`
4. Update credentials in the admin panel

### Apple Music
1. Create an Apple Developer account
2. Generate MusicKit JS credentials
3. Update credentials in the admin panel

### YouTube Music
1. Create a Google Cloud project
2. Enable YouTube Data API v3
3. Create API credentials
4. Update credentials in the admin panel

### Amazon Music
1. Create an Amazon Developer account
2. Set up Amazon Music API access
3. Update credentials in the admin panel

## 📁 Project Structure

```
playlist-manager/
├── assets/
│   ├── css/
│   │   └── main.css          # Main stylesheet with design system
│   └── js/
│       └── main.js           # Main JavaScript file
├── components/
│   ├── header.php            # Site header component
│   ├── footer.php            # Site footer component
│   └── language_switcher.php # Language switching component
├── database/
│   └── schema.sql            # Database schema
├── script/
│   ├── init.php              # Application initialization
│   ├── database.php          # Database configuration
│   ├── auth.php              # Authentication system
│   ├── languages.php         # Language management
│   ├── PlatformManager.php   # Platform integration manager
│   └── vendor/               # Composer dependencies
├── index.php                 # Homepage
├── login.php                 # Login page
├── signup.php                # Registration page
├── account.php               # User dashboard
├── admin.php                 # Admin panel
├── player.php                # Unified music player
├── spotify_play.php          # Spotify player
├── applemusic_play.php       # Apple Music player
├── youtube_play.php          # YouTube Music player
├── amazon_play.php           # Amazon Music player
└── test_project.php          # Project test script
```

## 🎨 Design System

The project uses a modern, custom design system with:

- **Color Palette**: Primary blue, semantic colors (success, error, warning)
- **Typography**: Clean, readable fonts with proper hierarchy
- **Components**: Cards, buttons, forms, alerts, and more
- **Responsive Grid**: Mobile-first responsive design
- **Animations**: Smooth transitions and hover effects
- **Accessibility**: WCAG compliant with proper ARIA labels

## 🔐 Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Input Sanitization**: All user input is properly sanitized
- **Session Security**: Secure session configuration
- **Password Hashing**: Bcrypt password hashing
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Output encoding and sanitization

## 🌐 Multi-Language Support

The application supports English and German with:

- **Language Manager**: Centralized translation management
- **Dynamic Language Switching**: Real-time language changes
- **Comprehensive Translations**: All UI elements translated
- **Fallback System**: Graceful fallback for missing translations

## 📊 Database Schema

The database includes tables for:

- **Users**: User accounts and authentication
- **User Settings**: User preferences and configuration
- **API Tokens**: Platform authentication tokens
- **Listening Stats**: Playback statistics and analytics
- **Admin Audit Log**: Administrative action logging
- **System Settings**: Application configuration

## 🧪 Testing

Run the test script to verify all components:

```bash
# Visit in browser
http://yourdomain.com/test_project.php
```

The test script checks:
- PHP environment
- File system integrity
- Database connection
- Language system
- Authentication system
- Platform manager
- CSS and assets
- Session management
- Database schema
- Security features

## 🚀 Deployment

1. **Production Setup**
   - Set `display_errors = 0` in PHP configuration
   - Enable SSL/HTTPS
   - Set up proper file permissions
   - Configure database backups

2. **Performance Optimization**
   - Enable PHP OPcache
   - Configure web server caching
   - Optimize database queries
   - Minify CSS and JavaScript

3. **Monitoring**
   - Set up error logging
   - Monitor database performance
   - Track application metrics
   - Set up uptime monitoring

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:

1. Check the test script for common issues
2. Review the error logs
3. Verify database connectivity
4. Check platform API credentials
5. Ensure all dependencies are installed

## 🔄 Updates

To update the application:

1. Backup your database and files
2. Pull the latest changes
3. Run database migrations if needed
4. Test the application
5. Update platform credentials if required

---

**Playlist Manager** - Your central platform for managing music across all streaming services. 