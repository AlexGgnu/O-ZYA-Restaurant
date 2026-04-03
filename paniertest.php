<?php
    require_once('./php/function_panier.php');
    require_once('./php/footer.php');
    require_once('./php/function_paiments.php');

    $params = get_paiment_params();
?>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Panier - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/main.css">
    </head>
    <body>
        <?php echo get_header(false, false); ?>

        <main class="flex-col gap-40 ph-40">
            <h1 class="w-full">Mon panier</h1>

            <div class="flex-row items-stretch gap-20 w-full">
                <section class="flex-1 w-full min-h-0">
                    <div class="form-card h-full gap-24 max-w-full m-0">
                        <h2 class="text-primary font-600">Articles commandés</h2>

                        <div class="scrollable-wrapper flex-1">
                            <div class="scrollable-container flex-col gap-14">
                                <?php afficher_panier(); ?>
                            </div>
                        </div>
                    </div>

  </body>
</html>