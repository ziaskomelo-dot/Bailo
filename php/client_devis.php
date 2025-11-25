<?php
require_once 'config.php';

if (!isLoggedIn() || !isClient()) {
    redirect('login.php');
}

$pdo = getDB();
$user_id = $_SESSION['user_id'];

// Récupérer les devis du client
$devis_stmt = $pdo->prepare("
    SELECT d.*, ds.titre, a.entreprise, u.prenom, u.nom as artisan_nom
    FROM devis d
    JOIN demandes_service ds ON d.demande_id = ds.id
    JOIN artisans a ON d.artisan_id = a.id
    JOIN users u ON a.user_id = u.id
    WHERE ds.client_id = ?
    ORDER BY d.created_at DESC
");
$devis_stmt->execute([$user_id]);
$devis = $devis_stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de l'acceptation/refus de devis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changer_statut'])) {
    $devis_id = (int)$_POST['devis_id'];
    $nouveau_statut = sanitizeInput($_POST['statut']);
    
    $stmt = $pdo->prepare("UPDATE devis SET statut = ? WHERE id = ?");
    if ($stmt->execute([$nouveau_statut, $devis_id])) {
        $success = "Statut du devis mis à jour avec succès.";
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
        <h2>Mes Devis</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($devis)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Aucun devis reçu pour le moment.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($devis as $devi): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($devi['titre']); ?></h5>
                                    <span class="badge 
                                        <?php echo $devi['statut'] == 'envoyé' ? 'bg-info' : 
                                               ($devi['statut'] == 'accepté' ? 'bg-success' : 
                                               ($devi['statut'] == 'refusé' ? 'bg-danger' : 'bg-warning')); ?>">
                                        <?php echo ucfirst($devi['statut']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Artisan:</strong><br>
                                    <?php echo htmlspecialchars($devi['entreprise']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($devi['prenom'] . ' ' . $devi['artisan_nom']); ?></small>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Montant:</strong><br>
                                    <span class="h4 text-primary"><?php echo formatPrice($devi['montant']); ?></span>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Description:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($devi['description'])); ?>
                                </div>
                                
                                <?php if ($devi['delai_jours']): ?>
                                    <div class="mb-3">
                                        <strong>Délai de réalisation:</strong><br>
                                        <?php echo $devi['delai_jours']; ?> jours
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($devi['date_expiration']): ?>
                                    <div class="mb-3">
                                        <strong>Date d'expiration:</strong><br>
                                        <?php echo date('d/m/Y', strtotime($devi['date_expiration'])); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <strong>Date de création:</strong><br>
                                    <?php echo date('d/m/Y', strtotime($devi['created_at'])); ?>
                                </div>
                            </div>
                            <div class="card-footer">
                                <?php if ($devi['statut'] == 'envoyé'): ?>
                                    <form method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="devis_id" value="<?php echo $devi['id']; ?>">
                                        <button type="submit" name="changer_statut" value="accepté" class="btn btn-success btn-sm me-2">
                                            <i class="bi bi-check-circle"></i> Accepter
                                        </button>
                                        <button type="submit" name="changer_statut" value="refusé" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-circle"></i> Refuser
                                        </button>
                                        <input type="hidden" name="statut" value="">
                                    </form>
                                <?php else: ?>
                                    <small class="text-muted">
                                        Statut: <?php echo ucfirst($devi['statut']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('button[name="changer_statut"]');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    const statutInput = form.querySelector('input[name="statut"]');
                    statutInput.value = this.value;
                });
            });
        });
    </script>
</body>
</html>
