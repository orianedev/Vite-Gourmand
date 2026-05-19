<?php

session_start();

include("../includes/database.php");

if (!isset($_GET['menu_id'])) {
    die("Menu introuvable");
}

$menu_id = $_GET['menu_id'];



/* =========================
   RECUPERATION DU MENU
========================= */

$sql = "SELECT * FROM menu WHERE menu_id = :menu_id";

$requete = $pdo->prepare($sql);

$requete->bindValue(':menu_id', $menu_id, PDO::PARAM_INT);

$requete->execute();

$menu = $requete->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    die("Ce menu n'existe pas");
}



/* =========================
   RECUPERATION DES PLATS
========================= */

$sql_plats = "
SELECT plat.*
FROM plat
INNER JOIN menu_plat
ON plat.plat_id = menu_plat.plat_id
WHERE menu_plat.menu_id = :menu_id
";

$requete_plats = $pdo->prepare($sql_plats);

$requete_plats->bindParam(':menu_id', $menu_id);

$requete_plats->execute();

$plats = $requete_plats->fetchAll(PDO::FETCH_ASSOC);



$utilisateur_connecte = isset($_SESSION['user']);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>Détail menu</title>

    <link href="../assets/style.css" rel="stylesheet">

</head>

<body>

<?php include("../includes/header.php"); ?>

<main>

    <div class="pub">

        <div class="pub-content">

            <h1>
                <?php echo $menu['titre']; ?>
            </h1>

            <p>
                <?php echo $menu['description']; ?>
            </p>

            <img
                src="<?php echo $menu['image']; ?>"
                alt="image menu"
                width="500"
            >

            <h2>
                <?php echo $menu['prix_par_personne']; ?> € / personne
            </h2>

            <p>
                Minimum :
                <?php echo $menu['nombre_personne_minimum']; ?>
                personnes
            </p>

        </div>

    </div>



   <div class="Carte_menu">

    <?php foreach ($plats as $plat) { ?>

        <div class="detail">

            <h2>
                <?php echo ucfirst($plat['categorie']); ?>
            </h2>

            <h3>
                <?php echo $plat['titre_plat']; ?>
            </h3>

            <img src="../Sources/upload/<?php echo $plat['photo']; ?>" 
                 alt="<?php echo $plat['titre_plat']; ?>">

        </div>

    <?php } ?>

</div>



    <aside class="filters">

        <form action="" method="GET">

            <div>

                <label for="Date">
                    Date de l'évènement
                </label>

                <input
                    id="Date"
                    name="Date"
                    type="date"
                    required
                >

            </div>

            <br>

            <div>

                <label for="number">
                    Nombre de convives
                </label>

                <input
                    id="number"
                    name="number"
                    type="number"
                    min="1"
                    required
                >

            </div>

            <br>

            <div>

                <label for="total">
                    Prix du menu
                </label>

                <input
                    id="total"
                    name="total"
                    type="text"
                    value="<?php echo $menu['prix_par_personne']; ?> €"
                    readonly
                >

            </div>

            <br>

            <?php if ($utilisateur_connecte) { ?>

                <a href="Espace_commande.php?menu_id=<?php echo $menu['menu_id']; ?>">

                    <input
                        type="button"
                        value="Commander"
                    >

                </a>

            <?php } else { ?>

                <div class="message_connexion">

                    <p>
                        Vous devez être connecté pour commander ce menu.
                    </p>

                    <br>

                    <a href="login.php">

                        <button type="button">
                            Se connecter
                        </button>

                    </a>

                    <a href="Create_account.php">

                        <button type="button">
                            Créer un compte
                        </button>

                    </a>

                </div>

            <?php } ?>

        </form>

    </aside>

</main>

<?php include("../includes/footer.php"); ?>

</body>

</html>