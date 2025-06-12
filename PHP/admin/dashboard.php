<?php
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Order.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

try {
    $user = new User();
    $product = new Product();
    $category = new Category();
    $order = new Order();

    $user_count = $user->getCount();
    $product_count = $product->getCount();
    $category_count = $category->getCount();
    $order_count = $order->getCount();
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la récupération des statistiques : " . $e->getMessage();
    $user_count = $product_count = $category_count = $order_count = 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Admin - Tableau de Bord</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="admin-header">
        <h1>Bienvenue à TM-Shop</h1>
        <p>Administration - Connecté en tant que <?php echo htmlspecialchars(Session::getAdminName() ?? 'Administrateur'); ?></p>
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
        <h2>Tableau de Bord</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <div class="dashboard-stats">
            <div class="stat-box">
                <h3>Utilisateurs</h3>
                <p><?php echo htmlspecialchars($user_count); ?></p>
                <a href="users.php">Voir tous</a>
            </div>
            <div class="stat-box">
                <h3>Produits</h3>
                <p><?php echo htmlspecialchars($product_count); ?></p>
                <a href="products.php">Voir tous</a>
            </div>
            <div class="stat-box">
                <h3>Catégories</h3>
                <p><?php echo htmlspecialchars($category_count); ?></p>
                <a href="categories.php">Voir tous</a>
            </div>
            <div class="stat-box">
                <h3>Commandes</h3>
                <p><?php echo htmlspecialchars($order_count); ?></p>
                <a href="orders.php">Voir tous</a>
            </div>
        </div>
    </div>
    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>