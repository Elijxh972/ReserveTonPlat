<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../libs/PHPMailer/Exception.php';
require '../libs/PHPMailer/PHPMailer.php';
require '../libs/PHPMailer/SMTP.php';
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    
    // --- 1. VÉRIFICATION DU DOMAINE (Cahier des Charges) ---
    if (!str_ends_with($email, '@etu.univ-antilles.fr') && !str_ends_with($email, '@univ-antilles.fr')) {
        header("Location: ../public/register.php?error=invalid_email");
        exit();
    }

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(25)); 

    try {
        // --- 2. VÉRIFICATION DOUBLON ---
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            header("Location: ../public/register.php?error=already_exists");
            exit();
        }

        // 3. Insertion en base de données
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, token_confirmation, est_verifie) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$nom, $email, $password, $token]);

        // 4. Configuration de PHPMailer (Alwaysdata)
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp-reservervotreplat.alwaysdata.net'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'votre_user_alwaysdata'; // Identifiant Alwaysdata
        $mail->Password   = 'votre_mot_de_passe';    // Mot de passe Alwaysdata
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Destinataires
        $mail->setFrom('noreply@reservervotreplat.alwaysdata.net', 'RéserveTonPlat UA');
        $mail->addAddress($email, $nom);

        // Contenu du mail
        $lien = "https://reservervotreplat.alwaysdata.net/public/confirm.php?token=" . $token;
        
        $mail->isHTML(true);
        $mail->Subject = 'Confirme ton inscription - RéserveTonPlat';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; text-align: center; border: 1px solid #005596; padding: 20px;'>
                <h1 style='color: #005596;'>Bienvenue $nom !</h1>
                <p>Merci de t'être inscrit sur <strong>RéserveTonPlat UA</strong>.</p>
                <p>Pour valider ton compte et accéder aux réservations du CROUS, clique sur le bouton ci-dessous :</p>
                <br>
                <a href='$lien' style='padding:12px 25px; background:#005596; color:white; text-decoration:none; border-radius:25px; font-weight:bold;'>Valider mon compte</a>
                <br><br>
                <p style='font-size: 0.8rem; color: #777;'>Si tu n'es pas à l'origine de cette demande, ignore ce mail.</p>
            </div>";

        $mail->send();
        header("Location: ../public/login.php?msg=check_email");

    } catch (Exception $e) {
        // En cas d'erreur SQL (doublon non géré ou autre)
        header("Location: ../public/register.php?error=db_error");
    }
}