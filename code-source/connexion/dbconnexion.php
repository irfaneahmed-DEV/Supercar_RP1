<?php

// $host = "mysql-irfane.alwaysdata.net";

// $utilisateur = "irfane";

// $mp = "q@D#Z!qM.Da3_M6";

// $baseDeDonnee = "irfane_supercar";

$host = "localhost";

$utilisateur = "root";

$mp = "";

$baseDeDonnee = "supercar";

$conn = mysqli_connect($host, $utilisateur, $mp, $baseDeDonnee);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}


?>