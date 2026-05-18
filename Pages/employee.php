<?php

session_start();

require_once("../includes/database.php");


if (!isset($_SESSION["user"])) {

    header("Location: login.php");
    exit;
}

if ($_SESSION["user"]["role_id"] != 2) {

    header("Location: user.php");
    exit;
}


$name = $_GET["Name"] ?? "";
$statut = $_GET["statut"] ?? "";

$sql = "
SELECT commande.*, utilisateur.nom, utilisateur.prenom, utilisateur.email, utilisateur.telephone
FROM commande
INNER JOIN utilisateur
ON commande.utilisateur_id = utilisateur.utilisateur_id
WHERE 1
";

$params = [];

if (!empty($name)) {

    $sql .= " AND utilisateur.nom LIKE ?";
    $params[] = "%$name%";
}

if (!empty($statut)) {

    $sql .= " AND commande.statut = ?";
    $params[] = $statut;
}

$sql .= " ORDER BY commande.date_commande DESC";

$request = $pdo->prepare($sql);
$request->execute($params);

$commandes = $request->fetchAll();


if (isset($_POST["update_status"])) {

    $commande_id = intval($_POST["commande_id"]);

    $new_status = htmlspecialchars($_POST["new_status"]);

    $update = $pdo->prepare("
        UPDATE commande
        SET statut = ?
        WHERE numero_commande = ?
    ");

    $update->execute([$new_status, $commande_id]);

    
    if ($new_status === "En attente de restitution du matériel") {

        $mailRequest = $pdo->prepare("
            SELECT utilisateur.email
            FROM commande
            INNER JOIN utilisateur
            ON commande.utilisateur_id = utilisateur.utilisateur_id
            WHERE commande.numero_commande = ?
        ");

        $mailRequest->execute([$commande_id]);

        $client = $mailRequest->fetch();

        if ($client) {

            $subject = "Retour du matériel";

            $message = "
            Bonjour,

            Du matériel doit être restitué.

            Sans restitution sous 10 jours ouvrés,
            des frais de 600€ seront appliqués.

            Merci de contacter Vite&Gourmand.

            Cordialement
            ";

            mail($client["email"], $subject, $message);
        }
    }
}



if (isset($_POST["cancel_order"])) {

    $commande_id = intval($_POST["commande_id"]);

    $mode_contact = htmlspecialchars($_POST["mode_contact"]);

    $motif = htmlspecialchars($_POST["motif_annulation"]);

    $update = $pdo->prepare("
        UPDATE commande
        SET
        statut = 'Annulée',
        mode_contact = ?,
        motif_annulation = ?
        WHERE numero_commande = ?
    ");

    $update->execute([
        $mode_contact,
        $motif,
        $commande_id
    ]);
}



if (isset($_POST["delete_menu"])) {

    $menu_id = intval($_POST["menu_id"]);

    $delete = $pdo->prepare("
        DELETE FROM menu
        WHERE menu_id = ?
    ");

    $delete->execute([$menu_id]);
}


$menus = $pdo->query("
    SELECT *
    FROM menu
")->fetchAll();



$avis = $pdo->query("
    SELECT avis.*, utilisateur.nom
    FROM avis
    INNER JOIN utilisateur
    ON avis.utilisateur_id = utilisateur.utilisateur_id
")->fetchAll();


if (isset($_POST["validate_review"])) {

    $avis_id = intval($_POST["avis_id"]);

    $update = $pdo->prepare("
        UPDATE avis
        SET statut = 'valide'
        WHERE avis_id = ?
    ");

    $update->execute([$avis_id]);
}


if (isset($_POST["delete_review"])) {

    $avis_id = intval($_POST["avis_id"]);

    $update = $pdo->prepare("
        UPDATE avis
        SET statut = 'refuse'
        WHERE avis_id = ?
    ");

    $update->execute([$avis_id]);
}

?>

<!DOCTYPE html> 

<html> 

  <head>
    <meta charset="UTF-8">
    <title> Espace employé </title>
    <link rel="stylesheet" href="../assets/style.css">
  </head>

<body>
    
    <header class="navbar">
        <img src="../Sources/Logo.jpeg" width="50"height="50" alt="Logo">

         <navbar> <a href="../Index.html"> Accueil </a>
         <a href="Menus.html"> Menus </a>
         <a href="Contact.php"> Contacts </a>
         <div class="login"> <a href="login.html"> Connexion </a> </div>
        </navbar>

    </header>

    <div class="pub"> 
                <div class="pub-content">
                <h1> Tableau de bord </h1>
                </div>
    </div>

<main class="dashboard"> 

    <aside class="employee_filters">
        <h3> Filtrer par : </h3>
        <form action="" method="GET">
          <div class="filter">
            <label for="name"> Nom du client </label>
            <input name="name" id="name" type="text">
          </div>
          <div class="filter">
            <label for="statut"> Statut de la commande </label>
            <select name="statut" id="statut">
                <option> </option>
                <option> Commandée </option>
                <option> Acceptée </option>
                <option> En cours de livraison </option>
                <option> Livrée </option>
                <option> Terminée </option>
                <option> En attente de restitution du matériel </option>
            </select>
          </div>
    </aside>


      <section class="content">
        <h2> Commandes </h2>

      <?php foreach($commandes as $commande) : ?>

<article class="order-card">

    <h3><?= htmlspecialchars($commande["numero_commande"]) ?></h3>

    <p>
        <?= htmlspecialchars($commande["prenom"]) ?>
        <?= htmlspecialchars($commande["nom"]) ?>
    </p>

    <p><?= htmlspecialchars($commande["date_commande"]) ?></p>

    <p><?= htmlspecialchars($commande["nombre_personne"]) ?> personnes</p>

    <p><?= htmlspecialchars($commande["telephone"]) ?></p>

    <p><?= htmlspecialchars($commande["email"]) ?></p>

    <div class="timeline">

        <span><?= htmlspecialchars($commande["statut"]) ?></span>

    </div>

    <div class="history">

        <p>
            Commandée le :
            <?= htmlspecialchars($commande["date_commande"]) ?>
        </p>

    </div>

    <div class="action">

        <form method="POST">

            <input type="hidden"
                   name="commande_id"
                   value="<?= $commande["numero_commande"] ?>">

            <select name="mode_contact" required>

                <option value="">Mode contact</option>
                <option value="telephone">Téléphone</option>
                <option value="mail">Mail</option>

            </select>

            <textarea
                name="motif_annulation"
                placeholder="Motif d'annulation"
                required>
            </textarea>

            <button class="btn-secondary"
                    type="submit"
                    name="cancel_order"> Annuler la commande
            </button>

        </form>


        <form method="POST">

            <input type="hidden"
                   name="commande_id"
                   value="<?= $commande["numero_commande"] ?>">

            <select name="new_status">

                <option>Acceptée</option>
                <option>En préparation</option>
                <option>En cours de livraison</option>
                <option>Livrée</option>
                <option>En attente de restitution du matériel</option>
                <option>Terminée</option>

            </select>

            <button class="btn-primary"
                    type="submit"
                    name="update_status"> Modifier le statut
            </button>

        </form>

    </div>

</article>

<?php endforeach; ?>

<?php foreach($menus as $menu) : ?>

<article class="menu">

    <img src="<?= htmlspecialchars($menu["image"]) ?>"
         alt="<?= htmlspecialchars($menu["nom"]) ?>">

    <div class="card-content">

        <h2><?= htmlspecialchars($menu["nom"]) ?></h2>

        <p>
            à partir de :
            <?= htmlspecialchars($menu["prix"]) ?> €
        </p>

        <p><?= htmlspecialchars($menu["description"]) ?></p>

        <a href="Detail_menus.php?id=<?= $menu["menu_id"] ?>"> Modifier le menu </a>

    </div>

    <div class="action">

        <form method="POST">

            <input type="hidden"
                   name="menu_id"
                   value="<?= $menu["menu_id"] ?>">

            <button class="btn-primary"
                    type="submit"
                    name="delete_menu"> Supprimer
            </button>

        </form>

    </div>

</article>

<?php endforeach; ?>
    
<div class="review_employee "> 
          <h4> Valider les avis </h4>
          <div class="stars"> 
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          </div>
         <?php foreach($avis as $review) : ?>
          
          <p><?= htmlspecialchars($review["nom"]) ?></p>
          <p><?= htmlspecialchars($review["description"]) ?></p>
          <p>Note : <?= htmlspecialchars($review["note"]) ?>/5</p>
          
        <div class="action">
          <form method="POST">
              
            <input type="hidden"
                   name="avis_id"
                   value="<?= $review["avis_id"] ?>">
                   <button class="btn-primary"
                    type="submit"
                    name="validate_review"> Valider l'avis /form
                  </button>
          </form>
          
          <form method="POST">

            <input type="hidden"
                   name="avis_id"
                   value="<?= $review["avis_id"] ?>">

            <button class="btn-secondary"
                    type="submit"
                    name="delete_review">Refuser l'avis

            </button>

          </form>

        </div>

</div>

<?php endforeach; ?>
          </div>
    
      </section>
    

    <aside class="sidebar">
      <ul> 
      <li class="active"> <button class="btn-primary"> Commandes </button> </li>
      <li><button class="btn-primary"> Menus </button> </li>
      <li><button class="btn-primary"> Avis </button></li>
      </ul>
    </aside>


      </div>
    </main>



    <footer> 

                <div class="horaires">
                    <h2> Horaires :</h2>
                    <div class="horaires-content"> 
                        <p> Lundi - Vendredi : 9h00 - 18h00</p>
                        <p> Samedi : 9h00 - 13h00 </p>
                        <span> Prestations évenementielles disponibles en dehors de ces horaires sur réservation.</span>
                    </div>
                </div>

                <div class="mentions">
                <a href="mentions_legales.php"> Mentions légales</a>
                <a href="Conditions_de_ventes.php"> Conditions générales de vente </a>
                </div>
                
        
            </footer>

    

</body>
</html>