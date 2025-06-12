<?php
require_once 'Database.php';

class Category {
    private $db;
    private $id;
    private $name;
    private $description;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($name, $description = null) {
        try {
            $stmt = $this->db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            return $stmt->execute([$name, $description]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de la catégorie : " . $e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de la catégorie : " . $e->getMessage());
        }
    }

    public static function getByName($name) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM categories WHERE name = ?");
            $stmt->execute([$name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de la catégorie : " . $e->getMessage());
        }
    }

    public function update($id, $name, $description = null) {
        try {
            $stmt = $this->db->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            return $stmt->execute([$name, $description, $id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour de la catégorie : " . $e->getMessage());
        }
    }

    public function hasProducts($category_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->execute([$category_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification des produits : " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
        
            if ($this->hasProducts($id)) {
                throw new Exception("Impossible de supprimer la catégorie : des produits y sont associés.");
            }

            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de la catégorie : " . $e->getMessage());
        }
    }

    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des catégories : " . $e->getMessage());
        }
    }

    public function getCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM categories");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des catégories : " . $e->getMessage());
        }
    }
} 