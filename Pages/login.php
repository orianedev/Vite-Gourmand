<?php
session_start();
include("../includes/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $sql = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ? AND password = ?");
    $sql->execute([$email, $password]);

    $user = $sql->fetch();

    if ($user) {

        $_SESSION["user_id"] = $user["utilisateur_id"];
        $_SESSION["prenom"] = $user["prenom"];
        $_SESSION["role_id"] = $user["role_id"];

        if ($user["role_id"] == 1) {
            header("Location: user.php");
            exit();
        }

        if ($user["role_id"] == 2) {
            header("Location: employee.php");
            exit();
        }

        if ($user["role_id"] == 3) {
            header("Location: Admin.php");
            exit();
        }

    } else {
        $message = "Email ou mot de passe incorrect.";
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
                        <label for="Email"> Email : </label>
                        <input id="Email" name="Email" type="email">
                    </div>

                    <div class="field">
                        <label for="password"> Mot de Passe : </label>
                        <input id="password" name="password" type="password">
                    </div>

                    <div class="loginbtn">
                        <input type="submit" value="Se connecter">
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