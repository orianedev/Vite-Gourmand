<?php

session_start();

include("../includes/database.php");


if (!isset($_GET['id_menu'])) {
    die("Menu introuvable");
}

$id_menu = $_GET['id_menu'];


$sql = "SELECT * FROM menu WHERE id_menu = :id_menu";

$requete = $db->prepare($sql);

$requete->bindParam(':id_menu', $id_menu);

$requete->execute();

$menu = $requete->fetch(PDO::FETCH_ASSOC);


if (!$menu) {
    die("Ce menu n'existe pas");
}

$utilisateur_connecte = isset($_SESSION['user']);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>Détail d'un menu</title>

    <link href="../assets/style.css" rel="stylesheet">

</head>

<body>

<?php include("../includes/header.php"); ?>

<main>

    <div class="pub">

        <div class="pub-content">

            <h1>
                <?php echo $menu['nom_menu']; ?>
            </h1>

            <p>
                <em>
                    <?php echo $menu['description_menu']; ?>
                </em>
            </p>

        </div>

    </div>


    <div>

        <p>
            <?php echo $menu['description_detaillee']; ?>
        </p>

        <a href="Menus.php">

            <button type="button">
                Retour aux menus
            </button>

        </a>

    </div>


    <div class="conditions_menu">

        <h2>
            Conditions importantes
        </h2>

        <p>
            <?php echo $menu['conditions_menu']; ?>
        </p>

    </div>


    <div class="Carte_menu">

        <div class="detail">

            <h2>Entrées</h2>

            <h3>
                <?php echo $menu['entree']; ?>
            </h3>

            <p>
                <?php echo $menu['description_entree']; ?>
            </p>

        </div>

      

        <div class="detail">

            <h2>Plats principaux</h2>

            <h3>
                <?php echo $menu['plat']; ?>
            </h3>

            <p>
                <?php echo $menu['description_plat']; ?>
            </p>

        </div>


        <div class="detail">

            <h2>Desserts</h2>

            <h3>
                <?php echo $menu['dessert']; ?>
            </h3>

            <p>
                <?php echo $menu['description_dessert']; ?>
            </p>

        </div>


        <div class="detail">

            <h2>Boissons</h2>

            <h3>
                <?php echo $menu['boisson']; ?>
            </h3>

            <p>
                <?php echo $menu['description_boisson']; ?>
            </p>

        </div>

    </div>

</main>


<aside class="filters">

    <p>
        Résumé de la commande
    </p>

    <br>

    <form action="" method="GET">


        <div>

            <label for="Date">
                Date de l'évènement
            </label>

            <input
                id="Date"
                name="Date"
                type="date"
                required>

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
                required>

        </div>

        <div>

            <label for="total">
                Prix du menu
            </label>

            <input
                id="total"
                name="total"
                type="text"
                value="<?php echo $menu['prix_menu']; ?> €"
                readonly>

        </div>


        <?php if ($utilisateur_connecte) { ?>


            <a href="Espace_commande.php?id_menu=<?php echo $menu['id_menu']; ?>">

                <input
                    type="button"
                    value="Commander">

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

<?php include("../includes/footer.php"); ?>

</body>

</html>