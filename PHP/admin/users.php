<?php
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$users = [];
$error = null;
$success = null;

try {
    $user = new User();
    $users = $user->getAll();
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des utilisateurs : " . $e->getMessage();
}

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Admin - Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h1>TM-Shop Administration</h1>
        <p>Gestion des utilisateurs</p>
    </header>
    <nav class="admin-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Utilisateurs</a>
        <a href="products.php">Produits</a>
        <a href="categories.php">Catégories</a>
        <a href="orders.php">Commandes</a>
        <a href="../logout.php">Déconnexion</a>
    </nav>
    


    <div class="admin-section">
        <h2>Gestion des Utilisateurs</h2>
        <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>

        <p><a href="add_user.php" class="button">Ajouter un utilisateur</a></p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_admin'] ? 'Oui' : 'Non'; ?></td>
                   
                    <td>
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="button delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>