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

// Récupérer les rendez-vous de l'artisan
$rdv_stmt = $pdo->prepare("
    SELECT rv.*, ds.titre, u.prenom, u.nom, u.telephone, u.email
    FROM rendez_vous rv
    JOIN demandes_service ds ON rv.demande_id = ds.id
    JOIN users u ON rv.client_id = u.id
    WHERE rv.artisan_id = ?
    ORDER BY rv.date_rdv ASC
");
$rdv_stmt->execute([$artisan_id]);
$rendez_vous = $rdv_stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du changement de statut
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changer_statut'])) {
    $rdv_id = (int)$_POST['rdv_id'];
    $nouveau_statut = sanitizeInput($_POST['statut']);
    
    $stmt = $pdo->prepare("UPDATE rendez_vous SET statut = ? WHERE id = ? AND artisan_id = ?");
    if ($stmt->execute([$nouveau_statut, $rdv_id, $artisan_id])) {
        $success = "Statut du rendez-vous mis à jour avec succès.";
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
    <title>Mes Rendez-vous - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .rdv-passe {
            opacity: 0.7;
        }
        .rdv-urgent {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
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
        <h2>Mes Rendez-vous</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($rendez_vous)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Aucun rendez-vous planifié pour le moment.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($rendez_vous as $rdv): 
                    $is_passe = strtotime($rdv['date_rdv']) < time();
                    $is_urgent = strtotime($rdv['date_rdv']) < strtotime('+2 days') && !$is_passe;
                ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 <?php echo $is_passe ? 'rdv-passe' : ''; ?> <?php echo $is_urgent ? 'rdv-urgent' : ''; ?>">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <?php echo htmlspecialchars($rdv['titre']); ?>
                                    </h5>
                                    <span class="badge 
                                        <?php echo $rdv['statut'] == 'planifié' ? 'bg-primary' : 
                                               ($rdv['statut'] == 'confirmé' ? 'bg-success' : 
                                               ($rdv['statut'] == 'annulé' ? 'bg-danger' : 'bg-secondary')); ?>">
                                        <?php echo ucfirst($rdv['statut']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Client:</strong><br>
                                    <?php echo htmlspecialchars($rdv['prenom'] . ' ' . $rdv['nom']); ?><br>
                                    <small class="text-muted">
                                        <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($rdv['telephone']); ?><br>
                                        <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($rdv['email']); ?>
                                    </small>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Date et heure:</strong><br>
                                    <?php echo date('d/m/Y à H:i', strtotime($rdv['date_rdv'])); ?>
                                    <?php if ($is_urgent): ?>
                                        <span class="badge bg-warning text-dark ms-2">URGENT</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($rdv['duree_estimee']): ?>
                                    <div class="mb-3">
                                        <strong>Durée estimée:</strong><br>
                                        <?php echo $rdv['duree_estimee']; ?> minutes
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($rdv['adresse']): ?>
                                    <div class="mb-3">
                                        <strong>Adresse:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($rdv['adresse'])); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($rdv['notes']): ?>
                                    <div class="mb-3">
                                        <strong>Notes:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($rdv['notes'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <form method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="rdv_id" value="<?php echo $rdv['id']; ?>">
                                    <select name="statut" class="form-select form-select-sm me-2" 
                                            <?php echo $is_passe ? 'disabled' : ''; ?> onchange="this.form.submit()">
                                        <option value="planifié" <?php echo $rdv['statut'] == 'planifié' ? 'selected' : ''; ?>>Planifié</option>
                                        <option value="confirmé" <?php echo $rdv['statut'] == 'confirmé' ? 'selected' : ''; ?>>Confirmé</option>
                                        <option value="annulé" <?php echo $rdv['statut'] == 'annulé' ? 'selected' : ''; ?>>Annulé</option>
                                        <option value="terminé" <?php echo $rdv['statut'] == 'terminé' ? 'selected' : ''; ?>>Terminé</option>
                                    </select>
                                    <input type="hidden" name="changer_statut" value="1">
                                    <?php if ($is_passe): ?>
                                        <small class="text-muted ms-2">Passé</small>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
