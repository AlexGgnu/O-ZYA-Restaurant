<?php
    if(!function_exists('is_logged') || !function_exists('get_access')) require_once(__DIR__ . '/../api/account.php');
    if(!function_exists('get_orders_by_user') || !function_exists('get_orders_data') || !isset($order_status)) require_once(__DIR__ . '/../api/order.php');
    if(!function_exists('get_all_delivery_people') || !function_exists('get_occupied_delivery_people')) require_once(__DIR__ . '/../api/delivery.php');
    if(!function_exists('get_notations_data'))   require_once(__DIR__ . '/../api/notation.php');
    if(!function_exists('generate_payment_params'))   require_once(__DIR__ . '/../api/payment.php');

    // MARK: - Fetch orders data
    if(!isset($current_page)) $current_page = strtolower(basename($_SERVER['PHP_SELF'], ".php"));

    if(is_logged() && $current_page === 'profile') $orders = get_orders_by_user($account_data['id'] ?? $_SESSION['uuid']);
    else if (is_logged() && get_access(["employee", "admin"], false) && $current_page === 'orders') $orders = get_orders_data();
    else $orders = [];

    // MARK: - Helper functions
    function format_order_details($details) {
        $result = '';
        foreach ($details as $id => $quantity) {
            $product = get_product_by_id($id);
            if ($product) $result .= '- ' . $product['name'] . ' x' . $quantity . ' (' . number_format($product['price'], 2, '.', '') . '€)<br>';
        }

        return $result;
    }

    function is_order_noted($order_id) {
        $notations = get_notations_data();

        foreach ($notations as $note) {
            if (isset($note['order_id']) && $note['order_id'] === $order_id) {
                return true;
            }
        }

        return false;
    }

    function format_order_status($order) {
        global $current_page;
        global $order_status;

        $status = $order['status'];
        $address = $order['address'];

        if(($current_page ==='profile' && array_key_exists($status, $order_status)) || $status === 'delivered' || $status === 'cancelled') {
            switch ($status) {
                case 'unpaid':
                    return '<span class="order__status" data-status="unpaid">' . $order_status['unpaid'] . '</span>';
                case 'paid':
                    return '<span class="order__status" data-status="paid">' . $order_status['paid'] . '</span>';
                case 'waiting':
                    return '<span class="order__status" data-status="waiting">' . $order_status['waiting'] . '</span>';
                case 'preparing':
                    return '<span class="order__status" data-status="preparing">' . $order_status['preparing'] . '</span>';
                case 'ready':
                    return '<span class="order__status" data-status="ready">' . (!empty($order['address']) ? preg_replace('/\{.*?\}/', 'de livraison', $order_status['ready']) : preg_replace('/\{.*?\}/', 'de récupération', $order_status['ready'])) . '</span>';
                case 'delivered':
                    return '<span class="order__status" data-status="delivered">' . (!empty($order['address']) ? preg_replace('/\{.*?\}/', 'Livré', $order_status['delivered']) : preg_replace('/\{.*?\}/', 'Récupéré', $order_status['delivered'])) . '</span>';
                case 'cancelled':
                    return '<span class="order__status" data-status="cancelled">' . $order_status['cancelled'] . '</span>';
                default:
                    return '<span class="order__status" data-status="unknown">' . htmlspecialchars($status) . '</span>';
            }
        } else if ($current_page === 'orders' && array_key_exists($status, $order_status)) {
            if($status === 'paid') $status = 'waiting';

            $result = '<select name="status" class="order__status" data-status="' . htmlspecialchars($status) . '">';
            foreach ($order_status as $key => $status_option) {
                $isSelected = $status === $key;

                if($key === 'unpaid' && $status !== 'unpaid') continue;
                else if($key === 'paid' && $status !== 'paid') continue;
                
                if (!empty($order['address']) && $key === 'delivered' && $status !== 'delivered') $isDisabled = true;
                else if (!empty($order['address']) && $key === 'cancelled' && $status === 'delivered') $isDisabled = true;
                else $isDisabled = false;

                if($key === 'ready') $status_option = !empty($order['address']) ? preg_replace('/\{.*?\}/', 'de livraison', $status_option) : preg_replace('/\{.*?\}/', 'de récupération', $status_option);
                else if($key === 'delivered') $status_option = !empty($order['address']) ? preg_replace('/\{.*?\}/', 'Livré', $status_option) : preg_replace('/\{.*?\}/', 'Récupéré', $status_option);

                $result .= '<option name="status" value="' . htmlspecialchars($key) . '"' . ($isSelected ? ' selected' : '') . ($isDisabled ? ' disabled' : '') . '>' . htmlspecialchars($status_option) . '</option>';
            }
            $result .= '</select>';

            return $result;
        } else {
            return '<span class="order__status" data-status="' . htmlspecialchars($status) . '">' . htmlspecialchars($status) . '</span>';
        }
    }

    function format_delivery_person($order) {
        if(!empty($order['address'])) {
            $delivery_person = get_all_delivery_people();
            $occupied_person = get_occupied_delivery_people();

            $result = '<select name="delivery_person" class="delivery__select" data-person-id="' . htmlspecialchars($order['delivery_person_id'] ?? '') . '"' . ($order['status'] !== 'ready' ? ' disabled' : '') . '>';
            if(!isset($order['delivery_person_id'])) $result .= '<option value="">Selectionner un livreur</option>';
            foreach ($delivery_person as $person) {
                $is_selected = isset($order['delivery_person_id']) && $order['delivery_person_id'] === $person['id'];
                $is_disabled = in_array($person, $occupied_person) && !$is_selected;
                $result .= '<option value="' . htmlspecialchars($person['id']) . '"' . ($is_selected ? ' selected' : '') . ($is_disabled ? ' disabled' : '') . '>' . htmlspecialchars($person['lastname']) . ' ' . htmlspecialchars($person['firstname']) . '</option>';
            }
            $result .= '</select>';
        } else {
            $result = '-';
        }

        return $result;
    }

    function get_order_action_button($order) {
        global $current_page;
        $order_id = $order['id_order'];

        switch ($order['status']) {
            case 'unpaid':
                $params = get_payment_params($order, $current_page);

                return '
                    <form method="POST" action="' . htmlspecialchars($params['action_url']) . '">
                        <input type=\'hidden\' name=\'transaction\' value=\'' . htmlspecialchars($params['transaction']) . '\'>
                        <input type=\'hidden\' name=\'montant\' value=\'' . htmlspecialchars($params['montant']) . '\'>
                        <input type=\'hidden\' name=\'vendeur\' value=\'' . htmlspecialchars($params['vendeur']) . '\'>
                        <input type=\'hidden\' name=\'retour\' value=\'' . htmlspecialchars($params['retour']) . '\'>
                        <input type=\'hidden\' name=\'control\' value=\'' . htmlspecialchars($params['control']) . '\'>

                        <button class="btn btn-primary" type=\'submit\'>Payer</button>
                    </form>
                ';
            case 'paid':
            case 'waiting':
                return '
                    <button class="modify__button btn btn-primary">Modifier</button>
                    <button class="cancel__button btn btn-primary">Annuler</button>
                ';
            case 'delivered':
                if (is_order_noted($order_id)) return '<span class="order__status">Déjà noté</span>';
                else return '<a class="btn btn-primary" href="/notation.php?order_id=' . htmlspecialchars($order_id) . '">Noter la commande</a>';
            default:
                return '-';
        }
    }

    // MARK: - Table generation
    function generate_table_header() {
        global $current_page;
        global $account_data;

        return '
            <thead>
                <tr>
                    <th>ID de commande</th>
                    <th>Date</th>'
                    .
                    ($current_page === 'orders' ? '<th>Client</th>' : '')
                    .
                    ($current_page === 'orders' ? '<th>Point de réception</th>' : '')
                    .
                    '<th>Détails</th>
                    <th class="col__centered">Total (€)</th>
                    <th class="col__centered">Statut</th>'
                    .
                    ($current_page === 'orders' ? '<th class="col__centered">Livreur</th>' : '')
                    .
                    ($current_page === 'profile' && $_SESSION['uuid'] === $account_data['id'] ? '<th class="col__centered">Action</th>' : '')
                    .
                '</tr>
            </thead>
        ';
    }
    function get_table_row($order) {
        global $current_page;
        global $account_data;

        return '
            <tr class="order__row" data-order-id="' . htmlspecialchars($order['id_order']) . '">
                <td>' . htmlspecialchars($order['id_order']) . '</td>
                <td>' . htmlspecialchars($order['date_heure']) . '</td>'
                .
                ($current_page === 'orders' ? '<td>' . htmlspecialchars(get_account_by_id($order['id_client'])['lastname']) . ' ' . htmlspecialchars(get_account_by_id($order['id_client'])['firstname']) . '</td>' : '')
                .
                ($current_page === 'orders' ? '<td>' . (!empty($order['address']) ? htmlspecialchars($order['address']) : 'À emporter') . '</td>' : '')
                .
                '<td>' . format_order_details($order['details']) . '</td>
                <td class="col__centered">' . number_format($order['total'], 2, '.', '') . '€</td>
                <td class="status__cell col__centered">' . format_order_status($order) . '</td>'
                .
                ($current_page === 'orders' ? '<td class="delivery__cell col__centered">' . format_delivery_person($order) . '</td>' : '')
                .
                ($current_page === 'profile' && $account_data['id'] === $_SESSION['uuid'] ? '<td class="action__cell col__centered">' . get_order_action_button($order) . '</td>' : '')
                .
            '</tr>
        ';
    }

    // MARK: - Display orders table
    if($current_page === 'profile' || $current_page === 'orders') {
        if(empty($orders)) {
            echo '<p>Vous n\'avez pas encore passé de commande</p>';
        } else {
            echo '<table class="orders-table">' . generate_table_header() . '<tbody>';
            foreach ($orders as $order) echo get_table_row($order);
            echo ' </tbody></table>';
        }        
    }
?>