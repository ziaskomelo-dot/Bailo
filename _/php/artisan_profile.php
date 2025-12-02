<?php
require_once 'config.php';

if (!isLoggedIn() || !isArtisan()) {
    redirect('login.php');
}

$pdo = getDB();
$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'artisan
$stmt = $pdo->prepare("SELECT * FROM artisans WHERE user_id = ?");
$stmt->execute([$user_id]);
$artisan = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entreprise = sanitizeInput($_POST['entreprise']);
    $description = sanitizeInput($_POST['description']);
    $tarif_horaire = sanitizeInput($_POST['tarif_horaire']);
    
    $stmt = $pdo->prepare("UPDATE artisans SET entreprise = ?, description = ?, tarif_horaire = ? WHERE user_id = ?");
    if ($stmt->execute([$entreprise, $description, $tarif_horaire, $user_id])) {
        $success = "Profil mis à jour avec succès.";
    } else {
        $error = "Erreur lors de la mise à jour du profil.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Artisan - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h2>Profil Artisan</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="entreprise" class="form-label">Nom de l'entreprise</label>
                <input type="text" class="form-control" name="entreprise" value="<?php echo htmlspecialchars($artisan['entreprise']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($artisan['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="tarif_horaire" class="form-label">Tarif horaire (€)</label>
                <input type="number" class="form-control" name="tarif_horaire" value="<?php echo htmlspecialchars($artisan['tarif_horaire']); ?>" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
