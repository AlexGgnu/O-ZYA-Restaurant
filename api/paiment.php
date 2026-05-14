<?php
    if(!function_exists('get_basket_total')) require_once(__DIR__ . '/basket.php');
    if(!function_exists('getAPIKey')) require_once(__DIR__ . '/getapikey.php');
    if(!function_exists('save_order')) require_once(__DIR__ . '/order.php');

    function get_payment_return_url() {
        $protocol = 'http';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') $protocol = 'https';

        return $protocol . '://' . $_SERVER['HTTP_HOST'] . '/api/paiment.php?payment_return=1';
    }

    function generate_transaction_id() {
        return substr(strtoupper(uniqid('TRX')), 0, 24);
    }

    function get_payment_amount() {
        return number_format(get_basket_total(), 2, '.', '');
    }

    function get_payment_control($api_key, $transaction, $montant, $vendeur, $retour) {
        return md5($api_key . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $retour . '#');
    }

    function get_paiment_params() {
        $vendeur = 'MI-3_C';
        $transaction = generate_transaction_id();
        $montant = get_payment_amount();
        $retour = get_payment_return_url();
        $api_key = getAPIKey($vendeur);
        $control = get_payment_control($api_key, $transaction, $montant, $vendeur, $retour);

        return [
            'action_url' => 'https://www.plateforme-smc.fr/cybank/index.php',
            'transaction' => $transaction,
            'montant' => $montant,
            'vendeur' => $vendeur,
            'retour' => $retour,
            'control' => $control,
        ];
    }

    if(isset($_GET['payment_return']) && $_GET['payment_return'] == '1') {
        if(isset($_GET['status']) && $_GET['status'] == 'accepted') {
            save_order();
            empty_basket();

            header('Location: /');
            exit();
        } else {
            $_SESSION['error'] = urlencode('Le paiement a échoué. Veuillez réessayer.');
            header('Location: /basket.php');
            exit();
        }
    }
?>