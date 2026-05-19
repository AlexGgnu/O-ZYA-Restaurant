<?php
    if(!function_exists('get_basket_total')) require_once(__DIR__ . '/basket.php');
    if(!function_exists('getAPIKey')) require_once(__DIR__ . '/getapikey.php');
    if(!function_exists('save_order') || !function_exists('update_order_status')) require_once(__DIR__ . '/order.php');

    function get_payment_return_url() {
        $protocole = 'http';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') $protocole = 'https';

        return  $protocole . '://' . $_SERVER['HTTP_HOST'] . '/api/paiement.php?payment_return=1';
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

    function get_paiement_params($order = null, $redirection = null) {
        $vendeur = 'MI-3_C';
        $transaction = generate_transaction_id();
        $montant = $order ? $order['total'] : get_payment_amount();
        $retour = get_payment_return_url() . ($redirection ? '&redirection=' . urlencode($redirection) : '') . ($order ? '&order_id=' . urlencode($order['id_order']) : '');
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
        if(isset($_GET['redirection'])) $redirection = '/' . htmlspecialchars($_GET['redirection']) . '.php';
        else $redirection = null;

        if(isset($_GET['status']) && $_GET['status'] == 'accepted') {
            if(isset($_GET['order_id'])) update_order_status(htmlspecialchars($_GET['order_id']), 'paid');
            else save_order();

            empty_basket();

            header('Location: ' . $redirection ?? '/');
            exit();
        } else {
            $_SESSION['error'] = urlencode('Le paiement a échoué. Veuillez réessayer.');

            header('Location: ' . $redirection ?? '/basket.php');
            exit();
        }
    }
?>