<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Session.php';

Session::start();

if (Session::isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $user = new User();
        if ($user->login($email, $password)) {
            $redirect = $_GET['redirect'] ?? 'index.php';
            header("Location: $redirect");
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Connexion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue à TM-Shop</h1>
        <p>Découvrez nos collections élégantes pour hommes</p>
    </header>

    <?php include 'nav.inc.php'; ?>

    <div class="order-section">
        <h2>Connexion</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (isset($_GET['success'])) echo "<p style='color: green;'>" . htmlspecialchars($_GET['success']) . "</p>"; ?>
        <form method="POST">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <p>Pas de compte ? <a href="register.php">Inscrivez-vous</a></p>
    </div>

    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>