<?php
require_once '../classes/Database.php';
require_once '../classes/Order.php';
require_once '../classes/User.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

$error = null;
$success = null;

// Handle status update
if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['order_id']) && isset($_GET['status'])) {
    $order_id = intval($_GET['order_id']);
    $new_status = $_GET['status'];

    if ($order_id <= 0) {
        $error = "ID de commande invalide.";
    } else {
        $valid_statuses = ['en attente', 'traitée', 'expédiée', 'livrée'];
        if (!in_array($new_status, $valid_statuses)) {
            $error = "Statut invalide.";
        } else {
            try {
                $order = new Order();
                if ($order->updateStatus($order_id, $new_status)) {
                    $success = "Statut de la commande mis à jour avec succès.";
                } else {
                    $error = "Erreur lors de la mise à jour du statut.";
                }
            } catch (Exception $e) {
                $error = "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        }
    }
}

// Fetch all orders
try {
    $order = new Order();
    $orders = $order->getAllWithUserDetails();
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des commandes : " . $e->getMessage();
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Admin - Gestion des Commandes</title>
    <link rel="stylesheet" href="admin.css">

</head>
<body>
    <header>
        <h1>TM-Shop Administration</h1>
        <p>Gestion des commandes</p>
    </header>

    <nav class="admin-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Utilisateurs</a>
        <a href="products.php">Produits</a>
        <a href="categories.php">Catégories</a>
        <a href="orders.php">Commandes</a>
        <a href="../logout.php">Déconnexion</a>
    </nav>

    <div class="admin-content">
        <h2>Gestion des Commandes</h2>
        
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Produit</th>
                    <th>Taille</th>
                    <th>Pointure</th>
                    <th>Longueur</th>
                    <th>Quantité</th>
                    <th>Adresse</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['product']); ?></td>
                        <td><?php echo htmlspecialchars($order['size'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($order['shoe_size'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($order['belt_length'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                        <td><?php echo htmlspecialchars($order['total']); ?> DT</td>
                        <td>
                            <span class="status-badge status-<?php echo str_replace(' ', '-', $order['status']); ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                        <td class="action-buttons">
                            <?php if ($order['status'] === 'en attente'): ?>
                                <a href="?action=update_status&order_id=<?php echo $order['id']; ?>&status=traitée" 
                                   class="action-button" style="background-color: #28a745;">
                                    Traiter
                                </a>
                            <?php elseif ($order['status'] === 'traitée'): ?>
                                <a href="?action=update_status&order_id=<?php echo $order['id']; ?>&status=expédiée" 
                                   class="action-button" style="background-color: #007bff;">
                                    Expédier
                                </a>
                            <?php elseif ($order['status'] === 'expédiée'): ?>
                                <a href="?action=update_status&order_id=<?php echo $order['id']; ?>&status=livrée" 
                                   class="action-button" style="background-color: #6c757d;">
                                    Livrer
                                </a>
                            <?php endif; ?>
                            <a href="edit_order.php?id=<?php echo $order['id']; ?>" 
                               class="action-button" style="background-color: #17a2b8;">
                                Modifier
                            </a>
                            <a href="delete_order.php?id=<?php echo $order['id']; ?>" 
                               class="action-button" style="background-color: #dc3545;"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html> 