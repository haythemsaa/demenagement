<?php
session_start();
require_once __DIR__ . '/../includes/helpers.php';

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
redirect('/admin/login.php');
