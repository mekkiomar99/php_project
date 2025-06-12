<?php
require_once '../classes/Database.php';
require_once '../classes/Order.php';
require_once '../classes/User.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['error'] = "ID de commande manquant.";
    header("Location: orders.php");
    exit;
}

try {
    $order = new Order();
    $orderData = $order->getById($id);
    if (!$orderData) {
        $_SESSION['error'] = "Commande non trouvée.";
        header("Location: orders.php");
        exit;
    }

    $user = new User();
    $users = $user->getAll();
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
    header("Location: orders.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $product = trim($_POST['product'] ?? '');
    $size = $_POST['size'] ?? null;
    $shoe_size = $_POST['shoe_size'] ?? null;
    $belt_length = $_POST['belt_length'] ?? null;
    $quantity = intval($_POST['quantity'] ?? 1);
    $address = trim($_POST['address'] ?? '');
    $shipping_cost = floatval($_POST['shipping_cost'] ?? 0.00);
    $total = floatval($_POST['total'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    if ($user_id <= 0 || empty($product) || $quantity <= 0 || empty($address) || $total <= 0 || empty($status)) {
        $_SESSION['error'] = "Tous les champs requis doivent être remplis correctement.";
    } else {
        try {
            if ($order->update($id, $user_id, $product, $size, $shoe_size, $belt_length, $quantity, $address, $shipping_cost, $total, $status)) {
                $_SESSION['success'] = "Commande mise à jour avec succès.";
                header("Location: orders.php");
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
    <title>TM-Shop | Modifier Commande</title>
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
        <h2>Modifier la commande</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Utilisateur :
                <select name="user_id" required>
                    <option value="">Sélectionner un utilisateur</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>" <?php echo $orderData['user_id'] == $user['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Produit : <input type="text" name="product" value="<?php echo htmlspecialchars($orderData['product']); ?>" required></label>
            <label>Taille :
                <select name="size">
                    <option value="">Aucune</option>
                    <option value="m" <?php echo $orderData['size'] === 'm' ? 'selected' : ''; ?>>M</option>
                    <option value="l" <?php echo $orderData['size'] === 'l' ? 'selected' : ''; ?>>L</option>
                    <option value="xl" <?php echo $orderData['size'] === 'xl' ? 'selected' : ''; ?>>XL</option>
                    <option value="xxl" <?php echo $orderData['size'] === 'xxl' ? 'selected' : ''; ?>>XXL</option>
                    <option value="3xl" <?php echo $orderData['size'] === '3xl' ? 'selected' : ''; ?>>3XL</option>
                </select>
            </label>
            <label>Pointure :
                <select name="shoe_size">
                    <option value="">Aucune</option>
                    <option value="40" <?php echo $orderData['shoe_size'] === '40' ? 'selected' : ''; ?>>40</option>
                    <option value="41" <?php echo $orderData['shoe_size'] === '41' ? 'selected' : ''; ?>>41</option>
                    <option value="42" <?php echo $orderData['shoe_size'] === '42' ? 'selected' : ''; ?>>42</option>
                    <option value="43" <?php echo $orderData['shoe_size'] === '43' ? 'selected' : ''; ?>>43</option>
                    <option value="44" <?php echo $orderData['shoe_size'] === '44' ? 'selected' : ''; ?>>44</option>
                </select>
            </label>
            <label>Longueur de ceinture :
                <select name="belt_length">
                    <option value="">Aucune</option>
                    <option value="1m" <?php echo $orderData['belt_length'] === '1m' ? 'selected' : ''; ?>>1 mètre</option>
                    <option value="1.5m" <?php echo $orderData['belt_length'] === '1.5m' ? 'selected' : ''; ?>>1.5 mètres</option>
                </select>
            </label>
            <label>Quantité : <input type="number" name="quantity" value="<?php echo htmlspecialchars($orderData['quantity']); ?>" min="1" required></label>
            <label>Adresse : <textarea name="address" required><?php echo htmlspecialchars($orderData['address']); ?></textarea></label>
            <label>Frais de livraison : <input type="number" step="0.01" name="shipping_cost" value="<?php echo htmlspecialchars($orderData['shipping_cost']); ?>" required></label>
            <label>Total : <input type="number" step="0.01" name="total" value="<?php echo htmlspecialchars($orderData['total']); ?>" required></label>
            <label>Statut :
                <select name="status" required>
                    <option value="">Sélectionner un statut</option>
                    <option value="en attente" <?php echo $orderData['status'] === 'en attente' ? 'selected' : ''; ?>>En attente</option>
                    <option value="traitée" <?php echo $orderData['status'] === 'traitée' ? 'selected' : ''; ?>>Traitée</option>
                    <option value="expédiée" <?php echo $orderData['status'] === 'expédiée' ? 'selected' : ''; ?>>Expédiée</option>
                    <option value="livrée" <?php echo $orderData['status'] === 'livrée' ? 'selected' : ''; ?>>Livrée</option>
                </select>
            </label>
            <button type="submit">Enregistrer</button>
            <a href="orders.php" class="button">Retour</a>
        </form>
    </div>
    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>