<?php
require_once '../classes/Database.php';
require_once '../classes/Admin.php';
require_once '../classes/Session.php';

Session::start();

if (Session::isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $admin = new Admin();
    $adminData = $admin->adminLogin($email, $password);

    if ($adminData) {
        $_SESSION['admin_id'] = $adminData['id'];
        $_SESSION['admin_name'] = $adminData['name'];
        $_SESSION['last_activity'] = time();

        $admin->updateLastLogin($adminData['id']);

        session_regenerate_id(true);
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Connexion Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h1>Bienvenue à TM-Shop</h1>
        <p>Connexion Administrateur</p>
    </header>
    <div class="order-section">
        <h2>Connexion Admin</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
    <footer>
        <p>© 2025 TM-Shop. Tous droits réservés.</p>
        <p><a href="mailto:bousbiathamer@gmail.com">Contactez-nous</a> | <a href="tel:+21628287012">Appelez-nous</a></p>
    </footer>
</body>
</html>