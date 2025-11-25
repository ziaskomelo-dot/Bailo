<?php
require_once 'config.php';

$pdo = getDB();

echo "Ajout de données de test...\n";

// Ajouter des utilisateurs de test
$users = [
    ['client1@test.com', 'Client', 'Test', 'password123', 'client', '123 Rue Test', 'Paris', '75001', '0123456789'],
    ['artisan1@test.com', 'Artisan', 'Dupont', 'password123', 'artisan', '456 Avenue Artisan', 'Lyon', '69001', '0987654321'],
    ['artisan2@test.com', 'Artisan', 'Martin', 'password123', 'artisan', '789 Boulevard Métier', 'Marseille', '13001', '0654321098']
];

foreach ($users as $user) {
    $stmt = $pdo->prepare("INSERT INTO users (email, prenom, nom, password, role, adresse, ville, code_postal, telephone, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)");
    $stmt->execute($user);
    echo "Utilisateur ajouté: {$user[0]}\n";
}

// Ajouter des artisans
$artisans = [
    [2, 'Entreprise Dupont', 'Spécialiste en plomberie et électricité', 15, 45.00],
    [3, 'Artisanat Martin', 'Menuiserie et ébénisterie de qualité', 8, 35.00]
];

foreach ($artisans as $artisan) {
    $stmt = $pdo->prepare("INSERT INTO artisans (user_id, entreprise, description, experience, tarif_horaire) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute($artisan);
    echo "Artisan ajouté: {$artisan[1]}\n";
}

// Ajouter des catégories
$categories = ['Plomberie', 'Électricité', 'Menuiserie', 'Peinture', 'Jardinage'];
foreach ($categories as $categorie) {
    $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
    $stmt->execute([$categorie]);
    echo "Catégorie ajoutée: $categorie\n";
}

// Ajouter des services pour les artisans
$services = [
    [1, 1, 'Réparation fuite d\'eau', 'Réparation de fuites et installation de robinetterie', 50.00, 150.00, 'forfait'],
    [1, 2, 'Installation électrique', 'Installation de prises et interrupteurs', 80.00, 200.00, 'forfait'],
    [2, 3, 'Fabrication meuble sur mesure', 'Création de meubles personnalisés', 300.00, 1000.00, 'forfait'],
    [2, 4, 'Peinture intérieure', 'Peinture de pièces et appartements', 20.00, 40.00, 'm²']
];

foreach ($services as $service) {
    $stmt = $pdo->prepare("INSERT INTO artisan_services (artisan_id, categorie_id, nom_service, description, prix_min, prix_max, unite_tarif, is_actif) VALUES (?, ?, ?, ?, ?, ?, ?, TRUE)");
    $stmt->execute($service);
    echo "Service ajouté: {$service[2]}\n";
}

echo "Données de test ajoutées avec succès!\n";
?>
