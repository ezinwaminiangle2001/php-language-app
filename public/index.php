<?php
/**
 * Public index.php - Entry point for the application
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/helpers.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Also autoload Config namespace
spl_autoload_register(function ($class) {
    $prefix = 'Config\\';
    $base_dir = __DIR__ . '/../config/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Set error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    logActivity(getCurrentUserId() ?? 'unknown', 'ERROR', $errstr);
    
    if (APP_ENV === 'production') {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    } else {
        echo json_encode(['error' => $errstr, 'file' => $errfile, 'line' => $errline]);
    }
    return true;
});

// Check if request is for API
if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
    require __DIR__ . '/../api/routes.php';
} else {
    // Serve static pages or redirect
    header('Location: /index.html');
    exit();
}
