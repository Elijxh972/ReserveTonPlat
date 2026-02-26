<?php
// 1. Force l'affichage des erreurs PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
echo "1. Script démarré...<br>";

// 2. Vérification Session
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("STOP : Tu n'es pas admin. Rôle actuel : " . ($_SESSION['user_role'] ?? 'AUCUN'));
}
echo "2. Session Admin OK...<br>";

// 3. Vérification BDD
if (!file_exists('../config/db.php')) {
    die("STOP : Le fichier ../config/db.php est introuvable.");
}
require_once('../config/db.php');
echo "3. Connexion BDD OK...<br>";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['menu_image'])) {
    echo "4. Image reçue, préparation de l'envoi à Gemini...<br>";

    $apiKey = "AIzaSyBX_ErFSWHS7aVea_fOM4vAWH3uMw-HEng";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

    $imagePath = $_FILES['menu_image']['tmp_name'];
    $imageData = base64_encode(file_get_contents($imagePath));

    $payload = [
        "contents" => [[
            "parts" => [
                ["text" => 'Analyse ce menu pour le Lundi 09 Février 2026. Réponds uniquement en JSON : {"plat_du_jour": "...", "pizza_grillade": "...", "vegetarien": "..."}'],
                ["inline_data" => ["mime_type" => "image/jpeg", "data" => $imageData]]
            ]
        ]]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    echo "5. Envoi à Gemini en cours... (patiente 5-10 sec)<br>";
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        die("ERREUR CURL : " . $error);
    }

    echo "6. Réponse reçue ! Voici le contenu brut :<br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit();
} else {
    echo "En attente d'un envoi (POST)...";
}