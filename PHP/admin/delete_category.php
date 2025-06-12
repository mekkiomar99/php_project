<?php
require_once '../classes/Database.php';
require_once '../classes/Category.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = "ID de catégorie invalide.";
    header("Location: categories.php");
    exit;
}

try {
    $category = new Category();
    
    // Vérifier si la catégorie est utilisée par des produits
    if ($category->hasProducts($id)) {
        $_SESSION['error'] = "Impossible de supprimer cette catégorie car elle contient des produits.";
        header("Location: categories.php");
        exit;
    }

    if ($category->delete($id)) {
        $_SESSION['success'] = "Catégorie supprimée avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de la catégorie.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
}

header("Location: categories.php");
exit;