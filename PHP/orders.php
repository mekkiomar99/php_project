<?php
require_once 'classes/Database.php';
require_once 'classes/Order.php';
require_once 'classes/Session.php';

Session::start();

if (!Session::isLoggedIn()) {
    header("Location: login.php");
    exit;
}

try {
    $order = new Order();
    $user_orders = $order->getByUserId(Session::get('user_id'));
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la récupération des commandes : " . $e->getMessage();
    $user_orders = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Mes Commandes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Accueil</a>

        </nav>
    </header>

    <main>
        <h1>Mes Commandes</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (empty($user_orders)): ?>
            <div class="order-card">
                <div class="order-info">
                    <div>
                        <p>Aucune commande n'a été passée pour le moment.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($user_orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Commande #<?php echo htmlspecialchars($order['id'] ?? 'N/A'); ?></h3>
                            <p>Date : <?php 
                                echo htmlspecialchars(date('d/m/Y H:i')); 
                            ?></p>
                        </div>
                        <div class="order-status-container">
                            <p><strong>État :</strong></p>
                            <span class="order-status status-<?php echo htmlspecialchars($order['status'] ?? 'pending'); ?>">
                                <?php 
                                    $status = $order['status'] ?? 'en attente';
                                    echo htmlspecialchars($status);
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="order-info">
                        <div>
                            <p><strong>Produit :</strong> <?php echo htmlspecialchars($order['product_name'] ?? 'Non spécifié'); ?></p>
                            <p><strong>Taille :</strong> <?php echo htmlspecialchars($order['size'] ?? 'Non spécifiée'); ?></p>
                        </div>
                        <div>
                            <p><strong>Quantité :</strong> <?php echo htmlspecialchars($order['quantity'] ?? 1); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($order['phone'] ?? 'Non spécifié'); ?></p>
                        </div>
                        <div>
                            <p><strong>Adresse :</strong> <?php echo htmlspecialchars($order['address'] ?? 'Non spécifiée'); ?></p>
                            <p><strong>Méthode de paiement :</strong> <?php 
                                $paymentMethod = $order['payment_method'] ?? 'Non spécifié';
                                echo htmlspecialchars($paymentMethod);
                            ?></p>
                        </div>
                    </div>


                    <div class="order-total">
                        <div class="order-item">
                            <span><strong>Produit :</strong></span>
                            <span><?php 
                                $unitPrice = $order['total'] - $order['shipping_cost'];
                                echo htmlspecialchars(number_format($unitPrice, 2)); ?>€</span>
                        </div>
                        <div class="order-item">
                            <span><strong>Livraison :</strong></span>
                            <span><?php echo htmlspecialchars(number_format($order['shipping_cost'] ?? 7, 2)); ?>€</span>
                        </div>
                        <div class="order-item" style="border-top: 2px solid #eee; padding-top: 0.5rem;">
                            <span><strong>Total :</strong></span>
                            <span><?php echo htmlspecialchars(number_format($order['total'], 2)); ?>€</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:tmshop@gmail.com">Contactez-nous</a> | <a href="tel:+21650100100">Appelez-nous</a></p>
    </footer>
</body>
</html>
