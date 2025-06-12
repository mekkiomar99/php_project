<?php
class Product {
    private $db;
    private $id;
    private $category_id;
    private $name;
    private $price;
    private $image;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($category_id, $name, $price, $image) {
        try {
            $stmt = $this->db->prepare("INSERT INTO products (category_id, name, price, image) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$category_id, $name, $price, $image]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création du produit : " . $e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du produit : " . $e->getMessage());
        }
    }

    public function update($id, $category_id, $name, $price, $image = null) {
        try {
            if ($image) {
                $stmt = $this->db->prepare("UPDATE products SET category_id = ?, name = ?, price = ?, image = ? WHERE id = ?");
                return $stmt->execute([$category_id, $name, $price, $image, $id]);
            } else {
                $stmt = $this->db->prepare("UPDATE products SET category_id = ?, name = ?, price = ? WHERE id = ?");
                return $stmt->execute([$category_id, $name, $price, $id]);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du produit : " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du produit : " . $e->getMessage());
        }
    }

    public function hasOrders($product_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE product = ?");
            $stmt->execute([$product_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification des commandes : " . $e->getMessage());
        }
    }

    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des produits : " . $e->getMessage());
        }
    }

    public static function getByCategory($category_id) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? ORDER BY p.name");
            $stmt->execute([$category_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des produits par catégorie : " . $e->getMessage());
        }
    }

    public function getNewest($limit = 2) {
        try {
            $limit = (int)$limit; 
            $stmt = $this->db->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT $limit");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des nouveaux produits : " . $e->getMessage());
        }
    }

    public function getByName($name) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name AS category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.name = ? 
            ");
            $stmt->execute([$name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du produit : " . $e->getMessage());
        }
    }

    public function getFirstByCategory($category_name) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name AS category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE c.name = ? 
                ORDER BY p.id 
                LIMIT 1
            ");
            $stmt->execute([$category_name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du premier produit de la catégorie : " . $e->getMessage());
        }
    }

    public function getImagePath($image) {
        
    }

    public function getCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM products");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des produits : " . $e->getMessage());
        }
    }
}