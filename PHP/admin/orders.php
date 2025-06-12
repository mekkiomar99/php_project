<?php
require_once '../classes/Database.php';
require_once '../classes/Order.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

try {
    $order = new Order();
    $orders = $order->getAllWithUserDetails();
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la récupération des commandes : " . $e->getMessage();
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Admin - Commandes</title>
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
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="status">Statut :</label>
                    <select name="status" id="status">
                        <option value="">Tous</option>
                        <option value="en attente" <?php echo isset($_GET['status']) && $_GET['status'] == 'en attente' ? 'selected' : ''; ?>>En attente</option>
                        <option value="traitée" <?php echo isset($_GET['status']) && $_GET['status'] == 'traitée' ? 'selected' : ''; ?>>Traitée</option>
                        <option value="expédiée" <?php echo isset($_GET['status']) && $_GET['status'] == 'expédiée' ? 'selected' : ''; ?>>Expédiée</option>
                        <option value="livrée" <?php echo isset($_GET['status']) && $_GET['status'] == 'livrée' ? 'selected' : ''; ?>>Livrée</option>
                    </select>
                </div>
                <button type="submit" class="filter-button">Filtrer</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Produit</th>
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
                        <td><?php echo htmlspecialchars($order['total']); ?> DT</td>
                        <td>
                            <span class="status-badge status-<?php echo str_replace(' ', '-', $order['status']); ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($order['date']); ?></td>
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
                            <?php if ($order['status'] !== 'livrée'): ?>
                                <a href="delete_order.php?id=<?php echo $order['id']; ?>" 
                                   class="action-button" style="background-color: #dc3545;"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">
                                    Supprimer
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:tmshop@gmail.com">Contactez-nous</a> | <a href="tel:+21650100100">Appelez-nous</a></p>
    </footer>
</body>
</html>