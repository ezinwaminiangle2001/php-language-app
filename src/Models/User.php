<?php
/**
 * User Model
 */

namespace App\Models;

use Config\Database;
use Exception;

class User {
    private $db;
    private $table = 'users';
    private $id;
    private $username;
    private $email;
    private $password;
    private $first_name;
    private $last_name;
    private $native_language;
    private $learning_language;
    private $level;
    private $created_at;
    private $updated_at;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Create new user
     */
    public function create($data) {
        $userData = [
            'username' => $data['username'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => password_hash($data['password'] ?? '', PASSWORD_BCRYPT),
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'native_language' => $data['native_language'] ?? 'en',
            'learning_language' => $data['learning_language'] ?? 'es',
            'level' => $data['level'] ?? 'beginner',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $userData);
        return $this->db->lastInsertId();
    }

    /**
     * Get user by ID
     */
    public function getById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = ?';
        return $this->db->fetch($query, [$email]);
    }

    /**
     * Get user by username
     */
    public function getByUsername($username) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE username = ?';
        return $this->db->fetch($query, [$username]);
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Update user
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Delete user
     */
    public function delete($id) {
        $this->db->delete($this->table, 'id = ?', [$id]);
        return true;
    }

    /**
     * Get all users
     */
    public function getAll($limit = 50, $offset = 0) {
        $query = 'SELECT * FROM ' . $this->table . ' LIMIT ? OFFSET ?';
        return $this->db->fetchAll($query, [$limit, $offset]);
    }

    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE email = ?';
        return $this->db->fetch($query, [$email]) !== false;
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE username = ?';
        return $this->db->fetch($query, [$username]) !== false;
    }

    /**
     * Get user progress
     */
    public function getProgress($userId) {
        $query = 'SELECT 
                    COUNT(DISTINCT lesson_id) as lessons_completed,
                    COUNT(DISTINCT quiz_id) as quizzes_taken,
                    AVG(score) as average_score,
                    SUM(points) as total_points
                FROM user_progress 
                WHERE user_id = ?';
        return $this->db->fetch($query, [$userId]);
    }
}
