<?php
    if(!function_exists('get_access') || !function_exists('get_account_by_id')) require_once('./api/account.php');
    if(!function_exists('get_assigned_order')) require_once('./api/order.php');
    if(!function_exists('format_order_details')) require_once('./components/orders_table.php');

    get_access(["delivery"], true);
    $assigned_order = get_assigned_order($_SESSION['uuid']);
    if($assigned_order) $client = get_account_by_id($assigned_order['id_client']);

    $maps_base_url = 'https://www.google.com/maps/search/?api=1&query=';
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Livraison - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/global.css">
        <link rel="stylesheet" href="./styles/forms.css">
        <link rel="stylesheet" href="./styles/delivery.css">

        <script src="./scripts/shared.js" defer></script>
        <script src="./scripts/theme.js" defer></script>
        <script src="./scripts/nav.js" defer></script>
        <script src="./scripts/api.js" defer></script>
        <script src="./scripts/delivery.js" defer></script>
    </head>
    <body>
        <?php include_once('./components/header.php'); ?>

        <main>
            <section class="form__card">
                <?php if (is_array($assigned_order)) { ?>
                    <h2>Commande #<?php echo htmlspecialchars($assigned_order['id_order']); ?></h2>

                    <div>
                        <div class="form__group col__group">
                            <h3>Infos Client</h3>
                            <div>
                                <?php echo '<p>' . ($client['gender'] === 'homme' ? 'Mr' : 'Mme') . ' ' . strtoupper(htmlspecialchars($client['lastname'])) . ' '. htmlspecialchars($client['firstname']) . '</p>'; ?>
                                <?php echo '<p><span>Téléphone :</span> ' . htmlspecialchars($client['phone']) . '</p>'; ?>
                                <?php echo '<p><span>Adresse :</span> ' . htmlspecialchars($assigned_order['address']) . '</p>'; ?>
                            </div>
                        </div>

                        <div class="form__group col__group">
                            <h3>Infos Commande</h3>

                            <div>
                                <?php echo '<p><span>Menus :</span><br/>' . format_order_details($assigned_order['details']). '</p>'; ?>
                                <?php echo '<p><span>Total :</span> ' . htmlspecialchars($assigned_order['total']) . '€</p>'; ?>
                            </div>
                        </div>

                        <div id="delivery__buttons" class="form__group col__group">
                            <a href="<?php echo $maps_base_url . rawurlencode($assigned_order['address']); ?>" target="_blank" class="btn btn-primary">Ouvrir Maps</a>
                            <button id="validate__delivery__button" class="btn btn-primary" data-order-id="<?php echo htmlspecialchars($assigned_order['id_order']); ?>">Terminer la livraison</button>
                        </div>
                    </div>
                <?php } else { ?>
                    <h2>Aucune commande attribuée</h2>
                    <p>Aucune commande n'est actuellement attribuée à votre compte</p>
                <?php } ?>
            </section>
        </main>

        <?php include_once('./components/footer.php'); ?>
    </body>
</html>