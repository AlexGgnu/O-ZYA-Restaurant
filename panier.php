<?php
    require_once('./php/function_panier.php');
    require_once('./php/footer.php');
    require_once('./php/function_paiments.php');

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
                                <hr />

                                <div class="flex-row justify-between items-center gap-24">
                                    <div class="flex-row items-center gap-14">
                                        <img src="./assets/products/us/smash_burger.png" alt="Smash Burger New York" width="56" height="56">
                                        <div class="flex-col gap-6">
                                            <p class="font-600">Smash Burger New York</p>
                                            <p class="text-sm">16,90 €</p>
                                        </div>
                                    </div>

                                    <div class="flex-col items-end gap-6">
                                        <p class="font-600">x2</p>
                                        <p class="font-600 text-primary">33,80 €</p>
                                    </div>
                                </div>

                                <hr />

                                <div class="flex-row justify-between items-center gap-24">
                                    <div class="flex-row items-center gap-14">
                                        <img src="./assets/products/fr/frites.png" alt="Frites Maison" width="56" height="56">
                                        <div class="flex-col gap-6">
                                            <p class="font-600">Frites Maison</p>
                                            <p class="text-sm">5,50 €</p>
                                        </div>
                                    </div>

                                    <div class="flex-col items-end gap-6">
                                        <p class="font-600">x1</p>
                                        <p class="font-600 text-primary">5,50 €</p>
                                    </div>
                                </div>

                                <hr />

                                <div class="flex-row justify-between items-center gap-24">
                                    <div class="flex-row items-center gap-14">
                                        <img src="./assets/products/it/tiramisu.png" alt="Tiramisu Classique" width="56" height="56">
                                        <div class="flex-col gap-6">
                                            <p class="font-600">Tiramisu Classique</p>
                                            <p class="text-sm">8,00 €</p>
                                        </div>
                                    </div>

                                    <div class="flex-col items-end gap-6">
                                        <p class="font-600">x3</p>
                                        <p class="font-600 text-primary">24,00 €</p>
                                    </div>
                                </div>

                                <hr />
                            </div>
                        </div>
                    </div>
                </section>
                
                <section class="flex-1 w-full flex-col gap-20">
                    <div class="form-card gap-24 max-w-full m-0">
                        <h2 class="text-primary font-600">Résumé</h2>

                        <div class="flex-col gap-14">
                            <div class="flex-row justify-between items-center gap-24">
                                <p>Sous-total</p>
                                <p class="font-600">63,30 €</p>
                            </div>

                            <div class="flex-row justify-between items-center gap-24">
                                <p>Livraison</p>
                                <p class="font-600">2,99 €</p>
                            </div>

                            <div class="flex-row justify-between items-center gap-24">
                                <p>Réduction</p>
                                <p class="font-600">0,00 €</p>
                            </div>

                            <hr />

                            <div class="flex-row justify-between items-center gap-24">
                                <p class="font-600">Total</p>
                                <p class="font-600 text-primary">66,29 €</p>
                            </div>
                        </div>

                        <div class="flex-col gap-10">
                            <label class="font-600" for="promo">Code promo</label>
                            <input id="promo" type="text" placeholder="Entrer un code promo">
                            <button class="btn btn-secondary" type="button">Appliquer</button>
                        </div>

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