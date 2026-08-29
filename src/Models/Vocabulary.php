<?php
/**
 * Vocabulary Model
 */

namespace App\Models;

use Config\Database;

class Vocabulary {
    private $db;
    private $table = 'vocabulary';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Add vocabulary word
     */
    public function addWord($data) {
        $wordData = [
            'lesson_id' => $data['lesson_id'] ?? null,
            'word' => $data['word'] ?? null,
            'translation' => $data['translation'] ?? null,
            'pronunciation' => $data['pronunciation'] ?? null,
            'example_sentence' => $data['example_sentence'] ?? null,
            'part_of_speech' => $data['part_of_speech'] ?? null,
            'difficulty' => $data['difficulty'] ?? 'beginner',
            'audio_url' => $data['audio_url'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $wordData);
        return $this->db->lastInsertId();
    }

    /**
     * Get word by ID
     */
    public function getById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get vocabulary by lesson
     */
    public function getByLessonId($lessonId) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE lesson_id = ?';
        return $this->db->fetchAll($query, [$lessonId]);
    }

    /**
     * Search vocabulary
     */
    public function search($query, $limit = 20) {
        $searchQuery = 'SELECT * FROM ' . $this->table . ' WHERE word LIKE ? OR translation LIKE ? LIMIT ?';
        $searchTerm = '%' . $query . '%';
        return $this->db->fetchAll($searchQuery, [$searchTerm, $searchTerm, $limit]);
    }

    /**
     * Get random vocabulary
     */
    public function getRandom($limit = 10, $difficulty = null) {
        if ($difficulty) {
            $query = 'SELECT * FROM ' . $this->table . ' WHERE difficulty = ? ORDER BY RAND() LIMIT ?';
            return $this->db->fetchAll($query, [$difficulty, $limit]);
        }
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY RAND() LIMIT ?';
        return $this->db->fetchAll($query, [$limit]);
    }

    /**
     * Get vocabulary by difficulty
     */
    public function getByDifficulty($difficulty, $limit = 50) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE difficulty = ? LIMIT ?';
        return $this->db->fetchAll($query, [$difficulty, $limit]);
    }

    /**
     * Update word
     */
    public function update($id, $data) {
        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Delete word
     */
    public function delete($id) {
        $this->db->delete($this->table, 'id = ?', [$id]);
        return true;
    }
}
