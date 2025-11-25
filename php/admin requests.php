<?php
// admin/requests.php
require_once '../config.php';
requireLogin();

// Vérifier que l'utilisateur est un administrateur
if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

// Récupérer toutes les demandes de service
$stmt = $pdo->query("
    SELECT sr.*, c.full_name as client_name, a.full_name as artisan_name, s.title as service_title 
    FROM service_requests sr 
    JOIN users c ON sr.client_id = c.id 
    JOIN users a ON sr.artisan_id = a.id 
    JOIN services s ON sr.service_id = s.id 
    ORDER BY sr.created_at DESC
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la suppression de demande
if (isset($_GET['delete'])) {
    $request_id = $_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM service_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $_SESSION['admin_success'] = "Demande supprimée avec succès.";
    
    header("Location: requests.php");
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
            <h2 class="mb-4">Gestion des demandes</h2>
            
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
                                    <th>Statut</th>
                                    <th>Date demande</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo $request['id']; ?></td>
                                    <td><?php echo htmlspecialchars($request['service_title']); ?></td>
                                    <td><?php echo htmlspecialchars($request['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['artisan_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch($request['status']) {
                                                case 'pending': echo 'warning'; break;
                                                case 'accepted': echo 'success'; break;
                                                case 'rejected': echo 'danger'; break;
                                                case 'completed': echo 'info'; break;
                                            }
                                        ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($request['created_at'])); ?></td>
                                    <td>
                                        <a href="../request_detail.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-outline-primary">Détails</a>
                                        <a href="requests.php?delete=<?php echo $request['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande?')">Supprimer</a>
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