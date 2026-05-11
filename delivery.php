<?php
    require_once('./php/function_account.php');
    require_once('./php/function_orders.php');

    get_access("delivery", true);

    $livreurId = isset($_SESSION['uuid']) ? $_SESSION['uuid'] : '';


    $commande = get_delivery_order_for_livreur($livreurId);
    $client = null;
    $mapsUrl = 'https://www.google.com/maps';

    if (is_array($commande) && isset($commande['id_client'])) {
        $client = get_account_by_id($commande['id_client']);
    }

    if (is_array($commande) && !empty($commande['adresse'])) {
        $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($commande['adresse']);
    }
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Livraison - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/main.css">
        <script src="./scripts/delivery.js" defer></script>
    </head>
    <body>
        <main class="justify-center sm-p-0">
            <div class="form-card gap-24 sm-flex-1 sm-justify-center sm-gap-40 sm-rounded-none">
                <?php if (is_array($commande)) { ?>
                    <h1 class="text-primary text-center font-bold">Commande #<?php echo htmlspecialchars($commande['id_order']); ?></h1>

                    <div class="flex-col gap-12">
                        <div class="client-infos">
                            <h2 class="font-600">Client</h2>
                            <p>Nom : <?php echo htmlspecialchars(is_array($client) && isset($client['lastname']) ? $client['lastname'] : 'Inconnu'); ?></p>
                            <p>Prénom : <?php echo htmlspecialchars(is_array($client) && isset($client['firstname']) ? $client['firstname'] : 'Inconnu'); ?></p>
                            <p>Téléphone : <?php echo htmlspecialchars(is_array($client) && isset($client['phone']) ? $client['phone'] : 'Inconnu'); ?></p>
                        </div>

                        <div id="client-adsress">
                            <h2 class="font-600">Adresse</h2>
                            <p>Adresse : <?php echo htmlspecialchars(isset($commande['adresse']) ? $commande['adresse'] : 'Inconnue'); ?></p>
                            <p>Date : <?php echo htmlspecialchars(isset($commande['date_heure']) ? $commande['date_heure'] : 'Inconnue'); ?></p>
                            <p>Statut : <?php echo htmlspecialchars(isset($commande['statut']) ? getLibelleStatut($commande['statut']) : 'Inconnu'); ?></p>
                        </div>

                        <div class="commentaire">
                            <h2 class="font-600">Commande</h2>
                            <p><?php echo htmlspecialchars(isset($commande['details']) ? formatDetailsCommande($commande['details']) : 'Aucun menu'); ?></p>
                            <p>Total : <?php echo htmlspecialchars(isset($commande['total']) ? $commande['total'] : '0'); ?>€</p>
                            <p>Paiement : <?php echo htmlspecialchars(isset($commande['statut_paiement']) ? getLibellePaiement($commande['statut_paiement']) : 'Non renseigné'); ?></p>
                        </div>
                    </div>
                    <div class="flex-col gap-16">
                        <a href="<?php echo htmlspecialchars($mapsUrl); ?>" target="_blank" class="btn btn-primary pv-16">Ouvrir Maps</a>
                        <button 
                        id="btn-terminer" 
                        class="btn btn-primary pv-16"
                        data-order-id="<?php echo htmlspecialchars($commande['id_order']); ?>">
                        Terminer la livraison
                        </button>
                        <p id="msg-livraison" style="display:none; text-align:center; margin-top:8px;"></p>
                    </div>
                <?php } else { ?>
                    <h1 class="text-primary text-center font-bold">Aucune commande attribuée</h1>
                    <p class="text-center">Il n'y a pas de commande attribuée pour le moment.</p>
                <?php } ?>
            </div>
        </main>
    </body>
</html>