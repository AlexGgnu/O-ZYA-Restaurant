<?php
    require_once(__DIR__ . '/function_basket.php');
    require_once(__DIR__ . '/getapikey.php');

    function get_payment_error_message() {
        if (!isset($_GET['payment_error']) || $_GET['payment_error'] == '') return '';
        return htmlspecialchars(urldecode($_GET['payment_error']));
    }

    function get_payment_return_url() {
        $protocol = 'http';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') $protocol = 'https';

        return $protocol . '://' . $_SERVER['HTTP_HOST'] . '/retour_paiement.php';
    }

    function get_promo_code_from_post() {
        if (!isset($_POST['code_promo'])) return '';
        return trim($_POST['code_promo']);
    }

    function generate_transaction_id() {
        return substr(strtoupper(uniqid('TRX')), 0, 24);
    }

    function get_payment_amount($promo_code = '') {
        $totaux = get_basket_totals($promo_code);
        return number_format((float) $totaux['total'], 2, '.', '');
    }

    function build_payment_control($api_key, $transaction, $montant, $vendeur, $retour) {
        return md5($api_key . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $retour . '#');
    }

    function get_paiment_params($promo_code = '') {
        $vendeur = 'MI-3_C';
        $transaction = generate_transaction_id();
        $montant = get_payment_amount($promo_code);
        $retour = get_payment_return_url();
        $api_key = getAPIKey($vendeur);

        return [
            'action_url' => 'https://www.plateforme-smc.fr/cybank/index.php',
            'transaction' => $transaction,
            'montant' => $montant,
            'vendeur' => $vendeur,
            'retour' => $retour,
            'control' => build_payment_control($api_key, $transaction, $montant, $vendeur, $retour),
        ];
    }

    function get_payment_status_from_query() {
        $status_keys = ['status', 'result', 'etat', 'payment', 'paiement', 'session'];

        foreach ($status_keys as $key) {
            if (isset($_GET[$key]) && $_GET[$key] !== '') {
                return strtolower(trim((string) $_GET[$key]));
            }
        }

        return '';
    }

    function is_payment_success($status) {
        $success_values = ['ok', 'success', 'paye', 'paid', '1', 'true', 'accepted', 'accepte', 'accepté', 's'];
        return in_array($status, $success_values, true);
    }

    function handle_payment_return() {
        $status = get_payment_status_from_query();

        if (is_payment_success($status)) {
            empty_basket();
            header('Location: /');
            exit();
        }

        header('Location: /basket.php?payment_error=' . urlencode('Le paiement a echoue. Veuillez reessayer.'));
        exit();
    }
?>