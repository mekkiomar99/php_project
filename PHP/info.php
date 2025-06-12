<?php
require_once 'classes/Database.php';
require_once 'classes/Session.php';

Session::start();

// Récupérer les informations de contact depuis la base de données
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM contact_info");
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $contact = [
        'email' => 'tmshop@gmail.com',
        'phone' => '+216 50 100 100',
        'address' => 'Tunis, Tunisie'
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TM-Shop | Informations</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
    <?php include 'nav.inc.php'; ?>

    <div class="info-section">
        <h1>À propos de TM-Shop</h1>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>Notre Mission</h3>
                <p>TM-Shop est votre destination de confiance pour les vêtements et accessoires de qualité pour hommes. Nous nous engageons à vous offrir des produits élégants et durables, fabriqués avec soin et attention aux détails.</p>
            </div>

            <div class="info-card">
                <h3>Nos Valeurs</h3>
                <p>Qualité, Style et Service. Nous croyons en l'importance de la qualité supérieure, du design élégant et d'un service client exceptionnel.</p>
            </div>

            <div class="info-card">
                <h3>Engagement</h3>
                <p>Nous sommes engagés à fournir des produits durables et à soutenir les artisans locaux. Chaque article est conçu pour durer et pour vous faire sentir élégant.</p>
            </div>
        </div>

        <div class="contact-info">
            <div class="contact-item">
                <span class="contact-icon">📧</span>
                <div>
                    <h3>Contactez-nous</h3>
                    <p><?php echo htmlspecialchars($contact['email']); ?></p>
                </div>
            </div>

            <div class="contact-item">
                <span class="contact-icon">📞</span>
                <div>
                    <h3>Téléphone</h3>
                    <p><?php echo htmlspecialchars($contact['phone']); ?></p>
                </div>
            </div>

            <div class="contact-item">
                <span class="contact-icon">📍</span>
                <div>
                    <h3>Adresse</h3>
                    <p><?php echo htmlspecialchars($contact['address']); ?></p>
                </div>
            </div>
        </div>

        <div class="opening-hours">
            <h3>Horaires d'ouverture</h3>
            <ul>
                <li>Lundi - Vendredi : 10h00 - 19h00</li>
                <li>Samedi : 10h00 - 17h00</li>
                <li>Dimanche : Fermé</li>
            </ul>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
