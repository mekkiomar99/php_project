<?php
class Order {
    private $db;
    private $id;
    private $user_id;
    private $total_amount;
    private $status;
    private $created_at;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($user_id, $product, $size, $shoe_size, $belt_length, $quantity, $address, $phone, $payment_method) {
        try {
            // Get product price
            $stmt = $this->db->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$product]);
            $productData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$productData) {
                throw new Exception("Produit non trouvé");
            }
            
            // Calculate prices
            $unitPrice = $productData['price'];
            $subtotal = $unitPrice * $quantity;
            $shippingCost = 7; 
            $total = $subtotal + $shippingCost;
            
            
            $formattedPhone = $phone;
            
            $stmt = $this->db->prepare("
                INSERT INTO orders 
                (user_id, product, size, shoe_size, belt_length, quantity, address, phone, shipping_cost, total, payment_method, status, date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en attente', NOW())
            ");
            
            if ($stmt->execute([
                $user_id, 
                $product, 
                $size, 
                $shoe_size, 
                $belt_length, 
                $quantity, 
                $address, 
                $formattedPhone,
                $shippingCost,
                $total,
                strtolower($payment_method)
            ])) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de la commande : " . $e->getMessage());
        }
    }

    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des commandes : " . $e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de la commande : " . $e->getMessage());
        }
    }

    public function update($id, $user_id, $product, $size, $shoe_size, $belt_length, $quantity, $address, $shipping_cost, $total, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE orders SET user_id = ?, product = ?, size = ?, shoe_size = ?, belt_length = ?, quantity = ?, address = ?, shipping_cost = ?, total = ?, status = ?, phone = ?, payment_method = ? WHERE id = ?");
            return $stmt->execute([$user_id, $product, $size, $shoe_size, $belt_length, $quantity, $address, $shipping_cost, $total, $status, $phone, $payment_method, $id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour de la commande : " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM orders WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de la commande : " . $e->getMessage());
        }
    }

    public function updateStatus($id, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du statut : " . $e->getMessage());
        }
    }

    public function getByUserId($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, 
                       u.name as user_name,
                       p.name as product_name,
                       o.id as order_id,
                       o.date as created_at,
                       o.status as status,
                       o.total as total,
                       o.shipping_cost as shipping_cost
                FROM orders o 
                JOIN users u ON o.user_id = u.id
                LEFT JOIN products p ON o.product = p.id
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
    
            foreach ($orders as &$order) {
                $order['items'] = [
                    [
                        'product_name' => $order['product_name'] ?? 'Produit non disponible',
                        'quantity' => $order['quantity'] ?? 1,
                        'price' => $order['total'] / ($order['quantity'] ?? 1)
                    ]
                ];
            }
            
        
            foreach ($orders as &$order) {
                $order['total_with_shipping'] = $order['total'] + $order['shipping_cost'];
            }
            
            return $orders;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des commandes de l'utilisateur : " . $e->getMessage());
        }
    }



    public function getCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM orders");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des commandes : " . $e->getMessage());
        }
    }

    public function getAllWithUserDetails() {
        try {
            $stmt = $this->db->query("SELECT o.*, u.name AS user_name, u.email AS user_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des commandes avec détails utilisateur : " . $e->getMessage());
        }
    }
}