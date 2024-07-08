<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "stage";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {

        die("erreur de connexion : " . $conn->connect_error);
    }

?>