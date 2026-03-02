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

// Gestion de la sauvegarde aprés modification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_menus'])) {
    echo "<h3>💾 Sauvegarde en base de données...</h3>";
    
    $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO menus (date_menu, plat_du_jour, pizza_grillade, vegetarien, est_visible) 
            VALUES (?, ?, ?, ?, 0)
            ON DUPLICATE KEY UPDATE 
            plat_du_jour = VALUES(plat_du_jour),
            pizza_grillade = VALUES(pizza_grillade),
            vegetarien = VALUES(vegetarien)
        ");
        
        // Dates pour la semaine en cours
        $dateObj = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $monday = $dateObj->modify('monday this week')->format('Y-m-d');
        
        $savedCount = 0;
        foreach ($days as $index => $day) {
            $platDuJour = $_POST['plat_du_jour_' . $day] ?? '';
            $pizzaGrillade = $_POST['pizza_grillade_' . $day] ?? '';
            $vegetarien = $_POST['vegetarien_' . $day] ?? '';
            
            if (!empty($platDuJour)) {
                $dateToSave = date('Y-m-d', strtotime($monday . ' +' . $index . ' days'));
                
                $stmt->execute([
                    $dateToSave,
                    $platDuJour,
                    $pizzaGrillade,
                    $vegetarien
                ]);
                $savedCount++;
            }
        }
        
        echo "✅ " . $savedCount . " jour(s) sauvegardé(s) en BDD avec succès!<br>";
        echo "<br><a href='../public/dashboard.php' style='color: #005596; text-decoration: none; font-weight: bold;'>← Retour au dashboard</a>";
    } catch (PDOException $e) {
        echo "⚠️ Erreur lors de la sauvegarde: " . htmlspecialchars($e->getMessage()) . "<br>";
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['menu_image'])) {
    echo "4. Image reçue, préparation de l'envoi à Groq...<br>";

    // Load API key from config
    if (!file_exists('../config/api_keys.php')) {
        die("STOP : Le fichier ../config/api_keys.php est introuvable.");
    }
    require_once('../config/api_keys.php');
    
    if (!defined('GROQ_API_KEY')) {
        die("STOP : La clé GROQ_API_KEY n'est pas définie dans api_keys.php");
    }
    
    $apiKey = GROQ_API_KEY;
    $url = "https://api.groq.com/openai/v1/chat/completions";

    $imagePath = $_FILES['menu_image']['tmp_name'];
    $imageData = base64_encode(file_get_contents($imagePath));

    $payload = [
        "model" => "meta-llama/llama-4-scout-17b-16e-instruct",
        "messages" => [
            [
                "role" => "user",
                "content" => [
                    [
                        "type" => "text",
                        "text" => 'Analyse ce menu de la semaine. Réponds UNIQUEMENT en JSON avec exactement cette structure, en extrayant les repas pour chaque jour (Lundi à Vendredi). Pour chaque jour, extrait 3 plats: plat_du_jour, pizza_grillade, vegetarien. Si un jour manque, mets "N/A". Réponds uniquement en JSON, pas de texte avant ou après: {"lundi": {"plat_du_jour": "...", "pizza_grillade": "...", "vegetarien": "..."}, "mardi": {...}, "mercredi": {...}, "jeudi": {...}, "vendredi": {...}}'
                    ],
                    [
                        "type" => "image_url",
                        "image_url" => [
                            "url" => "data:image/jpeg;base64," . $imageData
                        ]
                    ]
                ]
            ]
        ],
        "max_tokens" => 2048
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    echo "5. Envoi à Groq en cours... (patiente 5-10 sec)<br>";
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        die("ERREUR CURL : " . $error);
    }

    $responseData = json_decode($response, true);

    // Gestion des erreurs 429 (quota dépassé)
    if ($httpCode === 429 || (isset($responseData['error']['code']) && $responseData['error']['code'] === 429)) {
        $retryAfter = 60;
        echo "⚠️ Quota dépassé! Patiente " . $retryAfter . " secondes avant de réessayer...<br>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        exit();
    }

    echo "6. Réponse reçue ! Voici le contenu brut :<br>";
    
    // Extraire et parser le JSON
    if (isset($responseData['choices'][0]['message']['content'])) {
        $content = $responseData['choices'][0]['message']['content'];
        
        // Extraire le JSON du markdown (enlever ```json et ```)
        $jsonMatch = null;
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $jsonMatch = $matches[1];
        } else {
            $jsonMatch = $content;
        }
        
        // Parser le JSON
        $menuData = json_decode($jsonMatch, true);
        
        if ($menuData && json_last_error() === JSON_ERROR_NONE) {
            echo "<h3>✅ Semaine analysée ! Vérifiez et modifiez si nécessaire :</h3>";
            
            // Afficher un formulaire éditable
            echo "<form method='POST' action='../src/ia_scanner.php'>";
            echo "<input type='hidden' name='save_menus' value='1'>";
            echo "<input type='hidden' name='menu_json' value='" . htmlspecialchars(json_encode($menuData)) . "'>";
            
            // Afficher les 5 jours
            $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
            $dayNames = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
            
            foreach ($days as $index => $day) {
                if (isset($menuData[$day])) {
                    echo "<br><h4>" . $dayNames[$index] . "</h4>";
                    echo "<table border='1' cellpadding='8' style='margin-bottom: 15px;'>";
                    echo "<tr>";
                    echo "<td><strong>Plat du jour</strong></td>";
                    echo "<td><input type='text' name='plat_du_jour_" . $day . "' value='" . htmlspecialchars($menuData[$day]['plat_du_jour']) . "' style='width: 100%; padding: 5px;'></td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td><strong>Pizza/Grillade</strong></td>";
                    echo "<td><input type='text' name='pizza_grillade_" . $day . "' value='" . htmlspecialchars($menuData[$day]['pizza_grillade']) . "' style='width: 100%; padding: 5px;'></td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td><strong>Végétarien</strong></td>";
                    echo "<td><input type='text' name='vegetarien_" . $day . "' value='" . htmlspecialchars($menuData[$day]['vegetarien']) . "' style='width: 100%; padding: 5px;'></td>";
                    echo "</tr>";
                    echo "</table>";
                }
            }
            
            echo "<br><button type='submit' style='background: #2E7D32; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;'>💾 ENREGISTRER EN BDD</button>";
            echo "</form>";
            
        } else {
            echo "<h3>⚠️ Erreur lors du parsing du JSON</h3>";
            echo "<pre>" . htmlspecialchars($content) . "</pre>";
        }
    } else {
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
    exit();
} else {
    echo "En attente d'un envoi (POST)...";
}