<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../libs/PHPMailer/Exception.php';
require '../libs/PHPMailer/PHPMailer.php';
require '../libs/PHPMailer/SMTP.php';
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(25)); // Génération du token

    try {
        // 1. Insertion en base de données
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, token_confirmation) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $password, $token]);

        // 2. Configuration de PHPMailer avec les infos Alwaysdata
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp-reservervotreplat.alwaysdata.net'; // À vérifier sur votre panel
        $mail->SMTPAuth   = true;
        $mail->Username   = 'votre_user_alwaysdata'; 
        $mail->Password   = 'votre_mot_de_passe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Destinataires
        $mail->setFrom('noreply@reservervotreplat.alwaysdata.net', 'RéserveTonPlat UA');
        $mail->addAddress($email, $nom);

        // Contenu du mail
        $lien = "https://reservervotreplat.alwaysdata.net/public/confirm.php?token=" . $token;
        
        $mail->isHTML(true);
        $mail->Subject = 'Confirme ton inscription - RéserveTonPlat';
        $mail->Body    = "<h1>Bienvenue $nom !</h1>
                          <p>Pour valider ton compte et réserver ton repas, clique sur le lien ci-dessous :</p>
                          <a href='$lien' style='padding:10px 20px; background:#005596; color:white; text-decoration:none; border-radius:5px;'>Valider mon compte</a>";

        $mail->send();
        header("Location: ../public/login.php?msg=check_email");

    } catch (Exception $e) {
        echo "Erreur lors de l'envoi : {$mail->ErrorInfo}";
    }
}