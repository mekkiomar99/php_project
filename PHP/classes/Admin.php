<?php
class Admin {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function adminLogin($email, $password) {
        try {
            
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE email = ? AND password = ?");
            $stmt->execute([$email, $password]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                return $admin;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la connexion admin : " . $e->getMessage());
        }
    }

    public function getAdminId() {
        return $_SESSION['admin_id'] ?? null;
    }

    public function getAdminName() {
        return $_SESSION['admin_name'] ?? null;
    }

    public function updateLastLogin($admin_id) {
        try {
            $stmt = $this->db->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            return $stmt->execute([$admin_id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du dernier login : " . $e->getMessage());
        }
    }
}
