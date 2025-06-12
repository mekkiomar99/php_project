<?php
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Session.php';

Session::start();

if (!Session::isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    try {
        $user = new User();
        if ($user->create($name, $email, $password, $is_admin)) {
            $success = "Utilisateur créé avec succès";
        } else {
            $error = "Erreur lors de la création de l'utilisateur";
        }
    } catch (Exception $e) {
        $error = "Erreur lors de la création : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Ajouter un Utilisateur</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header>
        <h1>TM-Shop Administration</h1>
        <p>Ajouter un utilisateur</p>
    </header>

    <?php include '../nav.inc.php'; ?>

    <div class="admin-section">
        <h2>Ajouter un Utilisateur</h2>
        <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>

        <form method="POST">
            <label for="name">Nom :</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <label>
                <input type="checkbox" name="is_admin">
                Administrateur
            </label>

            <button type="submit">Créer l'utilisateur</button>
        </form>

        <p><a href="users.php">Retour à la liste des utilisateurs</a></p>
    </div>

    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>