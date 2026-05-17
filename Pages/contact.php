<?php

require_once("../includes/database.php");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = htmlspecialchars(trim($_POST["name"]));
    $prenom = htmlspecialchars(trim($_POST["firstname"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $sujet = htmlspecialchars(trim($_POST["title"]));
    $message = htmlspecialchars(trim($_POST["comment"]));

   
    if (
        empty($nom) ||
        empty($prenom) ||
        empty($email) ||
        empty($sujet) ||
        empty($message)
    ) {

        $error = "Tous les champs sont obligatoires.";

    } else {



        $insert = $pdo->prepare("
            INSERT INTO contact
            (
                nom,
                prenom,
                email,
                sujet,
                message
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $insert->execute([
            $nom,
            $prenom,
            $email,
            $sujet,
            $message
        ]);


        $to = "contact@vitegourmand.fr";

        $mail_subject = "Nouveau message de contact";

        $mail_message = "
        Nom : $nom

        Prénom : $prenom

        Email : $email

        Sujet : $sujet

        Message :
        $message
        ";

        $headers = "From: $email";

        mail($to, $mail_subject, $mail_message, $headers);

        $success = "Votre message a bien été envoyé.";
    }
}

?>

<!DOCTYPE html> 

<html> 

  <head>
    <meta charset="UTF-8">
    <title> Contact </title>
    <link rel="stylesheet" href="../assets/style.css">
  </head>

<body>
    
    <?php include("../includes/header.php"); ?>

    <div class="pub"> 
                <div class="pub-content">
                <h1> Nous contacter </h1>
                </div>
    </div>

    <main class="contact_form">
        
        <h2> Formulaire de contact </h2>

        <div class="contact_content">

        <?php if(!empty($error)) : ?>

    <p><?= $error ?></p>

<?php endif; ?>

<?php if(!empty($success)) : ?>

    <p><?= $success ?></p>

<?php endif; ?>

            <form action="" method="post">
                <div class="field">
                  <label for="name"> Nom : </label>
                  <input name="name" id="name" type="text" maxlength="25" required>
                </div>

                <div class="field">
                    <label for="firstname"> Prénom : </label>
                    <input name="firstname" id="firstname" type="text" maxlength="25" required>
                </div>

                <div class="field">
                    <label for="email"> Adresse email : </label>
                    <input name="email" id="email" type="email" maxlength="30" required>
                </div>

                <div class="field"> 
                    <label for="title"> Objet : </label>
                    <input name="title" id="title" type="text" maxlength="30" required>
                </div>

                <div class="field">
                    <label for="comment"> Commentaire : </label>
                    <textarea name="comment" id="comment"> </textarea>
                </div>

                <button class="btn-primary" type="submit"> Envoyer </button>
            </form>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>

    

</body>
</html>
