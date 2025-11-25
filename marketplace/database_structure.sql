-- Structure de la base de données pour le Marketplace Artisans & Services
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS marketplace_artisans CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE marketplace_artisans;

-- Table des catégories de services
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    icone VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des utilisateurs (base commune)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    ville VARCHAR(100),
    code_postal VARCHAR(10),
    type ENUM('artisan', 'client') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des artisans (extension de users)
CREATE TABLE artisans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    entreprise VARCHAR(255),
    siret VARCHAR(14) UNIQUE,
    description TEXT,
    experience INT DEFAULT 0,
    tarif_horaire DECIMAL(10,2),
    note_moyenne DECIMAL(3,2) DEFAULT 0.00,
    nb_avis INT DEFAULT 0,
    photos TEXT, -- JSON array des photos
    certificats TEXT, -- JSON array des certificats
    disponibilite ENUM('disponible', 'occupé', 'indisponible') DEFAULT 'disponible',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des services proposés par les artisans
CREATE TABLE artisan_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    artisan_id INT NOT NULL,
    categorie_id INT NOT NULL,
    nom_service VARCHAR(255) NOT NULL,
    description TEXT,
    prix_min DECIMAL(10,2),
    prix_max DECIMAL(10,2),
    unite_tarif ENUM('heure', 'jour', 'projet', 'unité'),
    is_actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (artisan_id) REFERENCES artisans(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des demandes de service
CREATE TABLE demandes_service (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    artisan_id INT NOT NULL,
    service_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    adresse_intervention TEXT,
    date_souhaitee DATE,
    budget_estime DECIMAL(10,2),
    statut ENUM('en_attente', 'acceptée', 'refusée', 'annulée', 'terminée') DEFAULT 'en_attente',
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (artisan_id) REFERENCES artisans(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES artisan_services(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des devis
CREATE TABLE devis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    demande_id INT NOT NULL,
    artisan_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    description TEXT,
    delai_jours INT,
    statut ENUM('envoyé', 'accepté', 'refusé', 'modifié') DEFAULT 'envoyé',
    date_expiration DATE,
    FOREIGN KEY (demande_id) REFERENCES demandes_service(id) ON DELETE CASCADE,
    FOREIGN KEY (artisan_id) REFERENCES artisans(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des rendez-vous
CREATE TABLE rendez_vous (
    id INT PRIMARY KEY AUTO_INCREMENT,
    demande_id INT NOT NULL,
    artisan_id INT NOT NULL,
    client_id INT NOT NULL,
    date_rdv DATETIME NOT NULL,
    duree_estimee INT, -- en minutes
    adresse TEXT,
    statut ENUM('planifié', 'confirmé', 'annulé', 'terminé') DEFAULT 'planifié',
    notes TEXT,
    FOREIGN KEY (demande_id) REFERENCES demandes_service(id) ON DELETE CASCADE,
    FOREIGN KEY (artisan_id) REFERENCES artisans(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des avis et notes
CREATE TABLE avis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    artisan_id INT NOT NULL,
    demande_id INT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    is_public BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (artisan_id) REFERENCES artisans(id) ON DELETE CASCADE,
    FOREIGN KEY (demande_id) REFERENCES demandes_service(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des messages
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    demande_id INT,
    sujet VARCHAR(255),
    message TEXT NOT NULL,
    is_lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (expediteur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (demande_id) REFERENCES demandes_service(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion des catégories de base
INSERT INTO categories (nom, description, icone) VALUES
('Maçonnerie', 'Travaux de maçonnerie, construction, rénovation', 'bricks'),
('Menuiserie', 'Travaux de menuiserie, ébénisterie, agencement', 'carpenter'),
('Électricité', 'Installation et réparation électrique', 'bolt'),
('Plomberie', 'Installation et réparation de plomberie', 'faucet'),
('Peinture', 'Travaux de peinture et décoration', 'paint-roller'),
('Carrelage', 'Pose et rénovation de carrelage', 'tile'),
('Toiture', 'Travaux de toiture et couverture', 'house'),
('Jardinage', 'Entretien et aménagement paysager', 'tree');

-- Création des index pour optimiser les performances
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_type ON users(type);
CREATE INDEX idx_artisans_user_id ON artisans(user_id);
CREATE INDEX idx_demandes_statut ON demandes_service(statut);
CREATE INDEX idx_devis_statut ON devis(statut);
CREATE INDEX idx_rdv_date ON rendez_vous(date_rdv);
CREATE INDEX idx_avis_artisan ON avis(artisan_id);
