<?php
require_once 'config.php';

if (!isLoggedIn() || !isClient()) {
    redirect('login.php');
}

$pdo = getDB();
$user_id = $_SESSION['user_id'];

// Récupérer les demandes de service du client
$stmt = $pdo->prepare("
    SELECT ds.*, a.entreprise, a.id as artisan_id, u.prenom, u.nom
    FROM demandes_service ds
    JOIN artisans a ON ds.artisan_id = a.id
    JOIN users u ON ds.client_id = u.id
    WHERE ds.client_id = ?
    ORDER BY ds.created_at DESC
");
$stmt->execute([$user_id]);
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        <?php if (empty($demandes)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Aucune demande de service pour le moment.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Artisan</th>
                            <th>Service</th>
                            <th>Date demande</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandes as $demande): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($demande['entreprise']); ?></td>
                                <td><?php echo htmlspecialchars($demande['titre']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($demande['created_at'])); ?></td>
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
                                    <a href="client_devis.php?demande_id=<?php echo $demande['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-text"></i> Voir devis
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
