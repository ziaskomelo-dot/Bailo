<?php
require_once 'config.php';

// Récupérer les catégories pour la recherche
$pdo = getDB();
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les artisans populaires (meilleures notes)
$popularArtisans = $pdo->query("
    SELECT a.*, u.nom, u.prenom, u.ville, AVG(av.note) as moyenne_notes
    FROM artisans a
    JOIN users u ON a.user_id = u.id
    LEFT JOIN avis av ON a.id = av.artisan_id
    GROUP BY a.id
    ORDER BY moyenne_notes DESC, a.nb_avis DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Trouvez le bon artisan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        .category-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .category-card:hover {
            transform: translateY(-5px);
        }
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
    </style>
</head>
<body>
    <!-- Navigation -->
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Trouvez l'artisan parfait près de chez vous</h1>
            <p class="lead mb-5">Des professionnels qualifiés pour tous vos projets de rénovation et réparation</p>
            
            <!-- Formulaire de recherche -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form action="search.php" method="GET" class="card p-3 shadow">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <select class="form-select" name="categorie" required>
                                    <option value="">Choisissez un service...</option>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?php echo $categorie['id']; ?>">
                                            <?php echo htmlspecialchars($categorie['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="ville" placeholder="Ville ou code postal" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Catégories -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nos catégories de services</h2>
            <div class="row g-4">
                <?php foreach ($categories as $categorie): ?>
                    <div class="col-md-3 col-6">
                        <div class="category-card card text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-<?php echo getCategoryIcon($categorie['id']); ?> display-6 text-primary"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($categorie['nom']); ?></h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($categorie['description']); ?></p>
                            <a href="search.php?categorie=<?php echo $categorie['id']; ?>" class="btn btn-outline-primary btn-sm">
                                Voir les artisans
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Artisans populaires -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Artisans les mieux notés</h2>
            <div class="row g-4">
                <?php foreach ($popularArtisans as $artisan): ?>
                    <div class="col-md-4">
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
                                    </div>
                                </div>
                                
                                <p class="card-text text-muted small">
                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($artisan['ville']); ?>
                                </p>
                                
                                <?php if ($artisan['moyenne_notes']): ?>
                                    <div class="rating mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?php echo $i <= round($artisan['moyenne_notes']) ? '-fill' : ''; ?>"></i>
                                        <?php endfor; ?>
                                        <small class="text-muted">(<?php echo round($artisan['moyenne_notes'], 1); ?>)</small>
                                    </div>
                                <?php endif; ?>
                                
                                <p class="card-text small"><?php echo htmlspecialchars(substr($artisan['description'], 0, 100) . '...'); ?></p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold"><?php echo formatPrice($artisan['tarif_horaire']); ?>/h</span>
                                    <a href="artisan.php?id=<?php echo $artisan['id']; ?>" class="btn btn-primary btn-sm">
                                        Voir profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="search.php" class="btn btn-outline-primary">
                    Voir tous les artisans
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2024 <?php echo SITE_NAME; ?>. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
