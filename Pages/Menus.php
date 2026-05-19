<?php
include("../includes/database.php");

$sql = "SELECT * FROM menu WHERE 1=1";

if(!empty($_GET["pricemax"])) {
    $pricemax = (int) $_GET["pricemax"];
    $sql .= " AND (prix_par_personne * nombre_personne_minimum) <= $pricemax";
}

if(!empty($_GET["persmin"])) {
    $persmin = (int) $_GET["persmin"];
    $sql .= " AND nombre_personne_minimum >= $persmin";
}

if(!empty($_GET["pricemin"])) {

    if($_GET["pricemin"] == "100-300") {
        $sql .= " AND (prix_par_personne * nombre_personne_minimum) BETWEEN 100 AND 300";
    }

    elseif($_GET["pricemin"] == "300-500") {
        $sql .= " AND (prix_par_personne * nombre_personne_minimum) BETWEEN 300 AND 500";
    }

    elseif($_GET["pricemin"] == "500-700") {
        $sql .= " AND (prix_par_personne * nombre_personne_minimum) BETWEEN 500 AND 700";
    }

    elseif($_GET["pricemin"] == "700+") {
        $sql .= " AND (prix_par_personne * nombre_personne_minimum) > 700";
    }

}

if(!empty($_GET["theme"])) {
    $theme = (int) $_GET["theme"];
    $sql .= " AND theme_id = $theme";
}

if(!empty($_GET["regime"])) {
    $regime = (int) $_GET["regime"];
    $sql .= " AND regime_id = $regime";

}

if(!empty($_GET["tri"])) {

    if($_GET["tri"] == "prix_asc") {
        $sql .= " ORDER BY prix_par_personne ASC";
    }

    elseif($_GET["tri"] == "prix_desc") {
        $sql .= " ORDER BY prix_par_personne DESC";
    }

    elseif($_GET["tri"] == "recent") {
        $sql .= " ORDER BY menu_id DESC";
    }

}


$req = $pdo->query($sql);
$themes = $pdo->query("SELECT * FROM theme");
$regimes = $pdo->query("SELECT * FROM regime");

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
                <h1> Nos menus </h1>
                <p> <em>Découvrez nos différents menus </em> </p>
                </div>
            </div>

    <main class="mainmenu"> 

      <div class="toolbar">

      <aside class="sort">
        <h3>Trier par :</h3>
        <div class="sortfilter">
          <a href="Menus.php?<?= http_build_query(array_merge($_GET, ['tri' => 'prix_desc'])) ?>"> Prix décroissant </a>
        </div>

        <div class="sortfilter">
          <a href="Menus.php?<?= http_build_query(array_merge($_GET, ['tri' => 'prix_asc'])) ?>"> Prix croissant </a>
        </div>
        
        <div class="sortfilter">
          <a href="Menus.php?<?= http_build_query(array_merge($_GET, ['tri' => 'recent'])) ?>"> Nouveauté </a>
        </div>

        <div class="sortfilter">
          <a href="Menus.php">Réinitialiser</a>
        </div>
      </aside>

      <aside class="filters">
        <h3> Filtrer par : </h3>
        <form action="Menus.php" method="GET">
          <div class="filter">
            <label for="pricemax"> Prix maximum : </label>
            <input type="number" name="pricemax" id="pricemax" value="<?= $_GET["pricemax"] ?? "" ?>" placeholder="Prix maximum">
          </div>

          <div class="filter">
            <label for="pricemin">Fourchette de prix :</label>
            <select name="pricemin" id="pricemin">
              <option value="">Choisir</option>
              <option value="100-300"<?= ($_GET["pricemin"] ?? "") == "100-300" ? "selected" : "" ?>> Entre 100€ et 300€ </option>
              <option value="300-500" <?= ($_GET["pricemin"] ?? "") == "300-500" ? "selected" : "" ?>> Entre 300€ et 500€ </option>
              <option value="500-700" <?= ($_GET["pricemin"] ?? "") == "500-700" ? "selected" : "" ?>> Entre 500€ et 700€ </option>
              <option value="700+" <?= ($_GET["pricemin"] ?? "") == "700+" ? "selected" : "" ?>> Plus de 700€ </option>
            </select>
          </div>

          <div class="filter">
            <label for="theme"> Thème : </label>
            <select name="theme" id="theme">
              <option value="">Choisir</option>
              <?php while($theme = $themes->fetch()) { ?>
              <option value="<?= $theme["theme_id"]; ?>" 
              <?= ($_GET["theme"] ?? "") == $theme["theme_id"] ? "selected" : "" ?>> 
              <?= $theme["libelle"]; ?> </option>
              <?php } ?>
            </select>
          </div>

          <div class="filter">
            <label for="regime"> Régime alimentaire : </label>
            <select name="regime" id="regime">
               <option value="">Choisir</option>
               <?php while($regime = $regimes->fetch()) { ?>
               <option value="<?= $regime["regime_id"]; ?>"
               <?= ($_GET["regime"] ?? "") == $regime["regime_id"] ? "selected" : "" ?>>
               <?= $regime["libelle"]; ?>
               </option>
               <?php } ?>
            </select>
          </div>

          <div class="filter">
            <label for="persmin"> Nombre de Personnes min :</label>
            <input type="number" name="persmin" id="persmin" value="<?= $_GET["persmin"] ?? "" ?>" placeholder="Nombre minimum de personnes">
          </div>
            <input type="submit" value="Filtrer">
        </form>

      </aside>
      </div>
      



      <div class="menus">
      
      <div class="filtres-actifs">

