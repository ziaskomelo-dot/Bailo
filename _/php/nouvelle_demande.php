<?php
require_once 'config.php';

if (!isLoggedIn() || !isClient()) {
    redirect('login.php');
}

$pdo = getDB();
$user_id = $_SESSION['user_id'];

// Récupérer les catégories et artisans
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la nouvelle demande
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $artisan_id = (int)$_POST['artisan_id'];
    $service_id = (int)$_POST['service_id'];
    $titre = sanitizeInput($_POST['titre']);
    $description = sanitizeInput($_POST['description']);
    $adresse_intervention = sanitizeInput($_POST['adresse_intervention']);
    $date_souhaitee = sanitizeInput($_POST['date_souhaitee']);
    $budget_estime = sanitizeInput($_POST['budget_estime']);

    // Vérifier que l'artisan existe et est actif
    $stmt = $pdo->prepare("
        SELECT a.id 
        FROM artisans a 
        JOIN users u ON a.user_id = u.id 
        WHERE a.id = ? AND u.is_active = TRUE
    ");
    $stmt->execute([$artisan_id]);
    $artisan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($artisan) {
        $stmt = $pdo->prepare("
            INSERT INTO demandes_service (client_id, artisan_id, service_id, titre, description, adresse_intervention, date_souhaitee, budget_estime)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$user_id, $artisan_id, $service_id, $titre, $description, $adresse_intervention, $date_souhaitee, $budget_estime])) {
            $success = "Votre demande a été envoyée avec succès !";
            // Rediriger vers les demandes
            redirect('client_demandes.php');
        } else {
            $error = "Erreur lors de l'envoi de la demande. Veuillez réessayer.";
        }
    } else {
        $error = "Artisan non trouvé ou inactif.";
    }
}

// Récupérer les services d'un artisan spécifique
function getArtisanServices($artisan_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT ars.*, c.nom as categorie_nom
        FROM artisan_services ars
        JOIN categories c ON ars.categorie_id = c.id
        WHERE ars.artisan_id = ? AND ars.is_actif = TRUE
        ORDER BY c.nom, ars.nom_service
    ");
    $stmt->execute([$artisan_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Demande - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script>
        function loadArtisanServices(artisanId) {
            if (!artisanId) {
                document.getElementById('service_id').innerHTML = '<option value="">Sélectionnez d\'abord un artisan</option>';
                return;
            }
            
            fetch('get_services.php?artisan_id=' + artisanId)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('service_id');
                    select.innerHTML = '<option value="">Choisissez un service...</option>';
                    data.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;
                        option.textContent = service.nom_service + ' (' + service.categorie_nom + ')';
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('service_id').innerHTML = '<option value="">Erreur de chargement</option>';
                });
        }
    </script>
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
        <h2>Nouvelle Demande de Service</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Artisan</label>
                                <select class="form-select" name="artisan_id" required onchange="loadArtisanServices(this.value)">
                                    <option value="">Choisissez un artisan...</option>
                                    <?php 
                                    $artisans = $pdo->query("
                                        SELECT a.id, a.entreprise, u.prenom, u.nom, u.ville
                                        FROM artisans a
                                        JOIN users u ON a.user_id = u.id
                                        WHERE u.is_active = TRUE
                                        ORDER BY a.entreprise
                                    ")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($artisans as $artisan): 
                                    ?>
                                        <option value="<?php echo $artisan['id']; ?>">
                                            <?php echo htmlspecialchars($artisan['entreprise'] . ' - ' . $artisan['prenom'] . ' ' . $artisan['nom'] . ' (' . $artisan['ville'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Service</label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">Sélectionnez d'abord un artisan</option>
                                </select>
                            </div>
                        </div>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date souhaitée</label>
                                <input type="date" class="form-control" name="date_souhaitee">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Budget estimé (€)</label>
                                <input type="number" class="form-control" name="budget_estime" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Envoyer la demande
                        </button>
                        <a href="client_demandes.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
