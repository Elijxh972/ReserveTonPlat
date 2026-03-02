<?php
session_start();

// Accès réservé aux admins
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

require_once('../config/db.php');

include 'includes/header.php';
?>

<div class="content-card" style="max-width: 900px; margin: 0 auto; text-align:center;">
    <h1>Scanner une réservation</h1>
    <p style="margin-bottom: 15px;">
        Place le QR code de l'élève devant la caméra.  
        Dès qu'il est reconnu, la page de vérification s'ouvrira automatiquement.
    </p>

    <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
    <div id="qr-result" style="margin-top: 15px; font-size:0.95rem; color:#555;"></div>

    <p style="margin-top:20px;">
        <a href="dashboard.php#admin-menus" class="btn-edit-cancel">⬅ Retour au tableau de bord</a>
    </p>
</div>

<!-- Librairie de scan QR -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Affiche le résultat pour debug / info
        var resultDiv = document.getElementById('qr-result');
        resultDiv.textContent = "QR détecté : " + decodedText;

        // Si le QR contient une URL (ex: lien verify.php), on y va directement
        try {
            // On vérifie grossièrement que c'est une URL HTTP/HTTPS
            if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                window.location.href = decodedText;
            }
        } catch (e) {
            console.error(e);
        }
    }

    function onScanFailure(error) {
        // Erreurs de scan fréquentes, on peut les ignorer silencieusement pour ne pas spammer la console.
    }

    document.addEventListener('DOMContentLoaded', function () {
        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader",
            {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                rememberLastUsedCamera: true
            }
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
</script>

<?php include 'includes/footer.php'; ?>

