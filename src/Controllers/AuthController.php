<?php
/**
 * Authentication Controller
 */

namespace App\Controllers;

use App\Models\User;
use Exception;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Register new user
     */
    public function register($data) {
        try {
            // Validate input
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                throw new Exception('Username, email, and password are required');
            }

            // Check if user already exists
            if ($this->userModel->emailExists($data['email'])) {
                throw new Exception('Email already registered');
            }

            if ($this->userModel->usernameExists($data['username'])) {
                throw new Exception('Username already taken');
            }

            // Validate password strength
            if (strlen($data['password']) < 8) {
                throw new Exception('Password must be at least 8 characters long');
            }

            // Create user
            $userId = $this->userModel->create($data);

            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user_id' => $userId
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        try {
            if (empty($email) || empty($password)) {
                throw new Exception('Email and password are required');
            }

            $user = $this->userModel->getByEmail($email);

            if (!$user) {
                throw new Exception('User not found');
            }

            if (!$this->userModel->verifyPassword($password, $user['password'])) {
                throw new Exception('Invalid password');
            }

            // Start session and store user info
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['learning_language'] = $user['learning_language'];
            $_SESSION['level'] = $user['level'];

            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name']
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        return ['success' => true, 'message' => 'Logout successful'];
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->userModel->getById($_SESSION['user_id']);
    }
}
