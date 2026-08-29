<?php
/**
 * Language Learning System Configuration
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'language_learning');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_PORT', 3306);

// Application settings
define('APP_NAME', 'PHP Language Learning System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/php-language-app');
define('APP_ENV', 'development');

// Session settings
define('SESSION_TIMEOUT', 3600);
define('SESSION_NAME', 'language_app_session');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Supported languages
const SUPPORTED_LANGUAGES = [
    'en' => 'English',
    'es' => 'Spanish',
    'fr' => 'French',
    'de' => 'German',
    'it' => 'Italian',
    'pt' => 'Portuguese',
    'ja' => 'Japanese',
    'zh' => 'Chinese',
    'ko' => 'Korean',
    'ru' => 'Russian'
];

// Difficulty levels
const DIFFICULTY_LEVELS = [
    'beginner' => 'Beginner',
    'intermediate' => 'Intermediate',
    'advanced' => 'Advanced',
    'expert' => 'Expert'
];

// Lesson types
const LESSON_TYPES = [
    'vocabulary' => 'Vocabulary',
    'grammar' => 'Grammar',
    'conversation' => 'Conversation',
    'listening' => 'Listening',
    'reading' => 'Reading',
    'writing' => 'Writing'
];

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('UTC');
