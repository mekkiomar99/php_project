<?php
require_once 'classes/Database.php';
require_once 'classes/Category.php';
require_once 'classes/Product.php';
require_once 'classes/Session.php';

Session::start();

try {
    $category = new Category();
    $categories = $category->getAll();
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des catégories : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Vêtements</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue à TM-Shop</h1>
        <p>Découvrez nos collections élégantes pour hommes</p>
    </header>

    <?php include 'nav.inc.php'; ?>

    <section id="vetements" class="clothing-section">
        <h2>Nos Vêtements</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (empty($categories)): ?>
            <p>Aucune catégorie disponible.</p>
        <?php else: ?>
            <div class="clothing-grid">
                <?php foreach ($categories as $category): ?>
                    <?php
                    $product = new Product();
                    $firstProduct = $product->getFirstByCategory($category['id']);
                    ?>
                    <div class="clothing-item">
                        <?php if ($firstProduct): ?>
                            <?php
                            $image_path = $firstProduct['image'];
                            if (file_exists($image_path)) {
                                echo "<img src='$image_path' alt='".htmlspecialchars($category['name'])."' width='200' height='200'>";
                            } else {
                                echo "<p style='color: red;'>Image manquante : {$firstProduct['image']}</p>";
                                echo "<img src='images/placeholder.png' alt='Placeholder' width='200' height='200'>";
                            }
                            ?>
                        <?php else: ?>
                            <img src='images/placeholder.png' alt='Placeholder' width='200' height='200'>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars(ucfirst($category['name'])); ?></h3>
                        <a href="modeles_<?php echo htmlspecialchars($category['name']); ?>.php">Voir Plus</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:tmshop@gmail.com">Contactez-nous</a> | <a href="tel:+21650100100">Appelez-nous</a></p>
    </footer>
</body>
</html>