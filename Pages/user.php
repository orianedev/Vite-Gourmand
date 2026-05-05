<?php
session_start();
include("../includes/database.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


$sqlUser = $pdo->prepare("SELECT * FROM utilisateur WHERE utilisateur_id = ?");
$sqlUser->execute([$user_id]);
$user = $sqlUser->fetch();


$sqlCmd = $pdo->prepare("
    SELECT c.*, m.titre 
    FROM commande c
    JOIN menu m ON c.menu_id = m.menu_id
    WHERE c.utilisateur_id = ?
    ORDER BY c.date_commande DESC
");
$sqlCmd->execute([$user_id]);
$commandes = $sqlCmd->fetchAll();

if (isset($_POST["envoyer_avis"])) {

    $note = $_POST["note"];
    $description = $_POST["description"];
    $user_id = $_SESSION["user_id"];

    $sqlAvis = $pdo->prepare("
        INSERT INTO avis (note, description, statut, utilisateur_id)
        VALUES (?, ?, 'publie', ?)
    ");

    $sqlAvis->execute([
        $note,
        $description,
        $user_id
    ]);

    $message = "Avis envoyé !";
}
?>

<!DOCTYPE html> 

<html> 

  <head>
    <meta charset="UTF-8">
    <title> Vue des menus </title>
    <link rel="stylesheet" href="../assets/style.css">
  </head>

<body>
    
    <?php include("../includes/header.php"); ?>

    <div class="pub"> 
                <div class="pub-content">
                <h1> Tableau de bord </h1>
                </div>
    </div>

    <main class="dashboard"> 
      <section class="content">
      
        <h2> Mes commandes </h2>
      <?php foreach ($commandes as $cmd) { ?>
      
      <article class="order-card">
        
      <h3><?= $cmd["titre"] ?></h3>
      <p>
        <?= date("d/m/Y", strtotime($cmd["date_prestation"])) ?>
        à <?= substr($cmd["heure_livraison"], 0, 5) ?>
      </p>

    <p><?= $cmd["nombre_personne"] ?> personnes</p>

          <div class="timeline">
           <span class="<?= $cmd["statut"] == "confirmee" ? "done" : "" ?>">Commandée</span>
           <span class="<?= $cmd["statut"] == "confirmee" ? "done" : "" ?>">Acceptée</span>
           <span class="<?= $cmd["statut"] == "en preparation" ? "current" : "" ?>">En préparation</span>
           <span class="<?= $cmd["statut"] == "livree" ? "done" : "" ?>">En livraison</span>
           <span class="<?= $cmd["statut"] == "terminee" ? "done" : "" ?>">Terminée</span>
          </div>
       

        <div class="review">
            
          <h4>Laisser un avis</h4>
          
          <form method="POST">
            <label>Note :</label>
          <select name="note">
            <option value="1">1 ⭐</option>
            <option value="2">2 ⭐</option>
            <option value="3">3 ⭐</option>
            <option value="4">4 ⭐</option>
            <option value="5">5 ⭐</option>
          </select>

        <textarea name="description" placeholder="Laisser un commentaire"></textarea>

        <button type="submit" name="envoyer_avis">Envoyer mon avis</button>
         </form>
        
        </div>

      </article>
<?php } ?>

      
      </section>
    

    <aside class="sidebar">
      <article class="infos-card">
        <h2> Mes informations personnelles </h2>

          <div class="info-row">
            <span class="label">Nom :</span>
           <span class="value" id="nomText"><?= $user["nom"] ?></span>
           <input type="text" id="nomInput" value="<?= $user["nom"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label">Prénom :</span>
           <span class="value" id="prenomText"><?= $user["prenom"] ?></span>
           <input type="text" id="prenomInput" value="<?= $user["prenom"] ?>" class="hidden">
          </div>

          <div class="info-row">
           <span class="label">Email :</span>
           <span class="value" id="emailText"><?= $user["email"] ?></span>
           <input type="email" id="emailInput" value="<?= $user["email"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label">Téléphone :</span>
            <span class="value" id="telText"> <?= $user["telephone"] ?></span>
            <input type="text" id="telInput" value="<?= $user["telephone"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label"> Adresse :</span>
            <span class="value" id="Adresse"> <?= $user["adresse_postale"] ?></span>
            <input type="text" id="adresse" value="<?= $user["adresse_postale"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label">Ville :</span>
            <span class="value" id="ville"> <?= $user["ville"] ?></span>
            <input type="text" id="ville" value="<?= $user["ville"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label">Code postal :</span>
            <span class="value" id="cp"> <?= $user["code_postal"] ?></span>
            <input type="text" id="code_postal" value="<?= $user["code_postal"] ?>" class="hidden">
          </div>

          <div class="actions">
            <button class="btn-primary" id="editBtn"> Modifier </button>
            <button class="btn-primary hidden" id="saveBtn">Enregistrer</button>
            <button class="btn-secondary hidden" id="cancelBtn">Annuler</button>
          </div>

        
      </article>
  

      <ul> 
      <li class="active"> <button class="btn-primary"> Mes commandes </button> </li>
      <li><button class="btn-primary"> Se déconnecter </button> </li>
      </ul>
    </aside>


      </div>
    </main>



    <?php include("../includes/footer.php"); ?>

    

</body>
</html>