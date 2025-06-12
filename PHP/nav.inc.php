<?php
require_once 'classes/Session.php';

Session::start();
?>
<nav class="main-nav">
    <div class="nav-container">
        <div class="nav-left">
            <a href="index.php" class="nav-link">Accueil</a>
            <a href="articles.php?categorie=vêtements" class="nav-link">Vêtements</a>
            <a href="articles.php?categorie=accessoires" class="nav-link">Accessoires</a>
            <a href="articles.php?categorie=chaussures" class="nav-link">Chaussures</a>
            <a href="orders.php" class="nav-link">Mes Commandes</a>
            <a href="info.php" class="nav-link">Infos de Boutique</a>
        </div>
        <div class="nav-right">
            <?php if (Session::isLoggedIn()): ?>
                <span class="user-welcome">Bienvenue, <?php echo htmlspecialchars(Session::getUserName()); ?></span>
                <a href="logout.php" class="nav-link logout">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="nav-link login">Connexion</a>
                <a href="register.php" class="nav-link register">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

