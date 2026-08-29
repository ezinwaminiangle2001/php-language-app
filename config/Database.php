<?php
/**
 * Database Connection Class
 */

namespace Config;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $user = DB_USER;
    private $password = DB_PASSWORD;
    private $port = DB_PORT;

    /**
     * Singleton pattern - get database instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connect to database
     */
    private function connect() {
        try {
            $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';charset=utf8mb4';
            
            $this->connection = new PDO(
                $dsn,
                $this->user,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die('Database Connection Error: ' . $e->getMessage());
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Execute query
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Database Error: ' . $e->getMessage());
        }
    }

    /**
     * Fetch all results
     */
    public function fetchAll($query, $params = []) {
        return $this->execute($query, $params)->fetchAll();
    }

    /**
     * Fetch single result
     */
    public function fetch($query, $params = []) {
        return $this->execute($query, $params)->fetch();
    }

    /**
     * Insert record
     */
    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $query = 'INSERT INTO ' . $table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        return $this->execute($query, array_values($data));
    }

    /**
     * Update record
     */
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = $key . ' = ?';
        }
        $query = 'UPDATE ' . $table . ' SET ' . implode(',', $set) . ' WHERE ' . $where;
        return $this->execute($query, array_merge(array_values($data), $whereParams));
    }

    /**
     * Delete record
     */
    public function delete($table, $where, $params = []) {
        $query = 'DELETE FROM ' . $table . ' WHERE ' . $where;
        return $this->execute($query, $params);
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollBack() {
        return $this->getConnection()->rollBack();
    }

    // Prevent cloning
    private function __clone() {}
    private function __wakeup() {}
}
