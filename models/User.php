<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $email, $gender, $country) {
        // Validate email domain
        if (!str_ends_with($email, '@webmail.npru.ac.th')) {
            throw new Exception('Email must end with @webmail.npru.ac.th');
        }

        // Validate required fields
        if (empty($name) || empty($email)) {
            throw new Exception('Name and Email are required.');
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO users (name, email, gender, country) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $gender, $country]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || $e->errorInfo[1] == 1062) {
                throw new Exception('Email already exists.');
            }
            throw new Exception('Error adding user: ' . $e->getMessage());
        }
    }

    public function update($id, $name, $email, $gender, $country) {
        // Validate email domain
        if (!str_ends_with($email, '@webmail.npru.ac.th')) {
            throw new Exception('Email must end with @webmail.npru.ac.th');
        }

        // Validate required fields
        if (empty($name) || empty($email)) {
            throw new Exception('Name and Email are required.');
        }

        try {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, gender = ?, country = ? WHERE id = ?");
            $stmt->execute([$name, $email, $gender, $country, $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || $e->errorInfo[1] == 1062) {
                throw new Exception('Email already exists.');
            }
            throw new Exception('Error updating user: ' . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception('Error deleting user: ' . $e->getMessage());
        }
    }

    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}