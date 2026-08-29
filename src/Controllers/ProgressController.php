<?php
/**
 * Progress Controller
 */

namespace App\Controllers;

use App\Models\Progress;
use Exception;

class ProgressController {
    private $progressModel;

    public function __construct() {
        $this->progressModel = new Progress();
    }

    /**
     * Record lesson completion
     */
    public function recordLessonCompletion($userId, $lessonId, $timeSpent) {
        try {
            $this->progressModel->recordLessonCompletion($userId, $lessonId, $timeSpent);
            return [
                'success' => true,
                'message' => 'Lesson completion recorded'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get user dashboard statistics
     */
    public function getUserDashboard($userId) {
        try {
            $stats = $this->progressModel->getUserStats($userId);
            $progress = $this->progressModel->getUserProgress($userId);
            $recentLessons = $this->progressModel->getLessonHistory($userId, 5);
            $recentQuizzes = $this->progressModel->getQuizResults($userId, 5);

            return [
                'success' => true,
                'stats' => $stats,
                'progress' => $progress,
                'recent_lessons' => $recentLessons,
                'recent_quizzes' => $recentQuizzes
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get user progress overview
     */
    public function getUserProgress($userId) {
        try {
            $progress = $this->progressModel->getUserProgress($userId);
            return [
                'success' => true,
                'data' => $progress
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get lesson history
     */
    public function getLessonHistory($userId, $limit = 20) {
        try {
            $history = $this->progressModel->getLessonHistory($userId, $limit);
            return [
                'success' => true,
                'data' => $history,
                'count' => count($history)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get quiz results
     */
    public function getQuizResults($userId, $limit = 20) {
        try {
            $results = $this->progressModel->getQuizResults($userId, $limit);
            return [
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get level progress
     */
    public function getLevelProgress($userId, $language, $level) {
        try {
            $progress = $this->progressModel->getLevelProgress($userId, $language, $level);
            return [
                'success' => true,
                'data' => $progress
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
