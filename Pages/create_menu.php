<?php

session_start();

require_once("../includes/database.php");



/* =========================
   RÉCUPÉRATION DES PLATS
========================= */

$sqlPlats = "SELECT * FROM plat ORDER BY categorie, titre_plat";
$stmtPlats = $pdo->query($sqlPlats);
$plats = $stmtPlats->fetchAll();



/* =========================
   CRÉATION DU MENU
========================= */

$message = "";

if(isset($_POST["create_menu"]))
{

    $titre = trim($_POST["titre"]);
    $description = trim($_POST["description"]);
    $minimum = $_POST["nombre_personne_minimum"];
    $prix = $_POST["prix_par_personne"];
    $quantite = $_POST["quantite"];
    $theme = $_POST["theme"];
    $regime = $_POST["regime"];



    /* =========================
       IMAGE
    ========================= */

    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $tmpName = $_FILES["image"]["tmp_name"];

    $imagePath = "../Sources/uploads/" . $imageName;

    move_uploaded_file($tmpName, $imagePath);



    /* =========================
       INSERT MENU
    ========================= */

    $sqlMenu = "INSERT INTO menu
    (
        titre,
        description,
        nombre_personne_minimum,
        prix_par_personne,
        image,
        quantite_restante,
        theme_id,
        regime_id
    )

    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?, ?
    )";

    $stmtMenu = $pdo->prepare($sqlMenu);

    $stmtMenu->execute([
    $titre,
    $description,
    $minimum,
    $prix,
    $imagePath,
    $quantite,
    $theme,
    $regime
]);


    /* =========================
       RÉCUPÉRATION MENU_ID
    ========================= */

    $menuId = $pdo->lastInsertId();



    /* =========================
       INSERT DANS MENU_PLAT
    ========================= */

    if(isset($_POST["plats"]))
    {

        $sqlMenuPlat = "INSERT INTO menu_plat
        (
            menu_id,
            plat_id
        )

        VALUES
        (
            ?, ?
        )";

        $stmtMenuPlat = $pdo->prepare($sqlMenuPlat);



        foreach($_POST["plats"] as $platId)
        {

            $stmtMenuPlat->execute([
                $menuId,
                $platId
            ]);

        }

    }



    $message = "Le menu a été créé avec succès.";

}

