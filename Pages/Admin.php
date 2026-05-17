<?php

session_start();

require_once("../includes/database.php");


if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["user"]["role"] !== "admin") {
    header("Location: user.php");
    exit;
}


$message = "";

if (isset($_POST["create_employee"])) {

    $email = htmlspecialchars(trim($_POST["email"]));
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {

        $message = "Cet email existe déjà.";

    } else {

        $insert = $pdo->prepare("
            INSERT INTO users(email, password, role, active)
            VALUES (?, ?, 'employe', 1)
        ");

        $insert->execute([$email, $password]);

        
        $subject = "Création de votre compte";

        $mailMessage = "
        Bonjour,

        Un compte employé a été créé pour vous sur Vite&Gourmand.

        Votre identifiant : $email

        Pour obtenir votre mot de passe,
        merci de contacter votre administrateur.

        Cordialement,
        Vite&Gourmand
        ";

        $headers = "From: contact@vitegourmand.fr";

        mail($email, $subject, $mailMessage, $headers);

        $message = "Compte employé créé avec succès.";
    }
}


if (isset($_POST["disable_employee"])) {

    $employee_id = intval($_POST["employee_id"]);

    $update = $pdo->prepare("
        UPDATE users
        SET active = 0
        WHERE id = ?
        AND role = 'employe'
    ");

    $update->execute([$employee_id]);
}



if (isset($_POST["validate_review"])) {

    $review_id = intval($_POST["review_id"]);

    $update = $pdo->prepare("
        UPDATE reviews
        SET status = 'valide'
        WHERE id = ?
    ");

    $update->execute([$review_id]);
}

if (isset($_POST["delete_review"])) {

    $review_id = intval($_POST["review_id"]);

    $delete = $pdo->prepare("
        DELETE FROM reviews
        WHERE id = ?
    ");

    $delete->execute([$review_id]);
}



if (isset($_POST["update_statut"])) {

    $order_id = intval($_POST["order_id"]);
    $status = htmlspecialchars($_POST["statut"]);

    $update = $pdo->prepare("
        UPDATE orders
        SET statut = ?
        WHERE id = ?
    ");

    $update->execute([$statut, $order_id]);

  

    if ($status === "En attente de restitution du matériel") {

        $getOrder = $pdo->prepare("
            SELECT users.email
            FROM orders
            INNER JOIN users
            ON orders.user_id = users.id
            WHERE orders.id = ?
        ");

        $getOrder->execute([$order_id]);

        $client = $getOrder->fetch();

        if ($client) {

            $subject = "Retour du matériel";

            $mailMessage = "
            Bonjour,

            Du matériel doit être restitué à Vite&Gourmand.

            Sans retour sous 10 jours ouvrés,
            des frais de 600 euros seront appliqués.

            Merci de contacter notre société.

            Cordialement,
            Vite&Gourmand
            ";

            mail($client["email"], $subject, $mailMessage);
        }
    }
}



$name = $_GET["Name"] ?? "";
$statut = $_GET["statut"] ?? "";

$sql = "
SELECT orders.*, users.email
FROM orders
INNER JOIN users
ON orders.user_id = users.id
WHERE 1
";

$params = [];

if (!empty($name)) {

    $sql .= " AND users.email LIKE ?";
    $params[] = "%$name%";
}

if (!empty($statut)) {

    $sql .= " AND orders.statut = ?";
    $params[] = $statut;
}

$sql .= " ORDER BY orders.created_at DESC";

$request = $pdo->prepare($sql);
$request->execute($params);

$orders = $request->fetchAll();


$employees = $pdo->query("
    SELECT *
    FROM users
    WHERE role = 'employe'
")->fetchAll();


$reviews = $pdo->query("
    SELECT *
    FROM reviews
    WHERE statut = 'en_attente'
")->fetchAll();



$menus = $pdo->query("
    SELECT *
    FROM menus
")->fetchAll();

$json = file_get_contents("../BDD/mongodb_data.json");

$data = json_decode($json, true);

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

      <div class="title"> 
        <h2> Commandes </h2> 
        <h2 class="hidden"> Menus </h2>
        <h2 class="hidden"> Avis </h2>
        <h2 class="hidden"> Mes employés </h2>
        <h2 class="hidden"> Statistiques </h2>
      </div>

       <div class="orders">
       <?php foreach($orders as $order) : ?>
        <article class="order-card">
          <h3><?= htmlspecialchars($order["menu_name"]) ?></h3>
          <p><?= htmlspecialchars($order["email"]) ?></p>
          <p><?= htmlspecialchars($order["created_at"]) ?></p>
          <p><?= htmlspecialchars($order["statut"]) ?></p>
          <div class="action">
            <form method="POST">
              <input type="hidden"
                   name="order_id"
                   value="<?= $order["id"] ?>">
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



      <article class="menu"> 
            <img src="../Sources/Images page menu/menu gourmet.jpeg" alt="Menu 2">
            <div class="card-content">
              <h2> Menu classique gourmet </h2>
              <p> nombres de personnes minimum : 10 </p>
              <p> à partir de : 280€ </p>
              <p> Un menu raffiné et élégant pour toutes les occasions. </p>
              <a href="Detail_menus.html"> Modifier le menu </a>
              </div>
            </div>

        <div class="action">
        <button class="btn-primary"> Supprimer </button>
        </div>
      </article>


    

        <div class="review_employee "> 
          <div class="stars"> 
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
          <?php foreach($reviews as $review) : ?>
            <p><?= htmlspecialchars($review["author"]) ?></p>
            <p><?= htmlspecialchars($review["commentaire"]) ?></p>
            <div class="action">
              <form method="POST">
                <input type="hidden"
                   name="review_id" value="<?= $review["id"] ?>">
                   <button class="btn-primary"
                    type="submit" name="validate_review"> Valider l'avis </button>
              </form>
              
              <form method="POST">
                <input type="hidden"
                   name="review_id" value="<?= $review["id"] ?>">
                   <button class="btn-secondary"
                    type="submit" name="delete_review"> Supprimer l'avis </button>
              </form>
            </div>
          </div> 
          <?php endforeach; ?>
        </div>



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
                   value="<?= $employee["id"] ?>">
                   <button class="btn-primary"
                    type="submit"
                    name="disable_employee"> Révoquer l'accès </button>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
          </div>



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
    
      </section>
    

    <aside class="sidebar">
      <ul> 
      <li class="active"> <button class="btn-primary"> Commandes </button> </li>
      <li><button class="btn-primary"> Menus </button> </li>
      <li><button class="btn-primary"> Avis </button></li>
      <li> <button class="btn-primary"> Mes employés </button></li>
      <li> <button class="btn-primary"> Statistiques </button></li>
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