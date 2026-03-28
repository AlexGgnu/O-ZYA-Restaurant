<?php
    require_once('./php/function_account.php');
    require_once('./php/function_orders.php');

    require_once('./php/header.php');

    get_access("restaurateur", true);

    $commandes = lireCommandes('./data/orders.json');
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Commandes - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/main.css">

        <script src="./scripts/common.js" defer></script>
    </head>
    <body>
        <?php echo get_header(false, false); ?>

        <main class="flex-col gap-40 ph-40">
            <h1 class="w-full">Commandes</h1>

            <div class="form-card flex-1 gap-24 max-w-full m-0">
                <h2 class="text-primary font-600">Commandes à préparer</h2>

                <div class="scrollable-wrapper flex-1">
                    <div class="scrollable-container">
                        <table class="w-full text-sm">
                            <thead>
                                <tr>
                                    <th>ID Commande</th>
                                    <th>client</th>
                                    <th>Adresse</th>
                                    <th>Detail</th>
                                    <th>Total</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php afficherLignesCommandes($commandes); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>