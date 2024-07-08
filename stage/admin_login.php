<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_email = htmlspecialchars(trim($_POST['email']));
    $admin_password = htmlspecialchars(trim($_POST['password']));


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $sql = "SELECT id, password FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($admin_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($admin_password, $hashed_password)) {

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin_id;
            header('Location: admin_dashboard.php');
            exit;
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Email non trouvé.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Connexion Administrateur</title>
</head>
<body style="background-image: url('background.png'); background-size: cover; background-position: center;">
<div class="container" >
    <form id="registerForm" action="admin_login.php" method="POST">
        <div class="logo-container">
            <img src="LogoSII.png" alt="Logo de votre entreprise">
        </div>
        <label for="Email" class="required">Email:</label>
        <input type="email" name="email"  required>
        <label for="Mot de passe" class="required">Mot de passe:</label>
        <input type="password" name="password"  required></br>
        <a href="oublier_passe.php">Mote de passe oublier ?</a></br></br></br>
        <button type="submit">Se connecter</button>
        
    </form>
    <footer>
        <img src="LogoSII.png" alt="Logo de votre entreprise">
    </footer>
</div>
</body>
</html>
