<?php
// admin/reviews.php
require_once '../config.php';
requireLogin();

// Vérifier que l'utilisateur est un administrateur
if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

// Récupérer tous les avis
$stmt = $pdo->query("
    SELECT r.*, c.full_name as client_name, a.full_name as artisan_name, s.title as service_title 
    FROM reviews r 
    JOIN users c ON r.client_id = c.id 
    JOIN users a ON r.artisan_id = a.id 
    JOIN service_requests sr ON r.service_request_id = sr.id 
    JOIN services s ON sr.service_id = s.id 
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la suppression d'avis
if (isset($_GET['delete'])) {
    $review_id = $_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$review_id]);
    $_SESSION['admin_success'] = "Avis supprimé avec succès.";
    
    header("Location: reviews.php");
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
            <h2 class="mb-4">Gestion des avis</h2>
            
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
                                    <th>Service</th>
                                    <th>Client</th>
                                    <th>Artisan</th>
                                    <th>Note</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?php echo $review['id']; ?></td>
                                    <td><?php echo htmlspecialchars($review['service_title']); ?></td>
                                    <td><?php echo htmlspecialchars($review['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($review['artisan_name']); ?></td>
                                    <td>
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $review['rating']) {
                                                echo '<i class="fas fa-star text-warning"></i>';
                                            } else {
                                                echo '<i class="far fa-star text-warning"></i>';
                                            }
                                        }
                                        ?>
                                        (<?php echo $review['rating']; ?>/5)
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $review['id']; ?>">
                                            Voir
                                        </button>
                                        <a href="reviews.php?delete=<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis?')">Supprimer</a>
                                    </td>
                                </tr>
                                
                                <!-- Modal pour voir l'avis -->
                                <div class="modal fade" id="reviewModal<?php echo $review['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Avis de <?php echo htmlspecialchars($review['client_name']); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>Note:</strong>
                                                    <?php
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $review['rating']) {
                                                            echo '<i class="fas fa-star text-warning"></i>';
                                                        } else {
                                                            echo '<i class="far fa-star text-warning"></i>';
                                                        }
                                                    }
                                                    ?>
                                                    (<?php echo $review['rating']; ?>/5)
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Commentaire:</strong>
                                                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Date:</strong>
                                                    <p><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Service:</strong>
                                                    <p><?php echo htmlspecialchars($review['service_title']); ?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Artisan:</strong>
                                                    <p><?php echo htmlspecialchars($review['artisan_name']); ?></p>
                                                </div>
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
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>