<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title> Page d'accueil </title>
        <link rel="stylesheet" href="../assets/style.css">
    </head>

    

    <body>

        <?php include("../includes/header.php"); ?>

         <main>

             
            <div class="pub"> 
                <div class="pub-content">
                <h1> Des menus sur-mesure pour vos évènements privés ou professionnels </h1>
                <p> Mariages, anniversaires, séminaires ou repas d'entreprise, nous créons des expériences culinaires conviviales et élégantes.</p>
                <button> Découvrir nos menus </button> 
                </div>
            </div>


             

            <div class="description">
                <div class="description-content"> 
                    <h1> Une entreprise engagée au service de vos évènements</h1>
                </p> Notre équipe accompagne depuis plus de 25 ans, particuliers et entreprises dans l’organisation de leurs évenements en proposant des menus de qualités, élaborés avec soin. </p>
                </div> 
                
                <img src="../Sources/Image description.jpeg" alt="image chefs cuisinier">
            </div>

             

            <div class="qualite">

                <h1> Notre savoir faire à votre service </h1>
                
                <div class="cards">
                    
                   <div class="card1">
                       <div class="icone">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A84C27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chef-hat-icon lucide-chef-hat">
                               <path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/>
                           </svg>
                       </div>

                    <h2> Equipe expérimentée </h2>
                    <p> 25 ans d'expérience pour vous servir</p>
                   </div>

                   <div class="card2">
                       <div class="icone">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A84C27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-carrot-icon lucide-carrot">
                               <path d="M2.27 21.7s9.87-3.5 12.73-6.36a4.5 4.5 0 0 0-6.36-6.37C5.77 11.84 2.27 21.7 2.27 21.7zM8.64 14l-2.05-2.04M15.34 15l-2.46-2.46"/><path d="M22 9s-1.33-2-3.5-2C16.86 7 15 9 15 9s1.33 2 3.5 2S22 9 22 9z"/><path d="M15 2s-2 1.33-2 3.5S15 9 15 9s2-1.84 2-3.5C17 3.33 15 2 15 2z"/>
                           </svg>
                       </div>
                               
                    <h2> Produits de qualité </h2>
                    <p> Produits de saison, d’origine française </p>
                   </div>

                   <div class="card3">
                       <div class="icone">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A84C27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-notebook-text-icon lucide-notebook-text"><path d="M2 6h4"/><path d="M2 10h4"/><path d="M2 14h4"/><path d="M2 18h4"/><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9.5 8h5"/><path d="M9.5 12H16"/><path d="M9.5 16H14"/>
                           </svg>
                       </div>
                    <h2> Organisation efficace </h2>
                    <p>Gestion précise des commandes et délais </p>
                   </div>
                    
                </div>
            
            </div>
 

            <div class="avis">

                <h1> Ils nous ont fait confiance </h1>
                
                <div class="avis-content">

                   <?php
                  $avis = [ 
                  ["texte"=>"Repas parfait pour notre mariage", "client"=>"Julie"],
                  ["texte"=>"Service impeccable", "client"=>"Samuel"],
                  ["texte"=>"Tout le monde ravi", "client"=>"Sophie"]
                  ];
                
                  foreach($avis as $client) { 
                  ?>

                <div class="avis-card">

                    <div class="stars">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <p> <?= $client["texte"] ?> </p>
                    <span> <?= $client["client"] ?> </span>
             
                    <?php 
                     }
                    ?>
                </div>
            
                </div>
            </div>


        </main>    
        
        <?php include("../includes/footer.php"); ?>

        
    </body>
</html>
