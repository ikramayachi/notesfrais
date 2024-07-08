<?php
session_start();
include 'config.php';


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); 
    exit;
}




if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, first_name, last_name, email, status FROM users WHERE status = 'pending'";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord de l'administrateur</title>
</head>
<body>
    <h1>Demandes d'inscription en attente</h1>
    <table border="1">
        <tr>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";

                echo "<td>" . $row['first_name'] . "</td>";
                echo "<td>" . $row['last_name'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>
                        <form action='admin_action.php' method='post' style='display:inline;'>
                            <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                            <button type='submit' name='action' value='approve'>Approuver</button>
                        </form>
                        <form action='admin_action.php' method='post' style='display:inline;'>
                            <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                            <button type='submit' name='action' value='reject'>Rejeter</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucune demande en attente</td></tr>";
        }
        ?>
    </table>
</body>
</html>
<?php
$conn->close();
?>
