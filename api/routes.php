<?php
/**
 * API Routes Configuration
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../Router.php';

use App\Router;
use App\Controllers\AuthController;
use App\Controllers\LessonController;
use App\Controllers\QuizController;
use App\Controllers\ProgressController;
use App\Controllers\VocabularyController;

$router = new Router();

// Authentication Routes
$router->post('/api/auth/register', function() {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new AuthController();
    sendJsonResponse($controller->register($data));
});

$router->post('/api/auth/login', function() {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new AuthController();
    sendJsonResponse($controller->login($data['email'] ?? '', $data['password'] ?? ''));
});

$router->post('/api/auth/logout', function() {
    $controller = new AuthController();
    sendJsonResponse($controller->logout());
});

$router->get('/api/auth/me', function() {
    requireAuth();
    $controller = new AuthController();
    $user = $controller->getCurrentUser();
    sendJsonResponse($user ? ['success' => true, 'user' => $user] : ['success' => false, 'message' => 'Not authenticated']);
});

// Lesson Routes
$router->get('/api/lessons', function() {
    $controller = new LessonController();
    $limit = $_GET['limit'] ?? 50;
    $offset = $_GET['offset'] ?? 0;
    sendJsonResponse($controller->getAllLessons($limit, $offset));
});

$router->get('/api/lessons/{id}', function() {
    $id = $_GET['id'] ?? null;
    $controller = new LessonController();
    sendJsonResponse($controller->getLesson($id));
});

$router->get('/api/lessons/language/{language}', function() {
    $language = $_GET['language'] ?? null;
    $level = $_GET['level'] ?? 'beginner';
    $controller = new LessonController();
    sendJsonResponse($controller->getLessonsByLanguageAndLevel($language, $level));
});

$router->get('/api/lessons/type/{type}', function() {
    $type = $_GET['type'] ?? null;
    $controller = new LessonController();
    sendJsonResponse($controller->getLessonsByType($type));
});

$router->post('/api/lessons', function() {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new LessonController();
    sendJsonResponse($controller->createLesson($data));
});

$router->put('/api/lessons/{id}', function() {
    requireAuth();
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new LessonController();
    sendJsonResponse($controller->updateLesson($id, $data));
});

$router->delete('/api/lessons/{id}', function() {
    requireAuth();
    $id = $_GET['id'] ?? null;
    $controller = new LessonController();
    sendJsonResponse($controller->deleteLesson($id));
});

// Quiz Routes
$router->get('/api/quizzes/{id}', function() {
    $id = $_GET['id'] ?? null;
    $controller = new QuizController();
    sendJsonResponse($controller->getQuiz($id));
});

$router->get('/api/lessons/{lessonId}/quizzes', function() {
    $lessonId = $_GET['lessonId'] ?? null;
    $controller = new QuizController();
    sendJsonResponse($controller->getQuizzesByLesson($lessonId));
});

$router->post('/api/quizzes', function() {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new QuizController();
    sendJsonResponse($controller->createQuiz($data));
});

$router->post('/api/quizzes/{id}/submit', function() {
    requireAuth();
    $quizId = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = getCurrentUserId();
    $controller = new QuizController();
    sendJsonResponse($controller->submitQuiz($userId, $quizId, $data['answers'] ?? []));
});

$router->post('/api/quizzes/{id}/questions', function() {
    requireAuth();
    $quizId = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new QuizController();
    sendJsonResponse($controller->addQuestion($quizId, $data));
});

$router->post('/api/questions/{id}/answers', function() {
    requireAuth();
    $questionId = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new QuizController();
    sendJsonResponse($controller->addAnswer($questionId, $data));
});

$router->put('/api/quizzes/{id}', function() {
    requireAuth();
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new QuizController();
    sendJsonResponse($controller->updateQuiz($id, $data));
});

$router->delete('/api/quizzes/{id}', function() {
    requireAuth();
    $id = $_GET['id'] ?? null;
    $controller = new QuizController();
    sendJsonResponse($controller->deleteQuiz($id));
});

// Vocabulary Routes
$router->get('/api/vocabulary/search', function() {
    $query = $_GET['q'] ?? '';
    $controller = new VocabularyController();
    sendJsonResponse($controller->search($query));
});

$router->get('/api/vocabulary/random', function() {
    $limit = $_GET['limit'] ?? 10;
    $difficulty = $_GET['difficulty'] ?? null;
    $controller = new VocabularyController();
    sendJsonResponse($controller->getRandomVocabulary($limit, $difficulty));
});

$router->get('/api/vocabulary/difficulty/{difficulty}', function() {
    $difficulty = $_GET['difficulty'] ?? 'beginner';
    $controller = new VocabularyController();
    sendJsonResponse($controller->getByDifficulty($difficulty));
});

$router->get('/api/vocabulary/{id}', function() {
    $id = $_GET['id'] ?? null;
    $controller = new VocabularyController();
    sendJsonResponse($controller->getWord($id));
});

$router->get('/api/lessons/{lessonId}/vocabulary', function() {
    $lessonId = $_GET['lessonId'] ?? null;
    $controller = new VocabularyController();
    sendJsonResponse($controller->getVocabularyByLesson($lessonId));
});

$router->post('/api/vocabulary', function() {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new VocabularyController();
    sendJsonResponse($controller->addWord($data));
});

$router->put('/api/vocabulary/{id}', function() {
    requireAuth();
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    $controller = new VocabularyController();
    sendJsonResponse($controller->updateWord($id, $data));
});

$router->delete('/api/vocabulary/{id}', function() {
    requireAuth();
    $id = $_GET['id'] ?? null;
    $controller = new VocabularyController();
    sendJsonResponse($controller->deleteWord($id));
});

// Progress Routes
$router->get('/api/progress/dashboard', function() {
    requireAuth();
    $userId = getCurrentUserId();
    $controller = new ProgressController();
    sendJsonResponse($controller->getUserDashboard($userId));
});

$router->get('/api/progress/overview', function() {
    requireAuth();
    $userId = getCurrentUserId();
    $controller = new ProgressController();
    sendJsonResponse($controller->getUserProgress($userId));
});

$router->get('/api/progress/lessons', function() {
    requireAuth();
    $userId = getCurrentUserId();
    $limit = $_GET['limit'] ?? 20;
    $controller = new ProgressController();
    sendJsonResponse($controller->getLessonHistory($userId, $limit));
});

$router->get('/api/progress/quizzes', function() {
    requireAuth();
    $userId = getCurrentUserId();
    $limit = $_GET['limit'] ?? 20;
    $controller = new ProgressController();
    sendJsonResponse($controller->getQuizResults($userId, $limit));
});

$router->get('/api/progress/level/{language}/{level}', function() {
    requireAuth();
    $userId = getCurrentUserId();
    $language = $_GET['language'] ?? null;
    $level = $_GET['level'] ?? null;
    $controller = new ProgressController();
    sendJsonResponse($controller->getLevelProgress($userId, $language, $level));
});

$router->post('/api/progress/lesson/{lessonId}', function() {
    requireAuth();
    $userId = getCurrentUserId();
    $lessonId = $_GET['lessonId'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    $timeSpent = $data['time_spent'] ?? 0;
    $controller = new ProgressController();
    sendJsonResponse($controller->recordLessonCompletion($userId, $lessonId, $timeSpent));
});

// Dispatch the request
$router->dispatch();
