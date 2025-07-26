<?php
/**
 * Language Utilities
 * Common functions for language handling across the application
 */

// Helper function to build language URLs (using query parameters)
function buildLanguageUrlQuery($language) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $parsedUrl = parse_url($currentUrl);
    $path = $parsedUrl['path'];
    $query = [];
    
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $query);
    }
    
    $query['lang'] = $language;
    
    return $path . '?' . http_build_query($query);
}

// Helper function to get current page URL without language parameter
function getCurrentPageUrl() {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $parsedUrl = parse_url($currentUrl);
    $path = $parsedUrl['path'];
    $query = [];
    
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $query);
    }
    
    // Remove language parameter
    unset($query['lang']);
    
    return $path . (!empty($query) ? '?' . http_build_query($query) : '');
}

// Helper function to check if language parameter is set
function hasLanguageParameter() {
    return isset($_GET['lang']);
}

// Helper function to get language from URL or session
function getLanguageFromRequest() {
    if (isset($_GET['lang'])) {
        return $_GET['lang'];
    }
    
    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    }
    
    return null;
}
?> 