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
            <form action="" method="post">
                <div class="field">
                  <label for="name"> Nom : </label>
                  <input name="name" id="name" type="text" maxlength="25">
                </div>

                <div class="field">
                    <label for="firstname"> Prénom : </label>
                    <input name="firstname" id="firstname" type="text" maxlength="25">
                </div>

                <div class="field">
                    <label for="email"> Adresse email : </label>
                    <input name="email" id="email" type="email" maxlength="30">
                </div>

                <div class="field"> 
                    <label for="title"> Objet : </label>
                    <input name="title" id="title" type="text" maxlength="30">
                </div>

                <div class="field">
                    <label for="comment"> Commentaire : </label>
                    <textarea name="comment" id="comment"> </textarea>
                </div>

                <button class="btn-primary"> Envoyer </button>
            </form>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>

    

</body>
</html>
