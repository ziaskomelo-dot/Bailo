CREATE DATABASE IF NOT EXISTS Marketplace;
USE Marketplace;

-- Table Utilisateur (clients)
CREATE TABLE Utilisateur (
    idUtilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    motDePasse VARCHAR(255) NOT NULL,
    dateInscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table Prestataire
CREATE TABLE Prestataire (
    idPrestataire INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    motDePasse VARCHAR(255) NOT NULL,
    dateInscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table Categorie
CREATE TABLE Categorie (
    idCategorie INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Table Service
CREATE TABLE Service (
    idService INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    localisation VARCHAR(100),
    datePublication DATETIME DEFAULT CURRENT_TIMESTAMP,
    idCategorie INT,
    idPrestataire INT,
    FOREIGN KEY (idCategorie) REFERENCES Categorie(idCategorie),
    FOREIGN KEY (idPrestataire) REFERENCES Prestataire(idPrestataire)
);

-- Table Disponibilite
CREATE TABLE Disponibilite (
    idDisponibilite INT AUTO_INCREMENT PRIMARY KEY,
    debut DATETIME NOT NULL,
    fin DATETIME NOT NULL,
    idService INT,
    FOREIGN KEY (idService) REFERENCES Service(idService)
);

-- Table Reservation
CREATE TABLE Reservation (
    idReservation INT AUTO_INCREMENT PRIMARY KEY,
    dateReservation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50),
    idUtilisateur INT,
    idService INT,
    FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    FOREIGN KEY (idService) REFERENCES Service(idService)
);

-- Table Avis
CREATE TABLE Avis (
    idAvis INT AUTO_INCREMENT PRIMARY KEY,
    note INT CHECK (note >= 1 AND note <= 5),
    commentaire TEXT,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    idUtilisateur INT,
    idService INT,
    FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    FOREIGN KEY (idService) REFERENCES Service(idService)
);

-- Table Paiement
CREATE TABLE Paiement (
    idPaiement INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(10,2) NOT NULL,
    commission DECIMAL(10,2),
    statut VARCHAR(50),
    idReservation INT,
    FOREIGN KEY (idReservation) REFERENCES Reservation(idReservation)
);

-- Table Message
CREATE TABLE Message (
    idMessage INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    dateEnvoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    idExpediteur INT,
    idDestinataire INT,
    expediteurType ENUM('utilisateur', 'prestataire') NOT NULL,
    destinataireType ENUM('utilisateur', 'prestataire') NOT NULL
    -- Gestion des messages entre clients et prestataires (logique applicative à gérer dans le code)
);