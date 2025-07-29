<?php
/**
 * OAuth Configuration Example
 * Copy this file to oauth_config.php and fill in your actual OAuth credentials
 */

// OAuth Configuration
// Set these environment variables or modify the OAuthManager to use a config file

// Google OAuth (for user login and YouTube)
// GOOGLE_CLIENT_ID=your_google_client_id_here
// GOOGLE_CLIENT_SECRET=your_google_client_secret_here

// Apple OAuth (for user login)
// APPLE_CLIENT_ID=your_apple_client_id_here
// APPLE_CLIENT_SECRET=your_apple_client_secret_here
// APPLE_KEY_ID=your_apple_key_id_here
// APPLE_TEAM_ID=your_apple_team_id_here
// APPLE_PRIVATE_KEY_PATH=/path/to/your/apple_private_key.p8

// Spotify OAuth
// SPOTIFY_CLIENT_ID=your_spotify_client_id_here
// SPOTIFY_CLIENT_SECRET=your_spotify_client_secret_here

// Apple Music OAuth
// APPLE_MUSIC_CLIENT_ID=your_apple_music_client_id_here
// APPLE_MUSIC_CLIENT_SECRET=your_apple_music_client_secret_here
// APPLE_MUSIC_KEY_ID=your_apple_music_key_id_here
// APPLE_MUSIC_TEAM_ID=your_apple_music_team_id_here
// APPLE_MUSIC_PRIVATE_KEY_PATH=/path/to/your/apple_music_private_key.p8

// YouTube OAuth
// YOUTUBE_CLIENT_ID=your_youtube_client_id_here
// YOUTUBE_CLIENT_SECRET=your_youtube_client_secret_here

// Amazon OAuth
// AMAZON_CLIENT_ID=your_amazon_client_id_here
// AMAZON_CLIENT_SECRET=your_amazon_client_secret_here

/*
 * Setup Instructions:
 * 
 * 1. Google OAuth:
 *    - Go to https://console.developers.google.com/
 *    - Create a new project or select existing one
 *    - Enable YouTube Data API v3
 *    - Create OAuth 2.0 credentials
 *    - Add authorized redirect URIs: https://your-domain.com/oauth_callback.php?provider=google
 * 
 * 2. Apple OAuth:
 *    - Go to https://developer.apple.com/
 *    - Create an App ID
 *    - Enable Sign In with Apple
 *    - Create a Services ID
 *    - Generate a private key
 *    - Add redirect URI: https://your-domain.com/oauth_callback.php?provider=apple
 * 
 * 3. Spotify OAuth:
 *    - Go to https://developer.spotify.com/dashboard/
 *    - Create a new app
 *    - Add redirect URI: https://your-domain.com/oauth_callback.php?provider=spotify
 * 
 * 4. Apple Music OAuth:
 *    - Similar to Apple OAuth but for music services
 *    - Add redirect URI: https://your-domain.com/oauth_callback.php?provider=apple_music
 * 
 * 5. YouTube OAuth:
 *    - Use the same Google OAuth credentials as Google
 *    - Add redirect URI: https://your-domain.com/oauth_callback.php?provider=youtube
 * 
 * 6. Amazon OAuth:
 *    - Go to https://developer.amazon.com/
 *    - Create a new app
 *    - Add redirect URI: https://your-domain.com/oauth_callback.php?provider=amazon
 * 
 * 7. Set environment variables:
 *    - Option 1: Set in your web server environment
 *    - Option 2: Use a .env file (requires additional library)
 *    - Option 3: Modify OAuthManager to read from a config file
 * 
 * 8. Run database migration:
 *    - Execute the SQL in database/oauth_schema.sql
 * 
 * 9. Test the OAuth flow:
 *    - Try logging in with Google/Apple
 *    - Try connecting a music platform
 *    - Check the logs for any errors
 */
?> 