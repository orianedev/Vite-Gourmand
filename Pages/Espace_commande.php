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

$menus = $pdo->query("SELECT * FROM menu");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $telephone = $_POST["telephone"];
    $ville = $_POST["ville"];
    $adresse = $_POST["adresse_postale"];
    $code_postal = $_POST["code_postal"];

    $menu_id = $_POST["menu_id"];
    $date = $_POST["date_prestation"];
    $heure = $_POST["heure_livraison"];
    $nb = $_POST["nombre_personne"];

    $prixLivraison = 15;

    $sqlMenu = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
    $sqlMenu->execute([$menu_id]);
    $menu = $sqlMenu->fetch();

    $prixMenu = $menu["prix_par_personne"] * $nb;
    $total = $prixMenu + $prixLivraison;

    $update = $pdo->prepare("
        UPDATE utilisateur
        SET nom=?, prenom=?, email=?, telephone=?, ville=?, adresse_postale=?, code_postal=?
        WHERE utilisateur_id=?
    ");

    $update->execute([
        $nom, $prenom, $email, $telephone,
        $ville, $adresse, $code_postal, $user_id
    ]);

    $insert = $pdo->prepare("
        INSERT INTO commande
        (date_commande, date_prestation, heure_livraison,
        prix_menu, nombre_personne, prix_livraison,
        statut, pret_materiel, restitution_materiel,
        utilisateur_id, menu_id)

        VALUES
        (CURDATE(), ?, ?, ?, ?, ?, 'en preparation', 0, 0, ?, ?)
    ");

    $insert->execute([
        $date,
        $heure,
        $prixMenu,
        $nb,
        $prixLivraison,
        $user_id,
        $menu_id
    ]);

    $message = "Commande validée avec succès !";
}
?>

<!DOCTYPE html>
<html>
  <head>
        <meta charset="UTF-8">
        <title> Page d'accueil </title>
        <link rel="stylesheet" href="../assets/style.css">
   </head>

   <body>

       <?php include("../includes/header.php"); ?>

            <div class="pub"> 
                <div class="pub-content">
                <h1> Espace de commandes </h1>
                <p> <em>Réalisez vos commandes en quelques clics </em> </p>
                </div>
            </div>

        <main>
            <div class="infos">
                <h2> Informations personnelles </h2>
                <div class="infos-content">
                    <form method="POST">
                        <div>
                            <label for="name"> Nom :</label>
                            <input type="text" name="nom" value="<?= $user["nom"] ?>" required>
                        </div>
                        <br>
                        <div>
                            <label for="firstname"> Prénom :</label>
                            <input type="text" name="prenom" value="<?= $user["prenom"] ?>" required>
                         </div>
                         <br>
                         <div> 
                            <label for="email"> Email : </label>
                            <input type="email" name="email" value="<?= $user["email"] ?>" required>
                        </div>
                        <br>
                        <div>
                            <label for="Num"> Numéro de téléphone :</label>
                            <input type="text" name="telephone" value="<?= $user["telephone"] ?>" required>
                        </div>
                        <br>
                        <div>
                            <label for="adress"> Adresse postale : </label>
                            <input type="text" name="adresse_postale" value="<?= $user["adresse_postale"] ?>" required>
                        </div>
                        <br>
                        <div>
                            <label for="city"> Ville : </label>
                            <input type="text" name="ville" value="<?= $user["ville"] ?>" required>
                        </div>
                        <br>
                        <div>
                            <label for="postal-code"> Code postal :</label>
                            <input type="text" name="code_postal" value="<?= $user["code_postal"] ?>" required>
                        </div>
                        <br>
                        <div>
                            <label for="time"> Date et heure de livraison </label>
                            <input id="time" name="time" type="datetime"> 
                        </div>
                        <input type="submit" value="Valider">
                    </form>
                </div>
            </div>

            <div class="Choix-menu">
                <h2>Choisir votre menu</h2>
                <form method="post">
                    <label>Menu :</label>
                    <select name="menu" id="menuSelect" required>
                        <option value="">Choisir</option> 
                        <?php while($m = $menus->fetch()) { ?>
                        <option value="<?= $m["menu_id"] ?>"
                        data-prix="<?= $m["prix_par_personne"] ?>"
                        data-min="<?= $m["nombre_personne_minimum"] ?>"
                        data-image="<?= $m["image"] ?>"
                        data-lien="Detail_menus.php?id=<?= $m["menu_id"] ?>">
                        <?= $m["titre"] ?> </option>
                        <?php } ?>
                    </select>

                    <div id="apercuMenu">
                        <img id="imgMenu" src="" width="300">
                        <p id="prixMenu"></p>
                        <p id="minMenu"></p>
                        <a id="lienMenu" href="">Voir détails</a>
                    </div>
                    
                    <label>Nombre de personnes :</label>
                    <input type="number" id="nb_personne" name="nombre_personne" min="1" required>
                    <p id="totalMenu"></p>
                    
                    <br><br>
                    
                    <label>Date de prestation :</label>
                    <input type="date" id="date" name="date_prestation" required>
                    
                    <br><br>
                    
                    <label>Heure de livraison :</label>
                    <input type="time" id="heure" name="heure_livraison" required>
                    
                    <br><br>
                    
                    <input type="submit" value="Valider commande">
                </form>
            </div>

            <div>
                <a href="Detail_menus.php?id=1">Voir détails</a>
            </div>

        </main>

    <aside>
        <h2>Résumé de ma commande</h2>
        
        <p>Menu : <span id="resume_menu">-</span></p>
        <p>Nombre de personnes : <span id="resume_nb">-</span> </p>
        <p>Date : <span id="resume_date">-</span></p>
        <p>Heure : <span id="resume_heure">-</span></p>
        <p>Prix menu : <span id="resume_prix">0€</span></p>
        <p>Livraison : <span id="resume_livraison">15€</span></p>
        <p><strong>Total : <span id="resume_total">0€</span></strong></p>
    
    </aside>

       <?php include("../includes/footer.php"); ?>
   
<script> 

const select = document.getElementById("menuSelect");
const nb = document.querySelector('input[name="nombre_personne"]');
const dateInput = document.querySelector('input[name="date_prestation"]');
const heureInput = document.querySelector('input[name="heure_livraison"]');

const prixLivraison = 15;

function calculTotal() {

    let option = select.options[select.selectedIndex];

    if (!option || option.value == "") return;

    let prix = parseFloat(option.dataset.prix) || 0;
    let personnes = parseInt(nb.value) || 0;

    let totalMenu = prix * personnes;
    let total = totalMenu + prixLivraison;

    
    document.getElementById("imgMenu").src = option.dataset.image;
    document.getElementById("prixMenu").innerHTML = "Prix / pers : " + prix + " €";
    document.getElementById("minMenu").innerHTML = "Min : " + option.dataset.min + " pers";
    document.getElementById("lienMenu").href = option.dataset.lien;

    document.getElementById("totalMenu").innerHTML =
        "Total estimé : " + total + " €";

    
    document.getElementById("resume_menu").innerHTML = option.text;
    document.getElementById("resume_nb").innerHTML = personnes || "-";
    document.getElementById("resume_date").innerHTML = dateInput.value || "-";
    document.getElementById("resume_heure").innerHTML = heureInput.value || "-";
    document.getElementById("resume_prix").innerHTML = totalMenu + "€";
    document.getElementById("resume_total").innerHTML = total + "€";
}


select.addEventListener("change", calculTotal);
nb.addEventListener("input", calculTotal);
dateInput.addEventListener("input", calculTotal);
heureInput.addEventListener("input", calculTotal);

</script>

   </body>



</html>