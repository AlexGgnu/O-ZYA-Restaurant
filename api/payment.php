<?php
    if(!function_exists('get_basket_total')) require_once(__DIR__ . '/basket.php');
    if(!function_exists('getAPIKey')) require_once(__DIR__ . '/getapikey.php');
    if(!function_exists('save_order') || !function_exists('update_order_status')) require_once(__DIR__ . '/order.php');

    function get_payment_return_url() {
        $protocole = 'http';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') $protocole = 'https';

        return  $protocole . '://' . $_SERVER['HTTP_HOST'] . '/api/payment.php?payment_return=1';
    }

    function generate_transaction_id() {
        return substr(strtoupper(uniqid('TRX')), 0, 24);
    }

    function get_payment_amount($order = null) {
        if ($order) {
            if ($order['old_total'] && $order['old_total'] !== $order['total'] && !empty($order['old_total']) && $order['total'] > $order['old_total']) $total = $order['total'] - $order['old_total'];
            else $total = $order['total'];

            return number_format($total, 2, '.', '');
        }
        return number_format(get_basket_total(), 2, '.', '');
    }

    function get_payment_control($api_key, $transaction, $montant, $vendeur, $retour) {
        return md5($api_key . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $retour . '#');
    }

    function get_payment_params($order = null, $redirection = null) {
        $vendeur = 'MI-3_C';
        $transaction = generate_transaction_id();
        $montant = get_payment_amount($order);
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

    // MARK: - API endpoints
    if($_SERVER['SCRIPT_FILENAME'] === __FILE__) {
        if(isset($_GET['payment_return']) && $_GET['payment_return'] == '1') {
            if(isset($_GET['redirection']) && !empty($_GET['redirection'])) $redirection = '/' . htmlspecialchars($_GET['redirection']) . '.php';
            else $redirection = null;

            if(isset($_GET['status']) && $_GET['status'] == 'accepted') {
                if(isset($_GET['order_id'])) update_order_status(htmlspecialchars($_GET['order_id']), 'paid');
                else if(!empty(get_basket_data()['items'])) save_order();

                empty_basket();

                $redirection = $redirection !== null ? $redirection : '/';
                header('Location: ' . $redirection);
                exit();
            } else {
                $_SESSION['error'] = urlencode('Le payment a échoué. Veuillez réessayer.');

                $redirection = $redirection !== null ? $redirection : '/basket.php';
                header('Location: ' . $redirection);
                exit();
            }
        }
    }
?>