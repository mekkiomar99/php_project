<?php
class User {
    private $db;
    private $id;
    private $name;
    private $email;
    private $password;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($name, $email, $password) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email, $hashed_password]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        }
    }

    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                Session::set('user_id', $user['id']);
                Session::set('user_name', $user['name']);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la connexion : " . $e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de l'utilisateur : " . $e->getMessage());
        }
    }

    public function update($id, $name, $email) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            return $stmt->execute([$name, $email, $id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
        }
    }

    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT u.id, u.name, u.email, IF(a.id IS NOT NULL, 1, 0) as is_admin 
                                    FROM users u 
                                    LEFT JOIN admins a ON u.email = a.email");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
        }
    }

    public function register($name, $email, $password) {
        return $this->create($name, $email, $password);
    }

    public function adminLogin($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && $password === $admin['password']) {
                Session::set('admin_id', $admin['id']);
                Session::set('admin_name', $admin['name']);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la connexion admin : " . $e->getMessage());
        }
    }

    public function getCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des utilisateurs : " . $e->getMessage());
        }
    }
} 