<?php
/**
 * Lesson Model
 */

namespace App\Models;

use Config\Database;

class Lesson {
    private $db;
    private $table = 'lessons';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Create lesson
     */
    public function create($data) {
        $lessonData = [
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'language' => $data['language'] ?? null,
            'type' => $data['type'] ?? 'vocabulary',
            'level' => $data['level'] ?? 'beginner',
            'content' => $data['content'] ?? null,
            'order' => $data['order'] ?? 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $lessonData);
        return $this->db->lastInsertId();
    }

    /**
     * Get lesson by ID
     */
    public function getById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get lessons by language and level
     */
    public function getByLanguageAndLevel($language, $level) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE language = ? AND level = ? ORDER BY `order` ASC';
        return $this->db->fetchAll($query, [$language, $level]);
    }

    /**
     * Get lessons by type
     */
    public function getByType($type) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE type = ? ORDER BY `order` ASC';
        return $this->db->fetchAll($query, [$type]);
    }

    /**
     * Get all lessons
     */
    public function getAll($limit = 50, $offset = 0) {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY `order` ASC LIMIT ? OFFSET ?';
        return $this->db->fetchAll($query, [$limit, $offset]);
    }

    /**
     * Update lesson
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Delete lesson
     */
    public function delete($id) {
        $this->db->delete($this->table, 'id = ?', [$id]);
        return true;
    }

    /**
     * Get lesson content (detailed)
     */
    public function getContent($lessonId) {
        $query = 'SELECT * FROM lesson_content WHERE lesson_id = ?';
        return $this->db->fetchAll($query, [$lessonId]);
    }
}
