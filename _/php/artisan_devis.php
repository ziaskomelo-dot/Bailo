<?php
require_once 'config.php';

if (!isLoggedIn() || !isArtisan()) {
    redirect('login.php');
}

$pdo = getDB();
$user_id = $_SESSION['user_id'];

// Récupérer l'artisan connecté
$stmt = $pdo->prepare("SELECT id FROM artisans WHERE user_id = ?");
$stmt->execute([$user_id]);
$artisan = $stmt->fetch(PDO::FETCH_ASSOC);
$artisan_id = $artisan['id'];

// Récupérer la demande spécifique si demandée
$demande_id = isset($_GET['demande_id']) ? (int)$_GET['demande_id'] : null;
$demande = null;

if ($demande_id) {
    $stmt = $pdo->prepare("
        SELECT ds.*, u.prenom, u.nom, u.email, u.telephone, 
               ars.nom_service, c.nom as categorie_nom
        FROM demandes_service ds
        JOIN users u ON ds.client_id = u.id
        JOIN artisan_services ars ON ds.service_id = ars.id
        JOIN categories c ON ars.categorie_id = c.id
        WHERE ds.id = ? AND ds.artisan_id = ?
    ");
    $stmt->execute([$demande_id, $artisan_id]);
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer tous les devis de l'artisan
$devis_stmt = $pdo->prepare("
    SELECT d.*, ds.titre, u.prenom, u.nom
    FROM devis d
    JOIN demandes_service ds ON d.demande_id = ds.id
    JOIN users u ON ds.client_id = u.id
    WHERE d.artisan_id = ?
    ORDER BY d.created_at DESC
");
$devis_stmt->execute([$artisan_id]);
$devis = $devis_stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la création/modification de devis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['creer_devis'])) {
        $demande_id = (int)$_POST['demande_id'];
        $montant = (float)$_POST['montant'];
        $description = sanitizeInput($_POST['description']);
        $delai_jours = (int)$_POST['delai_jours'];
        $date_expiration = sanitizeInput($_POST['date_expiration']);

        $stmt = $pdo->prepare("
            INSERT INTO devis (demande_id, artisan_id, montant, description, delai_jours, date_expiration)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$demande_id, $artisan_id, $montant, $description, $delai_jours, $date_expiration])) {
            $success = "Devis créé avec succès !";
            // Rediriger pour éviter la resoumission du formulaire
            redirect('artisan_devis.php');
        } else {
            $error = "Erreur lors de la création du devis.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Devis - <?php echo SITE_NAME; ?></title>
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
                <a href="dashboard.php" class="btn btn-outline-primary me-2">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
                <a href="logout.php" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Gestion des Devis</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulaire de création de devis -->
        <?php if ($demande): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Créer un devis pour la demande</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Demande de :</h6>
                        <p><?php echo htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']); ?></p>
                        <h6>Service :</h6>
                        <p><?php echo htmlspecialchars($demande['nom_service']); ?></p>
                        <h6>Description client :</h6>
                        <p><?php echo nl2br(htmlspecialchars($demande['description'])); ?></p>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Montant (€)</label>
                                    <input type="number" class="form-control" name="montant" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Délai de réalisation (jours)</label>
                                    <input type="number" class="form-control" name="delai_jours" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description du devis</label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date d'expiration du devis</label>
                            <input type="date" class="form-control" name="date_expiration">
                        </div>
                        <button type="submit" name="creer_devis" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text"></i> Créer le devis
                        </button>
                        <a href="artisan_devis.php" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Liste des devis existants -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Mes Devis</h4>
            </div>
            <div class="card-body">
                <?php if (empty($devis)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Aucun devis créé pour le moment.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Demande</th>
                                    <th>Montant</th>
                                    <th>Date création</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($devis as $devi): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($devi['prenom'] . ' ' . $devi['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($devi['titre']); ?></td>
                                        <td><?php echo formatPrice($devi['montant']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($devi['created_at'])); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?php echo $devi['statut'] == 'envoyé' ? 'bg-info' : 
                                                       ($devi['statut'] == 'accepté' ? 'bg-success' : 
                                                       ($devi['statut'] == 'refusé' ? 'bg-danger' : 'bg-warning')); ?>">
                                                <?php echo ucfirst($devi['statut']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#devisModal<?php echo $devi['id']; ?>">
                                                <i class="bi bi-eye"></i> Voir
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal Détails Devis -->
                                    <div class="modal fade" id="devisModal<?php echo $devi['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Détails du devis</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Client</h6>
                                                    <p><?php echo htmlspecialchars($devi['prenom'] . ' ' . $devi['nom']); ?></p>
                                                    
                                                    <h6>Demande</h6>
                                                    <p><?php echo htmlspecialchars($devi['titre']); ?></p>
                                                    
                                                    <h6>Montant</h6>
                                                    <p class="h4 text-primary"><?php echo formatPrice($devi['montant']); ?></p>
                                                    
                                                    <h6>Description</h6>
                                                    <p><?php echo nl2br(htmlspecialchars($devi['description'])); ?></p>
                                                    
                                                    <h6>Délai de réalisation</h6>
                                                    <p><?php echo $devi['delai_jours'] ? $devi['delai_jours'] . ' jours' : 'Non spécifié'; ?></p>
                                                    
                                                    <h6>Date d'expiration</h6>
                                                    <p><?php echo $devi['date_expiration'] ? date('d/m/Y', strtotime($devi['date_expiration'])) : 'Non spécifiée'; ?></p>
                                                    
                                                    <h6>Statut</h6>
                                                    <span class="badge 
                                                        <?php echo $devi['statut'] == 'envoyé' ? 'bg-info' : 
                                                               ($devi['statut'] == 'accepté' ? 'bg-success' : 
                                                               ($devi['statut'] == 'refusé' ? 'bg-danger' : 'bg-warning')); ?>">
                                                        <?php echo ucfirst($devi['statut']); ?>
                                                    </span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
