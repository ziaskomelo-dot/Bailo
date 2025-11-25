<?php
// admin/services.php
require_once '../config.php';
requireLogin();

// Vérifier que l'utilisateur est un administrateur
if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

// Récupérer tous les services avec les informations des artisans
$stmt = $pdo->query("
    SELECT s.*, u.full_name as artisan_name 
    FROM services s 
    JOIN users u ON s.artisan_id = u.id 
    ORDER BY s.created_at DESC
");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la suppression de service
if (isset($_GET['delete'])) {
    $service_id = $_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $_SESSION['admin_success'] = "Service supprimé avec succès.";
    
    header("Location: services.php");
    exit();
}

include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 admin-nav">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10 admin-content">
            <h2 class="mb-4">Gestion des services</h2>
            
            <?php if (isset($_SESSION['admin_success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Catégorie</th>
                                    <th>Artisan</th>
                                    <th>Prix</th>
                                    <th>Création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?php echo $service['id']; ?></td>
                                    <td><?php echo htmlspecialchars($service['title']); ?></td>
                                    <td><?php echo htmlspecialchars($service['category']); ?></td>
                                    <td><?php echo htmlspecialchars($service['artisan_name']); ?></td>
                                    <td><?php echo !empty($service['price_range']) ? htmlspecialchars($service['price_range']) : 'Non spécifié'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($service['created_at'])); ?></td>
                                    <td>
                                        <a href="../artisan_detail.php?id=<?php echo $service['artisan_id']; ?>" class="btn btn-sm btn-outline-primary">Voir artisan</a>
                                        <a href="services.php?delete=<?php echo $service['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service?')">Supprimer</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>