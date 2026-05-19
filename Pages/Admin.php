<?php

session_start();

require_once("../includes/database.php");

if (!isset($_SESSION["user"])) {

    header("Location: login.php");
    exit();
}

if ($_SESSION["user"]["role_id"] != 3) {

    header("Location: user.php");
    exit();
}

$message = "";

if (isset($_POST["create_employee"])) {

    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    $check = $pdo->prepare("
        SELECT *
        FROM utilisateur
        WHERE email = ?
    ");

    $check->execute([$email]);

    if ($check->rowCount() > 0) {

        $message = "Cet email existe déjà.";

    } else {

        $insert = $pdo->prepare("
            INSERT INTO utilisateur
            (
                email,
                password,
                nom,
                prenom,
                telephone,
                ville,
                adresse_postale,
                code_postal,
                role_id,
                active,
                created_at
            )
            VALUES
            (
                ?,
                ?,
                '',
                '',
                '',
                '',
                '',
                NULL,
                2,
                1,
                NOW()
            )
        ");

        $insert->execute([
            $email,
            $password
        ]);

        $message = "Compte employé créé avec succès.";
    }
}

if (isset($_POST["disable_employee"])) {

    $employee_id = intval($_POST["employee_id"]);

    $update = $pdo->prepare("
        UPDATE utilisateur
        SET active = 0
        WHERE utilisateur_id = ?
        AND role_id = 2
    ");

    $update->execute([$employee_id]);
}

$name = $_GET["Name"] ?? "";
$statut = $_GET["statut"] ?? "";

$sql = "
SELECT commande.*, utilisateur.email
FROM commande
INNER JOIN utilisateur
ON commande.utilisateur_id = utilisateur.utilisateur_id
WHERE 1
";

$params = [];

if (!empty($name)) {

    $sql .= " AND utilisateur.email LIKE ?";
    $params[] = "%$name%";
}

if (!empty($statut)) {

    $sql .= " AND commande.statut = ?";
    $params[] = $statut;
}

$request = $pdo->prepare($sql);
$request->execute($params);

$orders = $request->fetchAll();

$employees = $pdo->query("
    SELECT *
    FROM utilisateur
    WHERE role_id = 2
")->fetchAll();

$reviews = $pdo->query("
    SELECT *
    FROM avis
")->fetchAll();

$menus = $pdo->query("
    SELECT *
    FROM menu
")->fetchAll();

$json = file_get_contents("../BDD/mongodb_data.json");

$data = json_decode($json, true);

$statsMenus = [];
$chiffreAffaire = [];


$section = $_GET["section"] ?? "commandes";

$sqlMenus = "SELECT * FROM menu";
$stmtMenus = $pdo->query($sqlMenus);
$menus = $stmtMenus->fetchAll();

?>



<!DOCTYPE html> 

<html> 

  <head>
    <meta charset="UTF-8">
    <title> Espace administrateur </title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>

<body>
    
    <?php include("../includes/header.php"); ?>

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
            <label for="Name"> Nom du client </label>
            <input name="Name" id="Name" type="text">
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

      <?php if($section === "commandes") : ?>
       <div class="orders">
       <?php foreach($orders as $order) : ?>
        <article class="order-card">
          <h3><?= htmlspecialchars($order["menu_id"]) ?></h3>
          <p><?= htmlspecialchars($order["email"]) ?></p>
          <p><?= htmlspecialchars($order["date_commande"]) ?></p>
          <p><?= htmlspecialchars($order["statut"]) ?></p>
          <div class="action">
            <form method="POST">
              <input type="hidden"
                   name="order_id"
                   value="<?= $order["menu_id"] ?>">
                  <select name="statut">
                    <option>Acceptée</option>
                    <option>En préparation</option>
                    <option>En cours de livraison</option>
                    <option>Livrée</option>
                    <option>En attente de restitution du matériel</option>
                    <option>Terminée</option>
                  </select>
                  <button class="btn-primary"
                    type="submit"
                    name="update_statut"> Modifier le statut </button>
            </form>
          </div>
        </article>
        <?php endforeach; ?>
       </div>
       <?php endif; ?>


      <?php if($section === "menus") : ?>
<div class="menus_container">

<?php foreach($menus as $menu) : ?>

  <div class="menu_topbar">

    <a href="Create_menu.php"
       class="btn-primary">

        Créer un menu

    </a>

</div>

<article class="menu">

    <img src="<?= htmlspecialchars($menu["image"]) ?>" 
         alt="<?= htmlspecialchars($menu["titre"]) ?>">

    <div class="card-content">

        <h2><?= htmlspecialchars($menu["titre"]) ?></h2>

        <p>
            nombres de personnes minimum :
            <?= htmlspecialchars($menu["nombre_personne_minimum"]) ?>
        </p>

        <p>
            à partir de :
            <?= htmlspecialchars($menu["prix_par_personne"]) ?>€
        </p>

        <p>
            <?= htmlspecialchars($menu["description"]) ?>
        </p>

        <a href="Detail_menus.php?id=<?= $menu["menu_id"] ?>">
            Modifier le menu
        </a>

    </div>

    <div class="action">

        <form method="POST">

            <input type="hidden"
                   name="menu_id"
                   value="<?= $menu["menu_id"] ?>">

            <button class="btn-primary"
                    type="submit"
                    name="delete_menu">

                Supprimer

            </button>

        </form>

    </div>

</article>

<?php endforeach; ?>

</div>
      <?php endif; ?>


    
      <?php if($section === "avis") : ?>
        <div class="review_employee "> 
          <div class="stars"> 
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <?php foreach($reviews as $review) : ?>
            <p><?= htmlspecialchars($review["utilisateur_id"]) ?></p>
            <p><?= htmlspecialchars($review["description"]) ?></p>
            <div class="action">
              <form method="POST">
                <input type="hidden"
                   name="review_id" value="<?= $review["utilisateur_id"] ?>">
                   <button class="btn-primary"
                    type="submit" name="validate_review"> Valider l'avis </button>
              </form>
              
              <form method="POST">
                <input type="hidden"
                   name="review_id" value="<?= $review["utilisateur_id"] ?>">
                   <button class="btn-secondary"
                    type="submit" name="delete_review"> Supprimer l'avis </button>
              </form>
            </div>
          </div> 
          <?php endforeach; ?>
        </div>
        <?php endif; ?>


        <?php if($section === "employes") : ?>
          <div class="my_employee">
            <div class="new_employee"> 
            <button class="btn-primary"> Créer un nouvel employé </button>
            </div>
            <?php foreach($employees as $employee) : ?>

            <div class="current_employee">
              <h3><?= htmlspecialchars($employee["email"]) ?></h3>
              <p> Statut : <?= $employee["active"] ? "Actif" : "Désactivé" ?> </p>
              <div class="action">
                <form method="POST">
                  <input type="hidden"
                   name="employee_id"
                   value="<?= $employee["utilisateur_id"] ?>">
                   <button class="btn-primary"
                    type="submit"
                    name="disable_employee"> Révoquer l'accès </button>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>


        <?php if($section === "statistiques") : ?>
          <div class="stat">
            <div class="stat_filters">
                <form  action="" method="get">
                  <label for="menu"> Menu : </label>
                  <select name="menu" id="menu">
                    <option> </option>
                    <option> Menu classic gourmet </option>
                    <option> Menu Vegan chic </option>
                    <option> Mennu de Noël </option>
                    <option> Buffet hivernal </option>
                    <option> Menu cocktail dinatoire prestige </option>
                    <option> Brunch gourmand </option>
                  </select>
                  <label for="period"> Période : </label>
                  <select name="period" id="period">
                    <option> </option>
                    <option value="jour"> Par jour </option> 
                    <option vlaue="semaine"> Par semaine </option>
                    <option value="mois"> Par mois </option>
                    <option value="année"> Par année </options>
                  </select>
                </form>
                <button class="btn-primary"> Filtrer </button>
             </div>

            <div class="stat_resume">
              <?php foreach($statsMenus as $menu => $total) : ?>
                <div class="stat_card">

                <h3><?= $menu ?></h3>
                <p> Nombre de commandes :<?= $total ?> </p>
                <p> Chiffre d'affaires : <?= $chiffreAffaire[$menu] ?> €</p>
            </div>
            <?php endforeach; ?>
            

            </div>

            <div class="stat_graph">
              <canvas id="chartMenu"></canvas>
            </div>

            <div class="stat_array">

            </div>
          </div>
          <?php endif; ?>
    
      </section>
    

    <aside class="sidebar">
    <ul>

        <li>
            <a href="Admin.php?section=commandes" class="btn-primary">
                Commandes
            </a>
        </li>

        <li>
            <a href="Admin.php?section=menus" class="btn-primary">
                Menus
            </a>
        </li>

        <li>
            <a href="Admin.php?section=avis" class="btn-primary">
                Avis
            </a>
        </li>

        <li>
            <a href="Admin.php?section=employes" class="btn-primary">
                Mes employés
            </a>
        </li>

        <li>
            <a href="Admin.php?section=statistiques" class="btn-primary">
                Statistiques
            </a>
        </li>

    </ul>
</aside>


      </div>
    </main>



  <?php include("../includes/footer.php"); ?>

<script>

const labels = [

<?php foreach($statsMenus as $menu => $total) : ?>

"<?= $menu ?>",

<?php endforeach; ?>

];

const data = [

<?php foreach($statsMenus as $menu => $total) : ?>

<?= $total ?>,

<?php endforeach; ?>

];

new Chart(document.getElementById('chartMenu'), {

    type: 'bar',

    data: {

        labels: labels,

        datasets: [{

            label: 'Nombre de commandes',

            data: data

        }]
    }
});

</script>

    

</body>
</html>