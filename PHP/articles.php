<?php
require_once 'classes/Session.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';
require_once 'classes/Database.php';


ini_set('display_errors', 1);
error_reporting(E_ALL);

$database = Database::getInstance();
$pdo = $database->getConnection();

Session::start();

$category_name = $_GET['categorie'] ?? '';

$category_map = [
    'chemises' => 'chemise',
    'chaussure' => 'chaussures'
];


$category_name = strtolower($category_name);
$category_name = $category_map[$category_name] ?? $category_name;

// Gérer les catégories spéciales
if ($category_name === 'vêtements') {
    // Afficher tous les vêtements
    $clothing_categories = ['pull', 'blouson', 'chemises', 'pantalon', 'costumes'];
    $products = [];
    foreach ($clothing_categories as $cat) {
        $category = Category::getByName($cat);
        if ($category) {
            $cat_products = Product::getByCategory($category['id']);
            $products = array_merge($products, $cat_products);
        }
    }
} elseif ($category_name === 'accessoires') {
    // Afficher tous les accessoires
    $accessories_categories = ['ceinture', 'cravate'];
    $products = [];
    foreach ($accessories_categories as $cat) {
        $category = Category::getByName($cat);
        if ($category) {
            $cat_products = Product::getByCategory($category['id']);
            $products = array_merge($products, $cat_products);
        }
    }
} else {
    // Gérer les catégories normales
    $category = Category::getByName($category_name);
    if (!$category) {
        $error = "Catégorie '$category_name' non trouvée.";
        $products = [];
    } else {
        // Get products for this category
        $products = Product::getByCategory($category['id']);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | <?= htmlspecialchars($category['name'] ?? $category_name) ?></title>
    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <header>
        <h1>Bienvenue à TM-Shop</h1>
        <p>Découvrez nos collections élégantes pour hommes</p>
    </header>

    <?php include 'nav.inc.php'; ?>

    <section class="products-section">
        <h2> Articles </h2>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (empty($products)): ?>
            <p>Aucun produit disponible pour le moment.</p>
        <?php else: ?>
            <div class="clothing-grid">
                <?php foreach ($products as $product): ?>
                    <div class="clothing-item">
                        <?php
                        $image_path = $product['image'];
                        if (file_exists($image_path)) {
                            echo "<img src='$image_path' alt='" . htmlspecialchars($product['name']) . "' width='200' height='200'>";
                        } else {
                            echo "<img src='images/placeholder.png' alt='Placeholder' width='200' height='200'>";
                        }
                        ?>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="price">€<?php echo number_format($product['price'], 2); ?></p>
                        <a href="passer-une-commande.php?product_id=<?php echo $product['id']; ?>&product_name=<?php echo urlencode($product['name']); ?>&product_price=<?php echo $product['price']; ?>&quantity=1" class="command-button">Commander</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
