<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pdo = getDB();
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user_type === 'artisan') {
    // Récupérer les informations de l'artisan
    $stmt = $pdo->prepare("SELECT * FROM artisans WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $artisan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Statistiques pour l'artisan
    $demandes = $pdo->prepare("SELECT COUNT(*) FROM demandes_service WHERE artisan_id = ?");
    $demandes->execute([$artisan['id']]);
    $total_demandes = $demandes->fetchColumn();
    
    $devis = $pdo->prepare("SELECT COUNT(*) FROM devis WHERE artisan_id = ?");
    $devis->execute([$artisan['id']]);
    $total_devis = $devis->fetchColumn();
    
    $rendez_vous = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE artisan_id = ? AND statut = 'planifié'");
    $rendez_vous->execute([$artisan['id']]);
    $total_rdv = $rendez_vous->fetchColumn();
    
} else {
    // Statistiques pour le client
    $demandes = $pdo->prepare("SELECT COUNT(*) FROM demandes_service WHERE client_id = ?");
    $demandes->execute([$user_id]);
    $total_demandes = $demandes->fetchColumn();
    
    $devis = $pdo->prepare("SELECT COUNT(*) FROM devis WHERE demande_id IN (SELECT id FROM demandes_service WHERE client_id = ?)");
    $devis->execute([$user_id]);
    $total_devis = $devis->fetchColumn();
    
    $rendez_vous = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE client_id = ? AND statut = 'planifié'");
    $rendez_vous->execute([$user_id]);
    $total_rdv = $rendez_vous->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="bi bi-tools"></i> <?php echo SITE_NAME; ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a href="logout.php" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Tableau de bord</h2>
        <p>Bienvenue, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>!</p>
        
        <?php if ($user_type === 'artisan'): ?>
            <!-- Tableau de bord Artisan -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Demandes</h5>
                            <p class="card-text display-4"><?php echo $total_demandes; ?></p>
                            <a href="artisan_demandes.php" class="btn btn-primary">Voir les demandes</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Devis</h5>
                            <p class="card-text display-4"><?php echo $total_devis; ?></p>
                            <a href="artisan_devis.php" class="btn btn-primary">Voir les devis</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Rendez-vous</h5>
                            <p class="card-text display-4"><?php echo $total_rdv; ?></p>
                            <a href="artisan_rendezvous.php" class="btn btn-primary">Voir les rendez-vous</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h3>Actions rapides</h3>
                <div class="d-grid gap-2 d-md-flex">
                    <a href="artisan_profile.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-person"></i> Modifier mon profil
                    </a>
                    <a href="artisan_services.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-gear"></i> Gérer mes services
                    </a>
                    <a href="search.php" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Rechercher des clients
                    </a>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Tableau de bord Client -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Mes demandes</h5>
                            <p class="card-text display-4"><?php echo $total_demandes; ?></p>
                            <a href="client_demandes.php" class="btn btn-primary">Voir mes demandes</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Devis reçus</h5>
                            <p class="card-text display-4"><?php echo $total_devis; ?></p>
                            <a href="client_devis.php" class="btn btn-primary">Voir les devis</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Rendez-vous</h5>
                            <p class="card-text display-4"><?php echo $total_rdv; ?></p>
                            <a href="client_rendezvous.php" class="btn btn-primary">Voir les rendez-vous</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h3>Actions rapides</h3>
                <div class="d-grid gap-2 d-md-flex">
                    <a href="client_profile.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-person"></i> Modifier mon profil
                    </a>
                    <a href="search.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search"></i> Rechercher un artisan
                    </a>
                    <a href="nouvelle_demande.php" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Nouvelle demande
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
