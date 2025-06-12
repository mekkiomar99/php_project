<?php
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: admin_login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = "ID utilisateur invalide.";
    header("Location: users.php");
    exit;
}

// Empêcher la suppression de son propre compte
if ($id === $_SESSION['admin_id']) {
    $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte administrateur.";
    header("Location: users.php");
    exit;
}

try {
    $user = new User();
    
    // Vérifier si l'utilisateur a des commandes en cours
    if ($user->hasActiveOrders($id)) {
        $_SESSION['error'] = "Impossible de supprimer cet utilisateur car il a des commandes en cours.";
        header("Location: users.php");
        exit;
    }

    if ($user->delete($id)) {
        $_SESSION['success'] = "Utilisateur supprimé avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
}

header("Location: users.php");
exit;