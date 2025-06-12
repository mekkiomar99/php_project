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
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la récupération des catégories : " . $e->getMessage();
    $categories = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $image = $_FILES['image'] ?? null;

    if (empty($name) || $category_id <= 0 || $price <= 0 || !$image || $image['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['error'] = "Tous les champs sont requis.";
    } else {
        $image_path = 'images/' . basename($image['name']);
        if (move_uploaded_file($image['tmp_name'], '../' . $image_path)) {
            try {
                $product = new Product();
                if ($product->create($category_id, $name, $price, $image_path)) {
                    $_SESSION['success'] = "Produit ajouté avec succès.";
                    header("Location: products.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Erreur lors de l'ajout du produit.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur lors de l'ajout : " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Erreur lors du téléchargement de l'image.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Admin - Ajouter Produit</title>
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
        <h2>Ajouter un produit</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Nom : <input type="text" name="name" required></label>
            <label>Catégorie :
                <select name="category_id" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Prix : <input type="number" step="0.01" name="price" required></label>
            <label>Image : <input type="file" name="image" accept="image/*" required></label>
            <button type="submit">Ajouter</button>
            <a href="products.php" class="button">Retour</a>
        </form>
    </div>
    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>