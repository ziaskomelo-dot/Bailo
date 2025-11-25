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

// Récupérer les demandes de service
$demandes_stmt = $pdo->prepare("
    SELECT ds.*, u.prenom, u.nom, u.ville, ars.nom_service, c.nom as categorie_nom
    FROM demandes_service ds
    JOIN users u ON ds.client_id = u.id
    JOIN artisan_services ars ON ds.service_id = ars.id
    JOIN categories c ON ars.categorie_id = c.id
    WHERE ds.artisan_id = ?
    ORDER BY ds.created_at DESC
");
$demandes_stmt->execute([$artisan_id]);
$demandes = $demandes_stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du changement de statut
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changer_statut'])) {
    $demande_id = (int)$_POST['demande_id'];
    $nouveau_statut = sanitizeInput($_POST['statut']);
    
    $stmt = $pdo->prepare("UPDATE demandes_service SET statut = ? WHERE id = ? AND artisan_id = ?");
    if ($stmt->execute([$nouveau_statut, $demande_id, $artisan_id])) {
        $success = "Statut de la demande mis à jour avec succès.";
    } else {
        $error = "Erreur lors de la mise à jour du statut.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Demandes - <?php echo SITE_NAME; ?></title>
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
        <h2>Mes Demandes de Service</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($demandes)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Aucune demande de service pour le moment.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Date demande</th>
                            <th>Budget estimé</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandes as $demande): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($demande['ville']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($demande['nom_service']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($demande['categorie_nom']); ?></small>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($demande['created_at'])); ?></td>
                                <td>
                                    <?php if ($demande['budget_estime']): ?>
                                        <?php echo formatPrice($demande['budget_estime']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Non spécifié</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge 
                                        <?php echo $demande['statut'] == 'en_attente' ? 'bg-warning' : 
                                               ($demande['statut'] == 'acceptée' ? 'bg-success' : 
                                               ($demande['statut'] == 'refusée' ? 'bg-danger' : 
                                               ($demande['statut'] == 'annulée' ? 'bg-secondary' : 'bg-info'))); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $demande['statut'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $demande['id']; ?>">
                                            <i class="bi bi-eye"></i> Détails
                                        </button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                                            <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="en_attente" <?php echo $demande['statut'] == 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                                                <option value="acceptée" <?php echo $demande['statut'] == 'acceptée' ? 'selected' : ''; ?>>Acceptée</option>
                                                <option value="refusée" <?php echo $demande['statut'] == 'refusée' ? 'selected' : ''; ?>>Refusée</option>
                                                <option value="annulée" <?php echo $demande['statut'] == 'annulée' ? 'selected' : ''; ?>>Annulée</option>
                                            </select>
                                            <input type="hidden" name="changer_statut" value="1">
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Détails -->
                            <div class="modal fade" id="detailModal<?php echo $demande['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Détails de la demande</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h6>Client</h6>
                                            <p><?php echo htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']); ?></p>
                                            
                                            <h6>Service demandé</h6>
                                            <p><?php echo htmlspecialchars($demande['nom_service'] . ' (' . $demande['categorie_nom'] . ')'); ?></p>
                                            
                                            <h6>Titre</h6>
                                            <p><?php echo htmlspecialchars($demande['titre']); ?></p>
                                            
                                            <h6>Description</h6>
                                            <p><?php echo nl2br(htmlspecialchars($demande['description'])); ?></p>
                                            
                                            <h6>Adresse d'intervention</h6>
                                            <p><?php echo htmlspecialchars($demande['adresse_intervention']); ?></p>
                                            
                                            <h6>Date souhaitée</h6>
                                            <p><?php echo $demande['date_souhaitee'] ? date('d/m/Y', strtotime($demande['date_souhaitee'])) : 'Non spécifiée'; ?></p>
                                            
                                            <h6>Budget estimé</h6>
                                            <p><?php echo $demande['budget_estime'] ? formatPrice($demande['budget_estime']) : 'Non spécifié'; ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            <a href="artisan_devis.php?demande_id=<?php echo $demande['id']; ?>" class="btn btn-primary">
                                                <i class="bi bi-file-earmark-text"></i> Créer un devis
                                            </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
