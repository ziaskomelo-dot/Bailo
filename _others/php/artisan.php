<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    redirect('search.php');
}

$artisan_id = (int)$_GET['id'];
$pdo = getDB();

// Récupérer les informations de l'artisan
$stmt = $pdo->prepare("
    SELECT a.*, u.nom, u.prenom, u.email, u.telephone, u.adresse, u.ville, u.code_postal,
           AVG(av.note) as moyenne_notes, COUNT(av.id) as nb_avis
    FROM artisans a
    JOIN users u ON a.user_id = u.id
    LEFT JOIN avis av ON a.id = av.artisan_id
    WHERE a.id = ?
    GROUP BY a.id
");
$stmt->execute([$artisan_id]);
$artisan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artisan) {
    redirect('search.php');
}

// Récupérer les services de l'artisan
$services_stmt = $pdo->prepare("
    SELECT ars.*, c.nom as categorie_nom
    FROM artisan_services ars
    JOIN categories c ON ars.categorie_id = c.id
    WHERE ars.artisan_id = ? AND ars.is_actif = TRUE
");
$services_stmt->execute([$artisan_id]);
$services = $services_stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les avis
$avis_stmt = $pdo->prepare("
    SELECT av.*, u.prenom, u.nom
    FROM avis av
    JOIN users u ON av.client_id = u.id
    WHERE av.artisan_id = ? AND av.is_public = TRUE
    ORDER BY av.created_at DESC
    LIMIT 10
");
$avis_stmt->execute([$artisan_id]);
$avis = $avis_stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la demande de service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn() && isClient()) {
    $client_id = $_SESSION['user_id'];
    $service_id = (int)$_POST['service_id'];
    $titre = sanitizeInput($_POST['titre']);
    $description = sanitizeInput($_POST['description']);
    $adresse_intervention = sanitizeInput($_POST['adresse_intervention']);
    $date_souhaitee = sanitizeInput($_POST['date_souhaitee']);
    $budget_estime = sanitizeInput($_POST['budget_estime']);

    $stmt = $pdo->prepare("
        INSERT INTO demandes_service (client_id, artisan_id, service_id, titre, description, adresse_intervention, date_souhaitee, budget_estime)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if ($stmt->execute([$client_id, $artisan_id, $service_id, $titre, $description, $adresse_intervention, $date_souhaitee, $budget_estime])) {
        $success = "Votre demande a été envoyée avec succès !";
    } else {
        $error = "Erreur lors de l'envoi de la demande. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($artisan['prenom'] . ' ' . $artisan['nom']); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
        }
        .rating {
            color: #ffc107;
        }
        .service-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .service-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="bi bi-tools"></i> <?php echo SITE_NAME; ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a href="search.php" class="btn btn-outline-primary me-2">
                    <i class="bi bi-arrow-left"></i> Retour à la recherche
                </a>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-speedometer2"></i> Tableau de bord
                    </a>
                    <a href="logout.php" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-box-arrow-in-right"></i> Connexion
                    </a>
                    <a href="register.php" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Inscription
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- En-tête du profil -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                         style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?php echo strtoupper(substr($artisan['prenom'], 0, 1) . substr($artisan['nom'], 0, 1)); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($artisan['prenom'] . ' ' . $artisan['nom']); ?></h1>
                    <h2 class="h4"><?php echo htmlspecialchars($artisan['entreprise']); ?></h2>
                    <p class="mb-1">
                        <i class="bi bi-geo-alt"></i> 
                        <?php echo htmlspecialchars($artisan['adresse'] . ', ' . $artisan['ville'] . ' ' . $artisan['code_postal']); ?>
                    </p>
                    <p class="mb-1">
                        <i class="bi bi-telephone"></i> 
                        <?php echo htmlspecialchars($artisan['telephone']); ?>
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <?php if ($artisan['moyenne_notes']): ?>
                        <div class="rating display-6 mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?php echo $i <= round($artisan['moyenne_notes']) ? '-fill' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="mb-0"><?php echo round($artisan['moyenne_notes'], 1); ?> sur 5</p>
                        <small class="text-white-50">(<?php echo $artisan['nb_avis']; ?> avis)</small>
                    <?php else: ?>
                        <p class="text-white-50">Aucun avis pour le moment</p>
                    <?php endif; ?>
                    <p class="mt-2">
                        <span class="badge bg-light text-dark fs-6">
                            <?php echo formatPrice($artisan['tarif_horaire']); ?>/h
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Description -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">À propos</h4>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($artisan['description'])); ?></p>
                        <p class="text-muted">
                            <i class="bi bi-briefcase"></i> 
                            <?php echo $artisan['experience']; ?> ans d'expérience
                        </p>
                    </div>
                </div>

                <!-- Services proposés -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">Services proposés</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($services as $service): ?>
                                <div class="col-md-6">
                                    <div class="service-card card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($service['nom_service']); ?></h5>
                                            <p class="card-text text-muted small">
                                                <?php echo htmlspecialchars($service['categorie_nom']); ?>
                                            </p>
                                            <p class="card-text">
                                                <?php if ($service['prix_min'] && $service['prix_max']): ?>
                                                    <span class="text-primary fw-bold">
                                                        <?php echo formatPrice($service['prix_min']); ?> - <?php echo formatPrice($service['prix_max']); ?>
                                                    </span>
                                                    <?php if ($service['unite_tarif']): ?>
                                                        <small class="text-muted">/<?php echo $service['unite_tarif']; ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Sur devis</span>
                                                <?php endif; ?>
                                            </p>
                                            <p class="card-text small">
                                                <?php echo htmlspecialchars(substr($service['description'], 0, 100) . '...'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Avis -->
                <?php if (!empty($avis)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Avis clients</h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($avis as $avi): ?>
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($avi['prenom'] . ' ' . $avi['nom']); ?></h6>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?php echo $i <= $avi['note'] ? '-fill' : ''; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-1">
                                        <?php echo date('d/m/Y', strtotime($avi['created_at'])); ?>
                                    </p>
                                    <p class="mb-0"><?php echo htmlspecialchars($avi['commentaire']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Formulaire de demande -->
            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h4 class="mb-0">Demander un service</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isLoggedIn() && isClient()): ?>
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Service souhaité</label>
                                    <select class="form-select" name="service_id" required>
                                        <option value="">Choisissez un service...</option>
                                        <?php foreach ($services as $service): ?>
                                            <option value="<?php echo $service['id']; ?>">
                                                <?php echo htmlspecialchars($service['nom_service']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Titre de la demande</label>
                                    <input type="text" class="form-control" name="titre" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description détaillée</label>
                                    <textarea class="form-control" name="description" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Adresse d'intervention</label>
                                    <input type="text" class="form-control" name="adresse_intervention">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date souhaitée</label>
                                    <input type="date" class="form-control" name="date_souhaitee">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Budget estimé (€)</label>
                                    <input type="number" class="form-control" name="budget_estime" step="0.01">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-send"></i> Envoyer la demande
                                </button>
                            </form>
                        <?php elseif (isLoggedIn() && isArtisan()): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Vous êtes connecté en tant qu'artisan. Cette fonctionnalité est réservée aux clients.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Vous devez être connecté en tant que client pour faire une demande de service.
                                <div class="mt-2">
                                    <a href="login.php" class="btn btn-primary btn-sm me-2">Se connecter</a>
                                    <a href="register.php" class="btn btn-outline-primary btn-sm">Créer un compte</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
