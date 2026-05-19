<?php

session_start();

require_once("../includes/database.php");

$message = "";

if (isset($_POST["connexion"])) {

    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    $request = $pdo->prepare("
        SELECT *
        FROM utilisateur
        WHERE email = ?
        AND active = 1
    ");

    $request->execute([$email]);

    $utilisateur = $request->fetch();

    if ($utilisateur) {

        if ($password === $utilisateur["password"]) {

            $_SESSION["user"] = $utilisateur;

            if ($utilisateur["role_id"] == 3) {

                header("Location: Admin.php");
                exit();

            } elseif ($utilisateur["role_id"] == 2) {

                header("Location: employee.php");
                exit();

            } else {

                header("Location: user.php");
                exit();
            }

        } else {

            $message = "Mot de passe incorrect.";
        }

    } else {

        $message = "Utilisateur introuvable.";
    }
}

?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title> Espace de connexion </title>
        <link rel="stylesheet" href="../assets/style.css">
    </head>

    
    <body>

    <?php include("../includes/header.php"); ?>


        <div class="pub"> 
                <div class="pub-content">
                <h1> Connexion </h1>
                </div>
            </div>

        <main class="mainlogin">

            <div class="loginform">
                <h2> Se connecter </h2>

                <?php if($message != "") : ?>
                    <p style="color:red; margin-bottom:15px;">
                        <?= $message ?>
                    </p>
                <?php endif; ?>


                <form action="" method="POST">
                    <div class="field">
                        <label for="email"> Email : </label>
                        <input id="email" name="email" type="email">
                    </div>

                    <div class="field">
                        <label for="password"> Mot de Passe : </label>
                        <input id="password" name="password" type="password">
                    </div>

                    <div class="loginbtn">
                        <button type="submit" name="connexion"> Connexion </button>
                    </div>

                    <div class="loginbtn">
                    <a href="Create_account.php"> Créer un compte </a>
                    </div>

                     <a href="passwordforget.php"> Mot de passe oublié ? </a>

                </form>
            </div>
            
        </main>

        <?php include("../includes/footer.php"); ?>


</body>


</html>