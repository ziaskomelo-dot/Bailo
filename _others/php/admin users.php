<?php
// admin/users.php
require_once '../config.php';
requireLogin();

// Vérifier que l'utilisateur est un administrateur
if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

// Récupérer tous les utilisateurs
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la suppression d'utilisateur
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Ne pas permettre la suppression de soi-même
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $_SESSION['admin_success'] = "Utilisateur supprimé avec succès.";
    } else {
        $_SESSION['admin_error'] = "Vous ne pouvez pas supprimer votre propre compte.";
    }
    
    header("Location: users.php");
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
            <h2 class="mb-4">Gestion des utilisateurs</h2>
            
            <?php if (isset($_SESSION['admin_success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['admin_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch($user['user_type']) {
                                                case 'admin': echo 'danger'; break;
                                                case 'artisan': echo 'primary'; break;
                                                case 'client': echo 'success'; break;
                                            }
                                        ?>">
                                            <?php echo ucfirst($user['user_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="user_detail.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary">Voir</a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">Supprimer</a>
                                        <?php endif; ?>
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