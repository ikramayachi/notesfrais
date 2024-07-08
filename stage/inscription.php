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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $status = 'pending';

    $sql = "INSERT INTO users (first_name, last_name, email, password, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $status);

    if ($stmt->execute()) {
        echo "Inscription réussie, en attente d'approbation.";

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

           
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation d\'inscription';
            $mail->Body    = 'Votre inscription est en attente d\'approbation.';

            $mail->SMTPDebug = SMTP::DEBUG_SERVER;

            $mail->send();
            echo 'Email de confirmation envoyé.';
        } catch (Exception $e) {
            echo 'Erreur lors de l\'envoi de l\'email : ', $e->getMessage();
        }
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Inscription</title>
</head>
<body style="background-image: url('background.png'); background-size: cover; background-position: center;">
<div class="container">
    <form id="registerForm" action="inscription.php" method="POST">
        <div class="logo-container">
            <img src="LogoSII.png" alt="Logo de votre entreprise">
        </div>
        <label for="Prénom" class="required">Prénom:</label>
        <input type="text" name="first_name" required autocomplete="given-name">
        <label for="Nom" class="required">Nom:</label>
        <input type="text" name="last_name"  required autocomplete="family-name">
        <label for="Email" class="required">Email:</label>
        <input type="email" name="email"  required autocomplete="email">
        <label for="Mot de passe" class="required">Mot de passe:</label>
        <input type="password" name="password" required autocomplete="new-password"></br>
        <a href="oublier_passe.php">Mot de passe oublié ?</a></br></br></br>
        <button type="submit">S'inscrire</button>
    </form>
    <footer>
        <img src="logo.png" alt="Logo de votre entreprise">
    </footer>
</div>
</body>
</html>