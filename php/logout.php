<?php
require_once 'config.php';

// Détruire toutes les données de session
session_destroy();

// Rediriger vers la page d'accueil
redirect('index.php');
?>
