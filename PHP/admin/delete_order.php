<?php
require_once '../classes/Database.php';
require_once '../classes/Order.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = "ID de commande invalide.";
    header("Location: orders.php");
    exit;
}

try {
    $order = new Order();
    
    // Vérifier si la commande existe
    $orderData = $order->getById($id);
    if (!$orderData) {
        $_SESSION['error'] = "Commande non trouvée.";
        header("Location: orders.php");
        exit;
    }

    // Vérifier si la commande peut être supprimée (statut)
    if ($orderData['status'] === 'livrée') {
        $_SESSION['error'] = "Impossible de supprimer une commande déjà livrée.";
        header("Location: orders.php");
        exit;
    }

    if ($order->delete($id)) {
        $_SESSION['success'] = "Commande supprimée avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de la commande.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
}

header("Location: orders.php");
exit;
?>