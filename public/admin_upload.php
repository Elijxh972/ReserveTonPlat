<?php
session_start();
// Sécurité : Seul l'admin peut voir cette page
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Scanner le Menu | ReserveTonPlat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-blue: #005596;
            --vibrant-green: #2E7D32;
        }
        body { font-family: 'Segoe UI', sans-serif; background-color: #ffffff; text-align: center; padding: 50px; }
        
        .upload-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-top: 5px solid var(--primary-blue);
        }

        .logo-admin { width: 150px; margin-bottom: 20px; }

        input[type="file"] { margin: 20px 0; padding: 10px; }

        .btn-scan {
            background: linear-gradient(90deg, var(--primary-blue), var(--vibrant-green));
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }

        .loading { display: none; margin-top: 20px; color: var(--primary-blue); font-weight: bold; }
    </style>
</head>
<body>

    <div class="upload-container">
        <img src="assets/img/logo_RTP-removebg-preview.png" alt="Logo" class="logo-admin">
        <h2>Scanner le Menu</h2>
        <p>Prenez une photo propre du menu papier du CROUS.</p>

        <form action="../src/ia_scanner.php" method="POST" enctype="multipart/form-data" onsubmit="showLoading()">
            <input type="file" name="menu_image" accept="image/*" required>
            <button type="submit" class="btn-scan">LANCER L'ANALYSE IA</button>
        </form>

        <div id="loader" class="loading">⌛ L'intelligence artificielle analyse le menu... Patientez...</div>
        
        <br>
        <a href="dashboard.php" style="color: gray; text-decoration: none; font-size: 0.9rem;">← Retour au Dashboard</a>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loader').style.display = 'block';
        }
    </script>
</body>
</html>