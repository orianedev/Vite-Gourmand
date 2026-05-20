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


$name = $_GET["name"] ?? "";
$statut = $_GET["statut"] ?? "";
$section = $_GET["section"] ?? "commandes";

$sql = "
SELECT 
commande.*, 
utilisateur.nom, 
utilisateur.prenom, 
utilisateur.email, 
utilisateur.telephone,
menu.titre

FROM commande

INNER JOIN utilisateur
ON commande.utilisateur_id = utilisateur.utilisateur_id

INNER JOIN menu
ON commande.menu_id = menu.menu_id

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



$avisRequest = $pdo->prepare("
    SELECT avis.*, utilisateur.nom
    FROM avis
    INNER JOIN utilisateur
    ON avis.utilisateur_id = utilisateur.utilisateur_id
    WHERE avis.etat = ?
");

$avisRequest->execute(['en_attente']);

$avis = $avisRequest->fetchAll();


if (isset($_POST["validate_review"])) {

    $avis_id = intval($_POST["avis_id"]);

    $update = $pdo->prepare("
        UPDATE avis
        SET etat = 'valide'
        WHERE avis_id = ?
    ");

    $update->execute([$avis_id]);
}

if (isset($_POST["delete_review"])) {

    $avis_id = intval($_POST["avis_id"]);

    $delete = $pdo->prepare("
        DELETE FROM avis
        WHERE avis_id = ?
    ");

    $delete->execute([$avis_id]);
}

$request = $pdo->prepare($sql);
$request->execute($params);

$commandes = $request->fetchAll();
?>

<!DOCTYPE html> 

<html> 

  <head>
    <meta charset="UTF-8">
    <title> Espace employé </title>
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

<?php if($section === "commandes") : ?>
    <aside class="employee_filters">
        <h3> Filtrer par : </h3>
        <form action="" method="GET">
            <input type="hidden" name="section" value="commandes">
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
                <option> En prépration </option>
            </select>
          </div>
          <button type="submit" class="btn-primary"> Filtrer </button>
        </form>
    </aside>
<?php endif; ?>


      <section class="content">
      <?php if($section === "commandes") : ?>
      <?php foreach($commandes as $commande) : ?>
        <h2> Commandes </h2>
<article class="order-card">

    <h3><?= htmlspecialchars($commande["numero_commande"]) ?></h3>

    <p>
        <?= htmlspecialchars($commande["prenom"]) ?>
        <?= htmlspecialchars($commande["nom"]) ?>
    </p>

    <p><?= htmlspecialchars($commande["date_commande"]) ?></p>

    <p><?= htmlspecialchars($commande["nombre_personne"]) ?> personnes</p>

    <p class="menu_name"> Menu : <?= htmlspecialchars($commande["titre"]) ?> </p>
    
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

    <option value="Acceptée">Acceptée</option>

    <option value="En préparation">En préparation</option>

    <option value="En cours de livraison">
        En cours de livraison
    </option>

    <option value="Livrée">Livrée</option>

    <option value="En attente de restitution du matériel">
        En attente de restitution du matériel
    </option>

    <option value="Terminée">Terminée</option>

</select>

            <button class="btn-primary"
                    type="submit"
                    name="update_status"> Modifier le statut
            </button>

        </form>

    </div>

</article>

<?php endforeach; ?>
<?php endif; ?>

<?php if($section === "menus") : ?>
<?php foreach($menus as $menu) : ?>
<h2> Menu </h2>
<article class="menu">


    <img src="<?= htmlspecialchars($menu["image"]) ?>"
         alt="<?= htmlspecialchars($menu["titre"]) ?>">

    <div class="card-content">

        <h2><?= htmlspecialchars($menu["titre"]) ?></h2>

        <p>
            à partir de :
            <?= htmlspecialchars($menu["prix_par_personne"]) ?> €
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
<?php endif; ?>

<?php if($section === "avis") : ?>
<?php foreach($avis as $review) : ?>
    <h2> Avis</h2>

<div class="review_employee "> 
    
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
                    name="validate_review"> Valider l'avis
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
<?php endif; ?>

          </div>
    
      </section>
    

<aside class="sidebar">

    <ul>

        <li>
            <a href="?section=commandes" class="btn-primary">
                Commandes
            </a>
        </li>

        <li>
            <a href="?section=menus" class="btn-primary">
                Menus
            </a>
        </li>

        <li>
            <a href="?section=avis" class="btn-primary">
                Avis
            </a>
        </li>

    </ul>

</aside>

      </div>
    </main>



    <?php include("../includes/footer.php"); ?>

    

</body>
</html>