<?php
require_once 'config.php';

$pdo = getDB();
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les paramètres de recherche
$categorie_id = isset($_GET['categorie']) ? (int)$_GET['categorie'] : null;
$ville = isset($_GET['ville']) ? sanitizeInput($_GET['ville']) : '';

// Construire la requête de recherche
$query = "
    SELECT a.*, u.nom, u.prenom, u.ville, u.code_postal, 
           c.nom as categorie_nom, AVG(av.note) as moyenne_notes,
           COUNT(av.id) as nb_avis
    FROM artisans a
    JOIN users u ON a.user_id = u.id
    JOIN artisan_services ars ON a.id = ars.artisan_id
    JOIN categories c ON ars.categorie_id = c.id
    LEFT JOIN avis av ON a.id = av.artisan_id
    WHERE u.is_active = TRUE
";

$params = [];

if ($categorie_id) {
    $query .= " AND ars.categorie_id = ?";
    $params[] = $categorie_id;
}

if ($ville) {
    $query .= " AND (u.ville LIKE ? OR u.code_postal LIKE ?)";
    $params[] = "%$ville%";
    $params[] = "%$ville%";
}

$query .= " GROUP BY a.id ORDER BY moyenne_notes DESC, nb_avis DESC";

// Exécuter la recherche
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$artisans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Artisans - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .artisan-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .artisan-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .rating {
            color: #ffc107;
        }
        .search-form {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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

    <div class="container mt-4">
        <h2>Rechercher un artisan</h2>
        
        <!-- Formulaire de recherche -->
        <div class="search-form mb-4">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <select class="form-select" name="categorie">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo $categorie['id']; ?>" <?php echo $categorie_id == $categorie['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categorie['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="ville" placeholder="Ville ou code postal" value="<?php echo htmlspecialchars($ville); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>

        <!-- Résultats de recherche -->
        <h3 class="mb-4">
            <?php if ($categorie_id || $ville): ?>
                Résultats de la recherche
                <?php if ($categorie_id): ?>
                    <small class="text-muted">- <?php echo htmlspecialchars($categories[array_search($categorie_id, array_column($categories, 'id'))]['nom']); ?></small>
                <?php endif; ?>
                <?php if ($ville): ?>
                    <small class="text-muted">près de <?php echo htmlspecialchars($ville); ?></small>
                <?php endif; ?>
            <?php else: ?>
                Tous les artisans
            <?php endif; ?>
        </h3>

        <?php if (empty($artisans)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Aucun artisan trouvé pour votre recherche.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($artisans as $artisan): ?>
                    <div class="col-md-6">
                        <div class="artisan-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; font-size: 1.5rem;">
                                        <?php echo strtoupper(substr($artisan['prenom'], 0, 1) . substr($artisan['nom'], 0, 1)); ?>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($artisan['prenom'] . ' ' . $artisan['nom']); ?></h5>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($artisan['entreprise']); ?></p>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($artisan['ville'] . ' (' . $artisan['code_postal'] . ')'); ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($artisan['categorie_nom']); ?></span>
                                </div>
                                
                                <?php if ($artisan['moyenne_notes']): ?>
                                    <div class="rating mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?php echo $i <= round($artisan['moyenne_notes']) ? '-fill' : ''; ?>"></i>
                                        <?php endfor; ?>
                                        <small class="text-muted">
                                            (<?php echo round($artisan['moyenne_notes'], 1); ?> - <?php echo $artisan['nb_avis']; ?> avis)
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted mb-2">Aucun avis pour le moment</div>
                                <?php endif; ?>
                                
                                <p class="card-text small text-muted">
                                    <?php echo htmlspecialchars(substr($artisan['description'], 0, 150) . '...'); ?>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold"><?php echo formatPrice($artisan['tarif_horaire']); ?>/h</span>
                                    <a href="artisan.php?id=<?php echo $artisan['id']; ?>" class="btn btn-primary btn-sm">
                                        Voir le profil
                                    </a>
                                </div>
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
