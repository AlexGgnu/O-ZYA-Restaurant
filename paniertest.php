<?php
    require_once('./php/function_panier.php');
    require_once('./php/footer.php');
    require_once('./php/function_paiments.php');
    require_once('./php/function_basket.php');
    $params = get_paiment_params();
?>

<!DOCTYPE html>
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
                    </section>
                
                    <section class="flex-1 w-full flex-col gap-20">
                        <div class="form-card gap-24 max-w-full m-0">
                            <h2 class="text-primary font-600">Résumé</h2>

                        <form method="POST">
                            <div class="flex-col gap-10">
                                <label class="font-600" for="promo">Code promo</label>
                                <input id="promo" name="code_promo" type="text" placeholder="Entrer un code promo"
                                    value="<?php if (isset($_POST['code_promo'])){ echo $_POST['code_promo']; } ?>">
                                <button class="btn btn-secondary" type="submit">Appliquer</button>
                            </div>
                        </form>
                                                <form class="flex-col gap-10" action='https://www.plateforme-smc.fr/cybank/index.php' method='POST'>
                            <input type='hidden' name='transaction' value='UINIQUE_TRANSACTION_ID'>
                            <input type='hidden' name='montant' value='TOTAL_AMOUNT'>
                            <input type='hidden' name='vendeur' value='MI-3_C'>
                            <input type='hidden' name='retour' value='http://localhost/retour_paiement.php?session=s'>
                            <input type='hidden' name='control' value='01c06955b2d4ad0ccdedd4aad0ab68bf'>
                            <input class="btn btn-primary" type='submit' value="Valider et payer">
                        </form>
                    </div>
                </section>
            </div>
        </main>
        
        <?php echo get_footer(true); ?>
    </body>
</html>