<h3>Filtres actifs :</h3>

<?php if(!empty($_GET["pricemax"])) { ?>
<p>• Prix max : <?= $_GET["pricemax"]; ?> €</p>
<?php } ?>

<?php if(!empty($_GET["persmin"])) { ?>
<p>• Personnes minimum : <?= $_GET["persmin"]; ?></p>
<?php } ?>

<?php if(!empty($_GET["theme"])) { ?>
<?php
$themeActif = $pdo->query("SELECT libelle FROM theme WHERE theme_id = ".$_GET["theme"]);
$themeNom = $themeActif->fetch();
?>
<p>• Thème : <?= $themeNom["libelle"]; ?></p>
<?php } ?>

<?php if(!empty($_GET["regime"])) { ?>
<?php
$regimeActif = $pdo->query("SELECT libelle FROM regime WHERE regime_id = ".$_GET["regime"]);
$regimeNom = $regimeActif->fetch();
?>
<p>• Régime : <?= $regimeNom["libelle"]; ?></p>
<?php } ?>

<?php if(!empty($_GET["tri"])) { ?>

<?php if($_GET["tri"] == "prix_asc") { ?>
<p>• Tri : Prix croissant</p>
<?php } ?>

<?php if($_GET["tri"] == "prix_desc") { ?>
<p>• Tri : Prix décroissant</p>
<?php } ?>

<?php if($_GET["tri"] == "recent") { ?>
<p>• Tri : Nouveauté</p>
<?php } ?>

<?php } ?>

</div>


        <div class="menus-content"> 
          <?php while($menu = $req->fetch(PDO::FETCH_ASSOC)) { ?>
         <div class="menu">
          <img src="../Sources/Images page menu/menu Noël.jpeg" alt="menu">
          <div class="card-content">
            <h2><?= $menu["titre"]; ?></h2>
            <p>nombre minimum : <?= $menu["nombre_personne_minimum"]; ?></p>
            <p>À partir de :<?= $menu["prix_par_personne"] * $menu["nombre_personne_minimum"]; ?> €</p>
            <p> <?= $menu["prix_par_personne"]; ?> € / personne (minimum <?= $menu["nombre_personne_minimum"]; ?> pers.)</p>
            <p><?= $menu["description"]; ?></p>
            <a href="Detail_menus.php?menu_id=<?= $menu["menu_id"]; ?>">détails</a>
          </div>
        </div>
        <?php } ?>
          
       </div>
        
          
      </div>

      <aside>
        
      </aside>
    </main>
    
    <?php include("../includes/footer.php"); ?>
    
  </body>
</html>