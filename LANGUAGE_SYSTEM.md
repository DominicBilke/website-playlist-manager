# Language System Documentation

## Overview

The Playlist Manager application supports multiple languages (German and English) with a comprehensive translation system. The language system is designed to be easy to use and maintain.

## Files Structure

```
script/
├── languages.php          # Main language manager class
├── language_utils.php     # Utility functions for language handling
└── inc_start.php         # Application initialization

components/
├── header.php            # Header with language switcher
├── footer.php            # Footer with language links
└── language_switcher.php # Standalone language switcher component
```

## How to Use

### 1. Basic Usage

Include the language system in your PHP files:

```php
<?php
require 'script/inc_start.php';
require 'script/languages.php';
require 'script/language_utils.php';

// The global $lang object is now available
echo $lang->get('home'); // Returns "Home" or "Startseite"
?>
```

### 2. Getting Translations

```php
// Basic translation
echo $lang->get('dashboard');

// Translation with parameters
echo $lang->get('playing_seconds', ['seconds' => '120']);

// Get current language
$currentLang = $lang->getCurrentLanguage(); // Returns 'en' or 'de'

// Get language name
$langName = $lang->getLanguageName('de'); // Returns 'Deutsch'
```

### 3. Language Switching

The language can be changed via URL parameter:

```
https://yoursite.com/page.php?lang=de
https://yoursite.com/page.php?lang=en
```

The language switcher in the header automatically handles this.

### 4. Building Language URLs

```php
// Build URL for specific language
$englishUrl = buildLanguageUrl('en');
$germanUrl = buildLanguageUrl('de');

// Get current page URL without language parameter
$currentPage = getCurrentPageUrl();
```

## Adding New Translations

### 1. Add to Language Manager

Edit `script/languages.php` and add new keys to both language arrays:

```php
'en' => [
    // ... existing translations
    'new_key' => 'English translation',
],
'de' => [
    // ... existing translations
    'new_key' => 'German translation',
]
```

### 2. Use in Templates

```php
<?php echo $lang->get('new_key'); ?>
```

## Language Detection

The system detects the user's preferred language in this order:

1. URL parameter (`?lang=de`)
2. Session stored language
3. Browser language preference
4. Default language (English)

## Components

### Header Language Switcher

The header includes a dropdown language switcher that:
- Shows current language (EN/DE)
- Provides dropdown with language options
- Maintains current page and parameters when switching

### Footer Language Links

The footer includes simple language links for quick switching.

### Standalone Language Switcher

Use `components/language_switcher.php` to add language switching to any page:

```php
<?php include 'components/language_switcher.php'; ?>
```

## Best Practices

1. **Always use translation keys**: Never hardcode text in templates
2. **Use descriptive keys**: Make keys self-explanatory
3. **Group related translations**: Keep related terms together
4. **Test both languages**: Always test your changes in both languages
5. **Use parameters for dynamic content**: Use `{parameter}` syntax for variable content

## Testing

Use the test page `test_language.php` to verify language functionality:

- Check current language status
- Test translations
- Verify language switching
- Debug URL parameters

## Troubleshooting

### Common Issues

1. **Language not switching**: Check if session is started
2. **Missing translations**: Verify key exists in both language arrays
3. **URL not preserving parameters**: Use `buildLanguageUrl()` function
4. **Header not showing**: Ensure `components/header.php` is included

### Debug Mode

Add this to see language system status:

```php
echo "Current Language: " . $lang->getCurrentLanguage() . "<br>";
echo "Session Language: " . ($_SESSION['language'] ?? 'Not set') . "<br>";
echo "Available Languages: " . implode(', ', $lang->getAvailableLanguages()) . "<br>";
```

## Future Enhancements

- Add more languages (French, Spanish, etc.)
- Implement RTL language support
- Add translation management interface
- Support for language-specific formatting (dates, numbers)
- Automatic translation suggestions 