<?php
/**
 * Quiz Model
 */

namespace App\Models;

use Config\Database;

class Quiz {
    private $db;
    private $table = 'quizzes';
    private $questionsTable = 'quiz_questions';
    private $answersTable = 'quiz_answers';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Create quiz
     */
    public function create($data) {
        $quizData = [
            'lesson_id' => $data['lesson_id'] ?? null,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'total_questions' => $data['total_questions'] ?? 0,
            'passing_score' => $data['passing_score'] ?? 70,
            'time_limit' => $data['time_limit'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $quizData);
        return $this->db->lastInsertId();
    }

    /**
     * Get quiz by ID
     */
    public function getById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get quizzes by lesson
     */
    public function getByLessonId($lessonId) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE lesson_id = ?';
        return $this->db->fetchAll($query, [$lessonId]);
    }

    /**
     * Add question to quiz
     */
    public function addQuestion($quizId, $data) {
        $questionData = [
            'quiz_id' => $quizId,
            'question_text' => $data['question_text'] ?? null,
            'question_type' => $data['question_type'] ?? 'multiple_choice',
            'order' => $data['order'] ?? 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->questionsTable, $questionData);
        return $this->db->lastInsertId();
    }

    /**
     * Add answer option
     */
    public function addAnswer($questionId, $data) {
        $answerData = [
            'question_id' => $questionId,
            'answer_text' => $data['answer_text'] ?? null,
            'is_correct' => $data['is_correct'] ?? false,
            'order' => $data['order'] ?? 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->answersTable, $answerData);
        return $this->db->lastInsertId();
    }

    /**
     * Get questions for quiz
     */
    public function getQuestions($quizId) {
        $query = 'SELECT * FROM ' . $this->questionsTable . ' WHERE quiz_id = ? ORDER BY `order` ASC';
        return $this->db->fetchAll($query, [$quizId]);
    }

    /**
     * Get answers for question
     */
    public function getAnswers($questionId) {
        $query = 'SELECT * FROM ' . $this->answersTable . ' WHERE question_id = ? ORDER BY `order` ASC';
        return $this->db->fetchAll($query, [$questionId]);
    }

    /**
     * Update quiz
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Delete quiz
     */
    public function delete($id) {
        $this->db->delete($this->table, 'id = ?', [$id]);
        return true;
    }
}