$themeRequest = $pdo->query("
    SELECT *
    FROM theme
");

$themes = $themeRequest->fetchAll();


$regimeRequest = $pdo->query("
    SELECT *
    FROM regime
");

$regimes = $regimeRequest->fetchAll();

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>Créer un menu</title>

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>

<?php include("../includes/header.php"); ?>



<main class="create_menu_page">

    <section class="create_menu_container">

        <div class="create_menu_header">

            <h1>Créer un nouveau menu</h1>

            <p>
                Ajoutez un nouveau menu qui apparaîtra automatiquement
                sur le site client.
            </p>

        </div>



        <?php if($message) : ?>

            <div class="success_message">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>



        <form method="POST"
              enctype="multipart/form-data"
              class="create_menu_form">


            <div class="form_section">

                <h2>Informations du menu</h2>

                <div class="form_group">

                    <label for="titre">

                        Titre du menu

                    </label>

                    <input type="text"
                           id="titre"
                           name="titre"
                           required>

                </div>



                <div class="form_group">

                    <label for="description">

                        Description

                    </label>

                    <textarea id="description"
                              name="description"
                              rows="5"
                              required></textarea>

                </div>



                <div class="form_row">

                    <div class="form_group">

                        <label for="minimum">

                            Nombre minimum de personnes

                        </label>

                        <input type="number"
                               id="minimum"
                               name="nombre_personne_minimum"
                               required>

                    </div>



                    <div class="form_group">

                        <label for="prix">

                            Prix par personne

                        </label>

                        <input type="number"
                               step="0.01"
                               id="prix"
                               name="prix_par_personne"
                               required>

                    </div>

                    <div class="form_group">
    <label for="quantite">
        Quantité restante
    </label>

    <input
        type="number"
        name="quantite"
        id="quantite"
        required
    >
</div>

<div class="form_group">
    <label for="theme">
        Thème
    </label>

    <select name="theme" id="theme" required>

        <option value="">
            Choisir un thème
        </option>

        <?php foreach($themes as $theme) : ?>

            <option value="<?= $theme["theme_id"] ?>">

                <?= htmlspecialchars($theme["libelle"]) ?>

            </option>

        <?php endforeach; ?>

    </select>
</div>

<div class="form_group">
    <label for="regime">
        Régime alimentaire
    </label>

    <select name="regime" id="regime" required>

        <option value="">
            Choisir un régime
        </option>

        <?php foreach($regimes as $regime) : ?>

            <option value="<?= $regime["regime_id"] ?>">

                <?= htmlspecialchars($regime["libelle"]) ?>

            </option>

        <?php endforeach; ?>

    </select>
</div>

                </div>



                <div class="form_group">

                    <label for="image">

                        Image du menu

                    </label>

                    <input type="file"
                           id="image"
                           name="image"
                           accept="image/*"
                           required>

                </div>

            </div>


            <div class="form_section">

                <h2>Entrées</h2>

                <div class="plats_grid">

                    <?php foreach($plats as $plat) : ?>

                        <?php if($plat["categorie"] === "entree") : ?>

                            <label class="plat_card">

                                <input type="checkbox"
                                       name="plats[]"
                                       value="<?= $plat["plat_id"] ?>">

                                <div class="plat_content">

                                    <img src="../Sources/Images plats/<?= htmlspecialchars($plat["photo"]) ?>"
                                         alt="<?= htmlspecialchars($plat["titre_plat"]) ?>">

                                    <h3>

                                        <?= htmlspecialchars($plat["titre_plat"]) ?>

                                    </h3>

                                </div>

                            </label>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

            </div>



            <!-- =========================
                 PLATS
            ========================== -->

            <div class="form_section">

                <h2>Plats</h2>

                <div class="plats_grid">

                    <?php foreach($plats as $plat) : ?>

                        <?php if($plat["categorie"] === "plat") : ?>

                            <label class="plat_card">

                                <input type="checkbox"
                                       name="plats[]"
                                       value="<?= $plat["plat_id"] ?>">

                                <div class="plat_content">

                                    <img src="../Sources/Images plats/<?= htmlspecialchars($plat["photo"]) ?>"
                                         alt="<?= htmlspecialchars($plat["titre_plat"]) ?>">

                                    <h3>

                                        <?= htmlspecialchars($plat["titre_plat"]) ?>

                                    </h3>

                                </div>

                            </label>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

            </div>



            <!-- =========================
                 DESSERTS
            ========================== -->

            <div class="form_section">

                <h2>Desserts</h2>

                <div class="plats_grid">

                    <?php foreach($plats as $plat) : ?>

                        <?php if($plat["categorie"] === "dessert") : ?>

                            <label class="plat_card">

                                <input type="checkbox"
                                       name="plats[]"
                                       value="<?= $plat["plat_id"] ?>">

                                <div class="plat_content">

                                    <img src="../Sources/Images plats/<?= htmlspecialchars($plat["photo"]) ?>"
                                         alt="<?= htmlspecialchars($plat["titre_plat"]) ?>">

                                    <h3>

                                        <?= htmlspecialchars($plat["titre_plat"]) ?>

                                    </h3>

                                </div>

                            </label>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

            </div>



            <!-- =========================
                 BOISSONS
            ========================== -->

            <div class="form_section">

                <h2>Boissons</h2>

                <div class="plats_grid">

                    <?php foreach($plats as $plat) : ?>

                        <?php if($plat["categorie"] === "boisson") : ?>

                            <label class="plat_card">

                                <input type="checkbox"
                                       name="plats[]"
                                       value="<?= $plat["plat_id"] ?>">

                                <div class="plat_content">

                                    <img src="../Sources/Images plats/<?= htmlspecialchars($plat["photo"]) ?>"
                                         alt="<?= htmlspecialchars($plat["titre_plat"]) ?>">

                                    <h3>

                                        <?= htmlspecialchars($plat["titre_plat"]) ?>

                                    </h3>

                                </div>

                            </label>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

            </div>



            <!-- =========================
                 ACTIONS
            ========================== -->

            <div class="form_actions">

                <button type="submit"
                        name="create_menu"
                        class="btn-primary">

                    Créer le menu

                </button>



                <a href="Admin.php?section=menus"
                   class="btn-secondary">

                    Retour

                </a>

            </div>

        </form>

    </section>

</main>

<?php include("../includes/footer.php"); ?>

</body>

</html>