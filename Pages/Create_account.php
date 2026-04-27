<?php
include("../includes/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = $_POST["name"];
    $prenom = $_POST["firstname"];
    $telephone = $_POST["num"];
    $email = $_POST["email"];
    $adresse = $_POST["address"];
    $ville = $_POST["city"];
    $codepostal = $_POST["postal-code"];
    $password = $_POST["password"];

    $adresse_complete = $adresse . " - " . $codepostal;

    $check = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {

        $message = "Cet email existe déjà.";

    } else {

        $sql = $pdo->prepare("INSERT INTO utilisateur (email, password, prenom, telephone, ville, pays, adresse_postale, role_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $sql->execute([
            $email,
            $password,
            $prenom,
            $telephone,
            $ville,
            "France",
            $adresse_complete,
            1
        ]);

        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title> Page d'accueil </title>
        <link rel="stylesheet" href="../assets/style.css">
    </head>

    

    <body>

        <?php include("../includes/header.php"); ?>

        <main>

            <form acton="" method="post">
                <fieldset>
                <legend> Créer un compte </legend>

                <?php if($message != "") : ?>
                    <p style="color:red; margin-bottom:15px;">
                    <?= $message ?> </p>
                <?php endif; ?>

                <div>
                    <label for="name"> Nom : </label>
                    <input id="name" name="name" type="text" size="25" required>
                </div>

                <div>
                    <label for="firstname"> Prénom :</label>
                    <input id="firstname" name="firstname" type="text" size="15" required>
                </div>

                <div>
                    <label for="num"> Numéro de téléphone :</label>
                    <input id="num" name="num" type="tel" maxlength="10" pattern="[0-9]+" required>
                </div>

                <div> 
                    <label for="email"> Email : </label>
                    <input id="email" name="email" type="email" maxlength="20" required>
                </div>

                <div>
                    <label for="adress"> Adresse postale : </label>
                    <input id="adress" name="adress" type="text" placeholder="Numéro et rue" required>
                </div>

                <div>
                    <label for="city"> Ville : </label>
                    <input id="city" name="city" type="text" placeholder="Ville" autocomplete="address-level2" required>
                </div>

                <div>
                    <label for="postal-code"> Code postal :</label>
                    <input id="postal-code" name="postal-code" type="text" placeholder="Ex: 75000" autocomplete="postal-code" required>
                </div>

                <div>
                    <label for="password"> Mot de passe : </label>
                    <input id="password" name="password" type="password" minlength="10" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{10,}$" required>
                </div>

                <input type="submit" value="Créer un compte">



                </fieldset>
            </form>
        </main>
        <?php include("../includes/footer.php"); ?>