<?php
    require_once('./api/account.php');
    require_once('./api/paiment.php');
    require_once('./api/order.php');

    $params = get_paiment_params();
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Panier - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/global.css">
        <link rel="stylesheet" href="./styles/forms.css">
        <link rel="stylesheet" href="./styles/basket.css">

        <script src="./scripts/theme.js" defer></script>
        <script src="./scripts/nav.js" defer></script>
        <script src="./scripts/api.js" defer></script>
        <script src="./scripts/basket.js" defer></script>
        <script src="./scripts/form_validation.js" defer></script>
    </head>
    <body>
        <?php include_once('./components/header.php'); ?>

        <main>
            <h1>Mon panier</h1>

            <div id="basket__container">
                <section id="basket__items" class="form__card">
                    <h2>Articles commandés</h2>

                    <div class="scrollable__wrapper">
                        <div class="scrollable__container"></div>
                    </div>
                </section>
                
                <section id="basket__summary" class="form__card">
                    <h2>Résumé</h2>

                    <form method="POST" action="<?php echo htmlspecialchars($params['action_url']); ?>">
                        <div class="form__group col__group">
                            <label for="delivery_type">Mode de livraison</label>

                            <select id="delivery_type" name="delivery_type" required>
                                <option id="default__option" value="">Choisissez une option</option>
                                <option value="takeaway">À emporter</option>
                                <option value="delivery">En livraison</option>
                            </select>
                        </div>

                        <div id="spacer"></div>
                        
                        <div class="form__group row__group">
                            <input id="promotion__code" name="promotion__code" type="text" placeholder="Entrer un code promo" autocomplete="off" />
                            <button id="promo__button" class="btn btn-primary" type="button">Appliquer</button>
                        </div>

                        <div id="price__summary">
                            <div id="subtotal__container" class="form__group row__group">
                                <p>Sous-total</p>
                                <p id="subtotal__price"></p>
                            </div>
                            <div id="promo__container" class="form__group row__group">
                                <p>Promotion</p>
                                <p id="promo__summary"></p>
                            </div>
                            <hr />
                            <div id="total__container" class="form__group row__group">
                                <h4>Total</h4>
                                <h4 id="total__price"></h4>
                            </div>
                        </div>

                        <input type='hidden' name='transaction' value='<?php echo htmlspecialchars($params['transaction']); ?>'>
                        <input type='hidden' name='montant' value='<?php echo htmlspecialchars($params['montant']); ?>'>
                        <input type='hidden' name='vendeur' value='<?php echo htmlspecialchars($params['vendeur']); ?>'>
                        <input type='hidden' name='retour' value='<?php echo htmlspecialchars($params['retour']); ?>'>
                        <input type='hidden' name='control' value='<?php echo htmlspecialchars($params['control']); ?>'>

                        <?php if(is_logged()) echo '<button class="btn btn-primary" type=\'submit\' disabled>Valider et payer</button>'; ?>
                    </form>

                    <?php if(!is_logged()) echo '<a class="btn btn-primary" href="./sign_in.php?redirection=basket">Se connecter pour commander</a>'; ?>
                    
                    <?php
                        if (isset($_SESSION['error']) && $_SESSION['error'] != '') {
                            echo '<p class="error">' . htmlspecialchars(urldecode($_SESSION['error'])) . '</p>';
                            unset($_SESSION['error']);
                        }
                    ?>
                </section>
            </div>
        </main>
        
        <?php include_once('./components/footer.php'); ?>
    </body>
</html>
