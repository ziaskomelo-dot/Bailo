<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nom = sanitizeInput($_POST['nom']);
    $prenom = sanitizeInput($_POST['prenom']);
    $telephone = sanitizeInput($_POST['telephone']);
    $adresse = sanitizeInput($_POST['adresse']);
    $ville = sanitizeInput($_POST['ville']);
    $code_postal = sanitizeInput($_POST['code_postal']);
    $type = $_POST['type'];

    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO users (email, password, nom, prenom, telephone, adresse, ville, code_postal, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$email, $password, $nom, $prenom, $telephone, $adresse, $ville, $code_postal, $type])) {
        redirect('login.php');
    } else {
        $error = "Erreur lors de l'inscription. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Inscription</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" name="prenom" required>
            </div>
            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="text" class="form-control" name="telephone">
            </div>
            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <input type="text" class="form-control" name="adresse">
            </div>
            <div class="mb-3">
                <label for="ville" class="form-label">Ville</label>
                <input type="text" class="form-control" name="ville">
            </div>
            <div class="mb-3">
                <label for="code_postal" class="form-label">Code Postal</label>
                <input type="text" class="form-control" name="code_postal">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type d'utilisateur</label>
                <select class="form-select" name="type" required>
                    <option value="client">Client</option>
                    <option value="artisan">Artisan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
        <p class="mt-3">Déjà un compte? <a href="login.php">Connectez-vous ici</a>.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
