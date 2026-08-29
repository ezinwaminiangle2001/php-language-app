<?php
/**
 * Utility Helper Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Redirect if not authenticated
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function isStrongPassword($password) {
    // Minimum 8 characters, at least one uppercase, one lowercase, one number
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $password);
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Convert minutes to readable format
 */
function formatDuration($minutes) {
    if ($minutes < 60) {
        return $minutes . ' min';
    }
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    return $hours . 'h ' . $mins . 'm';
}

/**
 * Get difficulty level color
 */
function getDifficultyColor($level) {
    $colors = [
        'beginner' => '#4CAF50',
        'intermediate' => '#FF9800',
        'advanced' => '#f44336',
        'expert' => '#9C27B0'
    ];
    return $colors[$level] ?? '#999';
}

/**
 * Calculate percentage
 */
function calculatePercentage($completed, $total) {
    if ($total == 0) return 0;
    return round(($completed / $total) * 100, 2);
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
}

/**
 * Get time ago format
 */
function getTimeAgo($datetime) {
    $time_ago = strtotime($datetime);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2419200);
    $years = round($seconds / 29030400);
    
    if ($seconds <= 60) {
        return "just now";
    } else if ($minutes <= 60) {
        return ($minutes == 1) ? "a minute ago" : $minutes . " minutes ago";
    } else if ($hours <= 24) {
        return ($hours == 1) ? "an hour ago" : $hours . " hours ago";
    } else if ($days <= 7) {
        return ($days == 1) ? "yesterday" : $days . " days ago";
    } else if ($weeks <= 4.3) {
        return ($weeks == 1) ? "a week ago" : $weeks . " weeks ago";
    } else if ($months <= 12) {
        return ($months == 1) ? "a month ago" : $months . " months ago";
    } else {
        return ($years == 1) ? "a year ago" : $years . " years ago";
    }
}

/**
 * Log activity
 */
function logActivity($userId, $action, $description = '') {
    $logFile = __DIR__ . '/../logs/activity.log';
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    $log = date('Y-m-d H:i:s') . " | User: $userId | Action: $action | Description: $description" . PHP_EOL;
    file_put_contents($logFile, $log, FILE_APPEND);
}

/**
 * Send JSON response
 */
function sendJsonResponse($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

/**
 * Get user's learning streak
 */
function getLearningStreak($userId) {
    $db = \Config\Database::getInstance();
    $query = 'SELECT current_streak, longest_streak FROM user_streak WHERE user_id = ?';
    $result = $db->fetch($query, [$userId]);
    return $result ?? ['current_streak' => 0, 'longest_streak' => 0];
}

/**
 * Update learning streak
 */
function updateLearningStreak($userId) {
    $db = \Config\Database::getInstance();
    $today = date('Y-m-d');
    
    $streak = getLearningStreak($userId);
    $lastActivity = $db->fetch(
        'SELECT last_activity_date FROM user_streak WHERE user_id = ?',
        [$userId]
    );
    
    if ($lastActivity) {
        $lastDate = date('Y-m-d', strtotime($lastActivity['last_activity_date']));
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        if ($lastDate == $today) {
            return; // Already counted today
        } else if ($lastDate == $yesterday) {
            $newStreak = $streak['current_streak'] + 1;
            $longest = max($streak['longest_streak'], $newStreak);
        } else {
            $newStreak = 1;
            $longest = $streak['longest_streak'];
        }
    } else {
        $newStreak = 1;
        $longest = 1;
    }
    
    $db->update(
        'user_streak',
        ['current_streak' => $newStreak, 'longest_streak' => $longest, 'last_activity_date' => $today],
        'user_id = ?',
        [$userId]
    );
}
