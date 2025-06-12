<?php
require_once '../classes/Database.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

try {
    $category = new Category();
    $categories = $category->getAll();

    $product = new Product();
    $category_filter = $_GET['category_id'] ?? '';
    $products = $category_filter ? $product->getByCategory($category_filter) : $product->getAll();
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la récupération des données : " . $e->getMessage();
    $products = [];
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Admin - Produits</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h1>Bienvenue à TM-Shop</h1>
        <p>Administration</p>
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
        <h2>Gestion des Produits</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <div>
            <label>Filtrer par catégorie :</label>
            <select onchange="window.location='products.php?category_id=' + this.value;">
                <option value="">Toutes</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php echo $category_filter === $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a href="add_product.php" class="button">Ajouter un produit</a>
        </div>
        <table>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?> €</td>
                    <td><img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="Produit" style="width: 50px;"></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="button">Modifier</a>
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="button delete" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>