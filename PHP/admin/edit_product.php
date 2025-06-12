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

$product_id = intval($_GET['id'] ?? 0);
if ($product_id <= 0) {
    $_SESSION['error'] = "ID de produit invalide.";
    header("Location: products.php");
    exit;
}

try {
    $product = new Product();
    $product_data = $product->getById($product_id);
    
    if (!$product_data) {
        $_SESSION['error'] = "Produit non trouvé.";
        header("Location: products.php");
        exit;
    }

    $category = new Category();
    $categories = $category->getAll();
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
    header("Location: products.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $image = $_FILES['image'] ?? null;

    if (empty($name) || $category_id <= 0 || $price <= 0) {
        $_SESSION['error'] = "Tous les champs sont requis.";
    } else {
        $image_path = $product_data['image'];
        if ($image && $image['error'] !== UPLOAD_ERR_NO_FILE) {
            $new_image_path = 'images/' . basename($image['name']);
            if (move_uploaded_file($image['tmp_name'], '../' . $new_image_path)) {
                $image_path = $new_image_path;
            } else {
                $_SESSION['error'] = "Erreur lors du téléchargement de l'image.";
                header("Location: edit_product.php?id=" . $product_id);
                exit;
            }
        }

        try {
            if ($product->update($product_id, $category_id, $name, $price, $image_path)) {
                $_SESSION['success'] = "Produit mis à jour avec succès.";
                header("Location: products.php");
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour du produit.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Admin - Modifier Produit</title>
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
        <h2>Modifier le produit</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Nom : <input type="text" name="name" value="<?php echo htmlspecialchars($product_data['name']); ?>" required></label>
            <label>Catégorie :
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php echo $cat['id'] === $product_data['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Prix : <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product_data['price']); ?>" required></label>
            <label>Image actuelle : <img src="../<?php echo htmlspecialchars($product_data['image']); ?>" alt="Image actuelle" style="max-width: 200px;"></label>
            <label>Nouvelle image (optionnel) : <input type="file" name="image" accept="image/*"></label>
            <button type="submit">Mettre à jour</button>
            <a href="products.php" class="button">Retour</a>
        </form>
    </div>
    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>