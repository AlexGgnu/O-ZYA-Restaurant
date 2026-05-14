<?php
    require_once('./php/function_account.php');
    require_once('./php/function_basket.php');
    require_once('./php/function_paiments.php');
    
    require_once('./php/header.php');
    require_once('./php/footer.php');

    if (isset($_POST['delivery_type'])) $_SESSION['delivery_type'] = $_POST['delivery_type'];
    $delivery_type = isset($_SESSION['delivery_type']) ? $_SESSION['delivery_type'] : 'takeaway';
    
    $promo_code = get_promo_code_from_post();
    $basket = get_basket();
    $basket_is_empty = empty($basket);
    $params = $basket_is_empty ? null : get_paiment_params($promo_code);
    $payment_error_message = get_payment_error_message();
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

        <main class="flex-col gap-40 ph-40 h-content">
            <h1 class="w-full">Mon panier</h1>

            <div class="flex-row items-stretch gap-20 w-full flex-1 min-h-0">
                <section class="flex-1 w-full min-h-0">
                    <div class="form-card h-full gap-24 max-w-full m-0">
                        <h2 class="text-primary font-600">Articles commandés</h2>

                        <div class="scrollable-wrapper flex-1 min-h-0">
                            <div class="scrollable-container">
                                <?php afficher_panier(); ?>
                            </div>
                        </div>
                    </div>
                </section>
                
                <section class="flex-1 w-full flex-col gap-20 min-h-0">
                    <div class="form-card h-full gap-24 max-w-full m-0">
                        <h2 class="text-primary font-600">Résumé</h2>

                        <form method="POST">
                            <div class="flex-col gap-10">
                                <label class="font-600" for="promo">Code promo</label>
                                <div class="flex-row items-center gap-14">
                                    <input class="flex-1" id="promo" name="code_promo" type="text" placeholder="Entrer un code promo" value="">
                                    <button class="btn btn-primary" type="submit">Appliquer</button>
                                </div>
                            </div>
                        </form>

                        <div class="flex-1"></div>

                        <form method="POST">
                            <div class="flex-col gap-10">
                                <label class="font-600" for="delivery_type">Mode de livraison</label>
                                <div class="flex-row items-center gap-14">
                                    <select id="delivery_type" name="delivery_type" class="form-control">
                                        <option value="takeaway" <?php echo $delivery_type === 'takeaway' ? 'selected' : ''; ?>>À emporter</option>
                                        <option value="delivery" <?php echo $delivery_type === 'delivery' ? 'selected' : ''; ?>>En livraison</option>
                                    </select>
                                    <button class="btn btn-primary" type="submit">Confirmer</button>
                                </div>
                            </div>
                        </form>

                        <?php if (is_logged()) { ?>
                            <form class="flex-col gap-10" action='<?php echo $basket_is_empty ? '' : htmlspecialchars($params['action_url']); ?>' method='POST'>
                                <?php if ($payment_error_message != '') { ?>
                                    <p class="text-center text-error mb-24"><?php echo $payment_error_message; ?></p>
                                <?php } ?>

                                <?php if (!$basket_is_empty) { ?>
                                    <input type='hidden' name='transaction' value='<?php echo htmlspecialchars($params['transaction']); ?>'>
                                    <input type='hidden' name='montant' value='<?php echo htmlspecialchars($params['montant']); ?>'>
                                    <input type='hidden' name='vendeur' value='<?php echo htmlspecialchars($params['vendeur']); ?>'>
                                    <input type='hidden' name='retour' value='<?php echo htmlspecialchars($params['retour']); ?>'>
                                    <input type='hidden' name='control' value='<?php echo htmlspecialchars($params['control']); ?>'>
                                <?php } ?>
                                <input class="btn btn-primary" type='submit' value="Valider et payer" <?php echo $basket_is_empty ? 'disabled' : ''; ?>>
                            </form>
                        <?php } else { ?>
                            <a class="btn btn-primary" href="./sign_in.php?redirection=basket">Se connecter pour commander</a>
                        <?php } ?>
                    </div>
                </section>
            </div>
        </main>
        
        <?php echo get_footer(true); ?>
    </body>
</html>
