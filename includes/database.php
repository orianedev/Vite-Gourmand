<?php

$host = "sql107.infinityfree.com";
$dbname = "if0_41977457_vitegourmand_colandavaloo";
$user = "if0_41977457";
$pass = "Anbdy974";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}

?>