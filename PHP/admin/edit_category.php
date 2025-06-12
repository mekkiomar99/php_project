<?php
require_once '../classes/Database.php';
require_once '../classes/Category.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['error'] = "ID de catégorie manquant.";
    header("Location: categories.php");
    exit;
}

try {
    $category = new Category();
    $categoryData = $category->getById($id);
    if (!$categoryData) {
        $_SESSION['error'] = "Catégorie non trouvée.";
        header("Location: categories.php");
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la récupération : " . $e->getMessage();
    header("Location: categories.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        $_SESSION['error'] = "Le nom de la catégorie est requis.";
    } else {
        try {
            if ($category->update($id, $name)) {
                $_SESSION['success'] = "Catégorie mise à jour avec succès.";
                header("Location: categories.php");
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour.";
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
    <title>TM-Shop | Admin - Modifier Catégorie</title>
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
        <h2>Modifier la catégorie</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Nom : <input type="text" name="name" value="<?php echo htmlspecialchars($categoryData['name']); ?>" required></label>
            <button type="submit">Enregistrer</button>
            <a href="categories.php" class="button">Retour</a>
        </form>
    </div>
    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>