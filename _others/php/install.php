<?php
/**
 * Script d'installation du Marketplace Artisans & Services
 * Crée la base de données et les tables nécessaires
 */

// Configuration de la base de données
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'marketplace_artisans';

// Messages de statut
$messages = [];

try {
    // Connexion à MySQL sans base de données spécifique
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $messages[] = "✅ Connexion MySQL réussie";
    
    // Création de la base de données
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    $messages[] = "✅ Base de données '$db_name' créée avec succès";
    
    // Sélection de la base de données
    $pdo->exec("USE $db_name");
    $messages[] = "✅ Base de données sélectionnée";
    
    // Lecture du fichier SQL de structure
    $sql_file = 'database_structure.sql';
    if (file_exists($sql_file)) {
        $sql_queries = file_get_contents($sql_file);
        
        // Exécution des requêtes SQL
        $pdo->exec($sql_queries);
        $messages[] = "✅ Structure de la base de données créée avec succès";
        
        // Insertion de données de test
        insertTestData($pdo);
        $messages[] = "✅ Données de test insérées";
        
    } else {
        throw new Exception("❌ Fichier de structure '$sql_file' introuvable");
    }
    
    // Création du fichier de configuration
    createConfigFile($db_host, $db_user, $db_pass, $db_name);
    $messages[] = "✅ Fichier de configuration créé";
    
    $messages[] = "🎉 Installation terminée avec succès !";
    $messages[] = "Vous pouvez maintenant accéder à votre marketplace.";
    
} catch (PDOException $e) {
    $messages[] = "❌ Erreur de base de données: " . $e->getMessage();
} catch (Exception $e) {
    $messages[] = "❌ Erreur: " . $e->getMessage();
}

/**
 * Insertion de données de test
 */
function insertTestData($pdo) {
    // Insertion d'utilisateurs de test
    $password_hash = password_hash('password123', PASSWORD_DEFAULT);
    
    // Utilisateurs clients
    $pdo->exec("INSERT INTO users (email, password, nom, prenom, telephone, adresse, ville, code_postal, type) VALUES
        ('client1@example.com', '$password_hash', 'Dupont', 'Marie', '0123456789', '123 Rue Exemple', 'Paris', '75001', 'client'),
        ('client2@example.com', '$password_hash', 'Martin', 'Pierre', '0987654321', '456 Avenue Test', 'Lyon', '69001', 'client')");
    
    // Utilisateurs artisans
    $pdo->exec("INSERT INTO users (email, password, nom, prenom, telephone, adresse, ville, code_postal, type) VALUES
        ('artisan1@example.com', '$password_hash', 'Leroy', 'Jean', '0612345678', '789 Boulevard Artisan', 'Marseille', '13001', 'artisan'),
        ('artisan2@example.com', '$password_hash', 'Dubois', 'Sophie', '0698765432', '321 Rue Métier', 'Toulouse', '31000', 'artisan')");
    
    // Artisans
    $pdo->exec("INSERT INTO artisans (user_id, entreprise, siret, description, experience, tarif_horaire) VALUES
        (3, 'Leroy Maçonnerie', '12345678901234', 'Artisan maçon expérimenté avec 15 ans d\\'expérience. Spécialisé dans la rénovation et construction.', 15, 45.00),
        (4, 'Dubois Électricité', '98765432109876', 'Électricien qualifié pour installations résidentielles et commerciales. Certifié NF C15-100.', 8, 55.00)");
    
    // Services des artisans
    $pdo->exec("INSERT INTO artisan_services (artisan_id, categorie_id, nom_service, description, prix_min, prix_max, unite_tarif) VALUES
        (1, 1, 'Rénovation mur porteur', 'Rénovation complète de murs porteurs avec expertise', 1500.00, 5000.00, 'projet'),
        (1, 1, 'Construction cloisons', 'Construction de cloisons en placoplâtre', 300.00, 1200.00, 'projet'),
        (2, 3, 'Installation électrique complète', 'Installation électrique neuve avec mise aux normes', 2000.00, 8000.00, 'projet'),
        (2, 3, 'Dépannage électrique', 'Intervention rapide pour dépannage électrique', 60.00, 120.00, 'heure')");
}

/**
 * Création du fichier de configuration
 */
function createConfigFile($host, $user, $pass, $dbname) {
    $config_content = <<<EOT
<?php
// Configuration de la base de données
define('DB_HOST', '$host');
define('DB_USER', '$user');
define('DB_PASS', '$pass');
define('DB_NAME', '$dbname');

// Connexion à la base de données
function getDB() {
    try {
        \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return \$pdo;
    } catch (PDOException \$e) {
        die('Erreur de connexion à la base de données: ' . \$e->getMessage());
    }
}

// Démarrer la session
session_start();

// Configuration du site
define('SITE_NAME', 'Marketplace Artisans & Services');
define('SITE_URL', 'http://' . \$_SERVER['HTTP_HOST'] . dirname(\$_SERVER['PHP_SELF']));
EOT;

    file_put_contents('config.php', $config_content);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Marketplace Artisans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin-top: 50px; }
        .message-box { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .success { color: #198754; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <h1 class="display-4">🛠️ Marketplace Artisans & Services</h1>
            <p class="lead">Installation de la plateforme</p>
        </div>
        
        <div class="message-box">
            <h2 class="h4 mb-4">Résultat de l'installation</h2>
            <?php foreach ($messages as $message): ?>
                <div class="alert <?php echo strpos($message, '✅') !== false ? 'alert-success' : (strpos($message, '❌') !== false ? 'alert-danger' : 'alert-info'); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (strpos(end($messages), '❌') === false): ?>
                <div class="mt-4">
                    <h3 class="h5">Prochaines étapes:</h3>
                    <ol>
                        <li>Accédez à la <a href="index.php" class="btn btn-primary btn-sm">page d'accueil</a></li>
                        <li>Testez la connexion avec les comptes de test:
                            <ul>
                                <li>Client: client1@example.com / password123</li>
                                <li>Artisan: artisan1@example.com / password123</li>
                            </ul>
                        </li>
                        <li>Configurez votre serveur si nécessaire</li>
                    </ol>
                </div>
            <?php else: ?>
                <div class="mt-4">
                    <h3 class="h5">Résolution des problèmes:</h3>
                    <p>Vérifiez que MySQL est démarré et que les identifiants sont corrects.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
