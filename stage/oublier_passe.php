<?php

include 'config.php';

require '/Applications/XAMPP/xamppfiles/htdocs/stage/PHPMailer/src/PHPMailer.php';
require '/Applications/XAMPP/xamppfiles/htdocs/stage/PHPMailer/src/SMTP.php';
require '/Applications/XAMPP/xamppfiles/htdocs/stage/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt_check = $conn->prepare("SELECT id, first_name FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->bind_result($user_id, $first_name);
    $stmt_check->fetch();
    $stmt_check->close();

    if (!$user_id) {
        echo "Aucun utilisateur trouvé avec cet email.";
    } else {

        $token = bin2hex(random_bytes(32));

        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $token, $user_id);
        $stmt->execute();
        $stmt->close();

        try {
            $mail = new PHPMailer(true);

            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl'; 
            $mail->Port = 465; 
            $mail->Username = 'ikramayachi.532@gmail.com';
            $mail->Password = 'Ikram.2001';
            $mail->setFrom('ikramayachi.532@gmail.com', 'ikram');
            $mail->addAddress($email, $first_name); 

            $to_email = $email;

            $mail->addAddress($to_email, $first_name);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de mot de passe';
            $mail->Body    = 'Bonjour ' . $first_name . ',<br><br>'
                            . 'Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :<br>'
                            . '<a href="http://localhost/stage/reset_password_form.php?email=' . urlencode($email) . '&token=' . urlencode($token) . '">Réinitialiser mon mot de passe</a><br><br>'
                            . 'Si vous n\'avez pas demandé cette réinitialisation, ignorez simplement cet email.<br><br>'
                            . 'Cordialement,<br>Votre Nom';

            $mail->SMTPDebug = SMTP::DEBUG_SERVER;

            $mail->send();
            echo 'Un email a été envoyé à votre adresse pour réinitialiser votre mot de passe.';
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Mot de passe oublié</title>
</head>
<body style="background-image: url('background.png'); background-size: cover; background-position: center;">
<div class="container">
    <form id="registerForm" action="oublier_passe.php" method="POST">
    <div class="logo-container">
            <img src="LogoSII.png" alt="Logo de votre entreprise">
        </div>
        <label for="email"  class="required">Entrez votre email :</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Réinitialiser le mot de passe</button>
    </form>
</div>
<footer>
        <img src="LogoSII.png" alt="Logo de votre entreprise">
</footer>
</body>
</html>
