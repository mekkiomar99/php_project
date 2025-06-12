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


if (empty($category_name)) {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY name");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    try {
        
        if ($category_name === 'tous') {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY name");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        elseif ($category_name === 'vêtements') {
            $clothing_categories = ['pull', 'blouson', 'chemise', 'pantalon', 'costumes'];
            $products = [];
            foreach ($clothing_categories as $cat) {
                $category = Category::getByName($cat);
                if ($category) {
                    $cat_products = Product::getByCategory($category['id']);
                    $products = array_merge($products, $cat_products);
                }
            }
        } else {
            
            $category = Category::getByName($category_name);
            if (!$category) {
                throw new Exception("Catégorie '$category_name' non trouvée.");
            }
            
        
            $products = Product::getByCategory($category['id']);
        }
    } catch (Exception $e) {
        $error = "Erreur lors de la récupération des produits : " . $e->getMessage();
        $products = [];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | <?= htmlspecialchars($category_name) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue à TM-Shop</h1>
        <p>Découvrez nos collections élégantes pour hommes</p>
    </header>

    <?php include 'nav.inc.php'; ?>

    <section class="products-section">
        <div class="categories-list">
            <div class="category-buttons">
                <a href="modeles.php?categorie=tous" class="category-link green-button">Tous</a>
                <a href="articles.php?categorie=vêtements" class="category-link green-button">Vêtements</a>
                <a href="articles.php?categorie=accessoires" class="category-link green-button">Accessoires</a>
                <a href="articles.php?categorie=chaussures" class="category-link green-button">Chaussures</a>
            </div>
        </div>
    </section>
        <?php if (isset($error)): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php elseif (empty($products)): ?>
            <div class="no-products">
                <p>Aucun produit disponible pour le moment.</p>
            </div>
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
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="price">€<?= number_format($product['price'], 2) ?></p>
                        <form action="passer-une-commande.php" method="post" class="commande-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                            <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="button">Commander</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
