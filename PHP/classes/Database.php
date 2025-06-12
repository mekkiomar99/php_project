<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = 'localhost';
        $dbname = 'tmshop';
        $username = 'root';
        $password = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function isAdmin($admin_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM admins WHERE id = ?");
            $stmt->execute([$admin_id]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification admin : " . $e->getMessage());
            return false;
        }
    }

    public function requireAdmin() {
        if (!Session::isAdmin()) {
            header("Location: admin_login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    // Empêcher le clonage de l'instance
    private function __clone() {}

    // Empêcher la désérialisation
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
} 