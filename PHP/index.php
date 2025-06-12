<?php
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';
require_once 'classes/Session.php';

Session::start();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Accueil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header>
        <h1>Bienvenue à TM-Shop</h1>
        <p>Découvrez nos collections élégantes pour hommes</p>
    </header>


    <?php include 'nav.inc.php'; ?>

</body>
</html>
