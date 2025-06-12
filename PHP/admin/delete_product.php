<?php
require_once '../classes/Database.php';
require_once '../classes/Product.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = "ID de produit invalide.";
    header("Location: products.php");
    exit;
}

try {
    $product = new Product();
    
    // Vérifier si le produit existe
    $productData = $product->getById($id);
    if (!$productData) {
        $_SESSION['error'] = "Produit non trouvé.";
        header("Location: products.php");
        exit;
    }

    // Vérifier si le produit a des commandes associées
    if ($product->hasOrders($id)) {
        $_SESSION['error'] = "Impossible de supprimer ce produit car il a des commandes associées.";
        header("Location: products.php");
        exit;
    }

    // Supprimer l'image du produit si elle existe
    if (!empty($productData['image']) && file_exists('../' . $productData['image'])) {
        unlink('../' . $productData['image']);
    }

    if ($product->delete($id)) {
        $_SESSION['success'] = "Produit supprimé avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression du produit.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
}

header("Location: products.php");
exit;
?>