<?php
    if(!function_exists("get_access")) require_once('./api/account.php');

    get_access(["employee", "admin"], true);
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Commandes - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/global.css">
        <link rel="stylesheet" href="./styles/forms.css">
        <link rel="stylesheet" href="./styles/orders.css">

        <script src="./scripts/shared.js" defer></script>
        <script src="./scripts/theme.js" defer></script>
        <script src="./scripts/nav.js" defer></script>
        <script src="./scripts/api.js" defer></script>
        <script src="./scripts/form_validation.js" defer></script>
        <script src="./scripts/orders.js" defer></script>
    </head>
    <body>
        <?php include_once('./components/header.php'); ?>

        <main>
            <h1>Commandes</h1>

            <section id="orders__container" class="form__card">
                <h2>Commandes à préparer</h2>

                <div class="scrollable__wrapper">
                    <div class="scrollable__container">
                        <?php include_once('./components/orders_table.php'); ?>
                    </div>
                </div>
            </section>
        </main>
        
        <?php include_once('./components/footer.php'); ?>
    </body>
</html>