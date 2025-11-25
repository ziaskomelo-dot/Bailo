<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['artisan_id'])) {
    echo json_encode([]);
    exit;
}

$artisan_id = (int)$_GET['artisan_id'];
$pdo = getDB();

// Récupérer les services de l'artisan
$stmt = $pdo->prepare("
    SELECT ars.*, c.nom as categorie_nom
    FROM artisan_services ars
    JOIN categories c ON ars.categorie_id = c.id
    WHERE ars.artisan_id = ? AND ars.is_actif = TRUE
    ORDER BY c.nom, ars.nom_service
");
$stmt->execute([$artisan_id]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($services);
?>
