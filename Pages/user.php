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

    if (isset($_POST["update_user"])) {

    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $telephone = $_POST["telephone"];
    $adresse = $_POST["adresse_postale"];
    $ville = $_POST["ville"];
    $code_postal = $_POST["code_postal"];
    $user_id = $_SESSION["user_id"];

    $sql = $pdo->prepare("
        UPDATE utilisateur
        SET nom = ?, prenom = ?, email = ?, telephone = ?, ville = ?, adresse_postale = ?, code_postal = ?
        WHERE utilisateur_id = ?
    ");

    $sql->execute([
        $nom,
        $prenom,
        $email,
        $telephone,
        $ville,
        $adresse,
        $code_postal,
        $user_id
    ]);

    $message = "Informations mises à jour !";
}
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
            <option value="1">1 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg></option>
            <option value="2">2 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg></option>
            <option value="3">3 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg></option>
            <option value="4">4 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg></option>
            <option value="5">5 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg></option>
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

        <form method="POST">

          <div class="info-row">
            <span class="label">Nom :</span>
           <span class="value" name="nom" id="nomText"><?= $user["nom"] ?></span>
           <input type="text" id="nomInput" value="<?= $user["nom"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label">Prénom :</span>
           <span class="value" name="prenom" id="prenomText"><?= $user["prenom"] ?></span>
           <input type="text" id="prenomInput" value="<?= $user["prenom"] ?>" class="hidden">
          </div>

          <div class="info-row">
           <span class="label">Email :</span>
           <span class="value" name="email" id="emailText"><?= $user["email"] ?></span>
           <input type="email" id="emailInput" value="<?= $user["email"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label">Téléphone :</span>
            <span class="value" name="telephone" id="telText"> <?= $user["telephone"] ?></span>
            <input type="text" id="telInput" value="<?= $user["telephone"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label"> Adresse :</span>
            <span class="value" name="adresse" id="adresse"> <?= $user["adresse_postale"] ?></span>
            <input type="text" id="adresse" value="<?= $user["adresse_postale"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label">Ville :</span>
            <span class="value" name="ville" id="ville"> <?= $user["ville"] ?></span>
            <input type="text" id="ville" value="<?= $user["ville"] ?>" class="hidden">
          </div>

          <div class="info-row">
            <span class="label">Code postal :</span>
            <span class="value" name="code_postal" id="cp"> <?= $user["code_postal"] ?></span>
            <input type="text" id="code_postal" value="<?= $user["code_postal"] ?>" class="hidden">
          </div>

          <div class="actions">
            <button class="btn-primary" id="editBtn"> Modifier </button>
            <button class="btn-primary hidden" id="saveBtn">Enregistrer</button>
            <button class="btn-secondary hidden" id="cancelBtn">Annuler</button>
          </div>

        </form>

<
        
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