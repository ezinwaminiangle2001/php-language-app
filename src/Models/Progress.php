<?php
/**
 * Progress Model - Track user progress
 */

namespace App\Models;

use Config\Database;

class Progress {
    private $db;
    private $table = 'user_progress';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Record lesson completion
     */
    public function recordLessonCompletion($userId, $lessonId, $timeSpent) {
        $data = [
            'user_id' => $userId,
            'lesson_id' => $lessonId,
            'completed_at' => date('Y-m-d H:i:s'),
            'time_spent' => $timeSpent
        ];

        $this->db->insert($this->table, $data);
        return $this->db->lastInsertId();
    }

    /**
     * Record quiz result
     */
    public function recordQuizResult($userId, $quizId, $score, $totalQuestions, $timeSpent) {
        $data = [
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'passed' => ($score >= 70) ? 1 : 0,
            'time_spent' => $timeSpent,
            'completed_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $data);
        return $this->db->lastInsertId();
    }

    /**
     * Get user progress
     */
    public function getUserProgress($userId) {
        $query = 'SELECT 
                    COUNT(DISTINCT lesson_id) as lessons_completed,
                    COUNT(DISTINCT quiz_id) as quizzes_taken,
                    AVG(score) as average_score,
                    SUM(CASE WHEN score IS NOT NULL THEN 1 ELSE 0 END) as total_attempts
                FROM ' . $this->table . ' 
                WHERE user_id = ?';
        return $this->db->fetch($query, [$userId]);
    }

    /**
     * Get user lesson history
     */
    public function getLessonHistory($userId, $limit = 10) {
        $query = 'SELECT up.*, l.title as lesson_title, l.type as lesson_type 
                FROM ' . $this->table . ' up
                LEFT JOIN lessons l ON up.lesson_id = l.id
                WHERE up.user_id = ? AND up.lesson_id IS NOT NULL
                ORDER BY up.completed_at DESC
                LIMIT ?';
        return $this->db->fetchAll($query, [$userId, $limit]);
    }

    /**
     * Get user quiz results
     */
    public function getQuizResults($userId, $limit = 10) {
        $query = 'SELECT up.*, q.title as quiz_title 
                FROM ' . $this->table . ' up
                LEFT JOIN quizzes q ON up.quiz_id = q.id
                WHERE up.user_id = ? AND up.quiz_id IS NOT NULL
                ORDER BY up.completed_at DESC
                LIMIT ?';
        return $this->db->fetchAll($query, [$userId, $limit]);
    }

    /**
     * Calculate level progress
     */
    public function getLevelProgress($userId, $language, $level) {
        $query = 'SELECT 
                    COUNT(DISTINCT up.lesson_id) as completed_lessons,
                    (SELECT COUNT(*) FROM lessons WHERE language = ? AND level = ?) as total_lessons,
                    AVG(up.score) as average_score
                FROM ' . $this->table . ' up
                LEFT JOIN lessons l ON up.lesson_id = l.id
                WHERE up.user_id = ? AND l.language = ? AND l.level = ?';
        return $this->db->fetch($query, [$language, $level, $userId, $language, $level]);
    }

    /**
     * Get user statistics
     */
    public function getUserStats($userId) {
        $query = 'SELECT 
                    COUNT(DISTINCT lesson_id) as total_lessons_completed,
                    COUNT(DISTINCT quiz_id) as total_quizzes_taken,
                    SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as quizzes_passed,
                    AVG(score) as average_quiz_score,
                    SUM(time_spent) as total_study_time,
                    MAX(completed_at) as last_activity
                FROM ' . $this->table . '
                WHERE user_id = ?';
        return $this->db->fetch($query, [$userId]);
    }
}
