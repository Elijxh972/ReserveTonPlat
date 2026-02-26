<?php
session_start();
// Détruire la session
session_destroy();
// Rediriger vers l'accueil
header("Location: ../public/index.html");
exit();
