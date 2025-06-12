<?php

require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/Order.php';
require_once 'classes/User.php';
require_once 'classes/Session.php';

Session::start();

if (!Session::isLoggedIn()) {
    header("Location: login.php?redirect=passer-une-commande.php");
    exit;
}

try {
    $product = new Product();
    $products = $product->getAll();
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des produits : " . $e->getMessage();
    $products = [];
}

$preselected_product_id = $_GET['product_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone = $_POST['phone'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'espace';
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? '';
    echo "Product ID reçu : " . htmlspecialchars($product_id);
    $quantity = intval($_POST['quantity'] ?? 1);
    $size = $_POST['size'] ?? null;
    $shoe_size = $_POST['shoe_size'] ?? null;
    $belt_length = $_POST['belt_length'] ?? null;


    $productObj = new Product();
    $productData = $productObj->getById($product_id);
    if (!$productData) {
        $error = "Produit non trouvé";
    } else {
        $unitPrice = $productData['price'];
        $subtotal = $unitPrice * $quantity;
        $shipping_cost = 0;
        $total = $subtotal + $shipping_cost;
    }


    $userObj = new User();
    $userData = $userObj->getById($user_id);
    if (!$userData) {
        $error = "Utilisateur inexistant. Veuillez vous reconnecter.";
    } elseif (empty($address) || empty($phone) || $product_id <= 0 || $quantity <= 0) {

    } else {
        try {
        
            if (!$productData) {
                $error = "Produit non trouvé";
            }
            
            elseif (!$userData) {
                $error = "Utilisateur inexistant. Veuillez vous reconnecter.";
            }
            
            elseif (empty($address)) {
                $error = "L'adresse de livraison est requise.";
            }
            elseif (empty($phone)) {
                $error = "Le numéro de téléphone est requis.";
            }
            elseif ($product_id <= 0) {
                $error = "Veuillez sélectionner un produit.";
            }
            elseif ($quantity <= 0) {
                $error = "La quantité doit être supérieure à 0.";
            }
            
            elseif (($productData['category_name'] === 'chaussures' && empty($shoe_size)) ||
                   ($productData['category_name'] === 'ceinture' && empty($belt_length))) {
                $error = "Veuillez sélectionner la taille appropriée pour ce produit.";
            }
            else {
                $order = new Order();
                if ($order->create(
                    $user_id,
                    $product_id,
                    $size,
                    $shoe_size,
                    $belt_length,
                    $quantity,
                    $address,
                    $phone,          
                    $payment_method  
                )) {
                    $_SESSION['success'] = "Commande passée avec succès !";
                    header("Location: orders.php");
                    exit;
                } else {
                    $error = "Erreur lors de la création de la commande.";
                }
            }
        } catch (Exception $e) {
            $error = "Erreur lors de la commande : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Passer une commande</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <header>
        <h1>Bienvenue à TM-Shop</h1>
        <p>Découvrez nos collections élégantes pour hommes</p>
    </header>

    <?php include 'nav.inc.php'; ?>

    <div class="order-section">
        <h2>Passer une commande</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (isset($_SESSION['success'])) echo "<p style='color: green;'>" . $_SESSION['success'] . "</p>"; unset($_SESSION['success']); ?>
        
        <form method="POST">
            <h3>Produit sélectionné :</h3>
            <div class="selected-product">
                <?php
                $selectedProduct = null;
                if (!empty($preselected_product_id)) {
                    foreach ($products as $product) {
                        if ($product['id'] == $preselected_product_id) {
                            $selectedProduct = $product;
                            break;
                        }
                    }
                }
                
                if ($selectedProduct):
                    echo "<p><strong>" . htmlspecialchars($selectedProduct['name']) . "</strong> - " . htmlspecialchars($selectedProduct['price']) . " DT</p>";
                    echo "<input type='hidden' name='product_id' value='" . $selectedProduct['id'] . "'>";
                    
                    
                    if ($selectedProduct['category_name'] === 'chaussures'): ?>
                        <div id="shoes-field" class="conditional-field active">
                            <label for="shoe_size">Pointure :</label>
                            <select name="shoe_size" required>
                                <option value="40">40</option>
                                <option value="41">41</option>
                                <option value="42">42</option>
                                <option value="43">43</option>
                                <option value="44">44</option>
                            </select>
                        </div>
                    <?php elseif ($selectedProduct['category_name'] === 'ceinture'): ?>
                        <div id="belt-field" class="conditional-field active">
                            <label for="belt_length">Longueur de ceinture :</label>
                            <select name="belt_length" required>
                                <option value="1m">1 mètre</option>
                                <option value="1.5m">1.5 mètres</option>
                            </select>
                        </div>
                    <?php else: ?>
                        <div id="clothing-field" class="conditional-field active">
                            <label for="size">Taille :</label>
                            <select name="size" required>
                                <option value="m">M</option>
                                <option value="l">L</option>
                                <option value="xl">XL</option>
                                <option value="xxl">XXL</option>
                                <option value="3xl">3XL</option>
                            </select>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Produit non trouvé</p>
                <?php endif; ?>
            </div>

            <div id="clothing-field" class="conditional-field">
                <label for="size">Taille :</label>
                <select name="size">
                    <option value="m">M</option>
                    <option value="l">L</option>
                    <option value="xl">XL</option>
                    <option value="xxl">XXL</option>
                    <option value="3xl">3XL</option>
                </select>
            </div>

            <div id="shoes-field" class="conditional-field">
                <label for="shoe_size">Pointure :</label>
                <select name="shoe_size">
                    <option value="40">40</option>
                    <option value="41">41</option>
                    <option value="42">42</option>
                    <option value="43">43</option>
                    <option value="44">44</option>
                </select>
            </div>

            <div id="belt-field" class="conditional-field">
                <label for="belt_length">Longueur de ceinture :</label>
                <select name="belt_length">
                    <option value="1m">1 mètre</option>
                    <option value="1.5m">1.5 mètres</option>
                </select>
            </div>

            <label for="quantity">Quantité :</label>
            <input type="number" id="quantity" name="quantity" min="1" value="1" required>

            <label for="address">Adresse de livraison :</label>
            <textarea id="address" name="address" required></textarea>

            <label for="phone">Numéro de téléphone :</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="payment_method">Méthode de paiement :</label>
            <select id="payment_method" name="payment_method" required>
                <option value="espace">Espéce</option>
                <option value="carte">Carte</option>
            </select>

            <button type="submit">Confirmer la commande</button>
        </form>
    </div>

    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:tmshop@gmail.com">Contactez-nous</a> | <a href="tel:+50100100">Appelez-nous</a></p>
    </footer>

    <script>
        function updateConditionalFields(select) {
            document.querySelectorAll('.conditional-field').forEach(e => e.classList.remove('active'));
            const selectedOption = select.options[select.selectedIndex];
            const category = selectedOption ? selectedOption.getAttribute('data-category') : '';
            
            if (category) {
                switch(category.toLowerCase()) {
                    case 'blouson':
                    case 'pull':
                    case 'chemise':
                    case 'pantalon':
                        document.getElementById('clothing-field').classList.add('active');
                        break;
                    case 'chaussures':
                        document.getElementById('shoes-field').classList.add('active');
                        break;
                    case 'ceinture':
                        document.getElementById('belt-field').classList.add('active');
                        break;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('product_id');
            if (select) {
                const preselected = "<?php echo $preselected_product_id; ?>";
                if (preselected) {
                    const options = select.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value === preselected) {
                            select.selectedIndex = i;
                            updateConditionalFields(select);
                            break;
                        }
                    }
                } else {
                    updateConditionalFields(select);
                }
            }
        });
    </script>
</body>
</html>