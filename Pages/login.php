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
                        <input type="submit" value="Se connecter">
                    </div>

                    <div class="loginbtn">
                    <a href="Create_account.php"> Créer un compte </a>
                    </div>

                     <a href="passwordforget.html"> Mot de passe oublié ? </a>

                </form>
            </div>
            
        </main>

        <?php include("../includes/footer.php"); ?>


</body>


</html>