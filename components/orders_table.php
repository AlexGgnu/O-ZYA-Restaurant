<?php
    if(!function_exists('is_logged') || !function_exists('get_access')) require_once(__DIR__ . '/../api/account.php');
    if(!function_exists('get_orders_by_user') || !function_exists('get_orders_data')) require_once(__DIR__ . '/../api/order.php');

    // MARK: - Fetch orders data
    if(!isset($current_page)) $current_page = strtolower(basename($_SERVER['PHP_SELF'], ".php"));

    if(is_logged() && $current_page === 'profile') $orders = get_orders_by_user($_SESSION['uuid']);
    else if (is_logged() && get_access("admin", true) && $current_page === 'orders') $orders = get_orders_by_user($_SESSION['uuid']);
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
    function format_order_status($order) {
        $status = $order['statut'];
        $address = $order['adresse'];

        switch ($status) {
            case 'unpaid':
                return '<span class="order__status" data-status="unpaid">Non payé</span>';
            case 'paid':
                return '<span class="order__status" data-status="paid">Payé</span>';
            case 'waiting':
                return '<span class="order__status" data-status="waiting">En attente de préparation</span>';
            case 'preparing':
                return '<span class="order__status" data-status="preparing">En préparation</span>';
            case 'ready':
                return '<span class="order__status" data-status="ready">En attente ' . (empty($address) ? 'du livreur' : 'de récupération') . '</span>';
            case 'delivered':
                return '<span class="order__status" data-status="delivered">Livré</span>';
            case 'cancelled':
                return '<span class="order__status" data-status="cancelled">Annulé</span>';
            default:
                return '<span class="order__status" data-status="unknown">' . htmlspecialchars($status) . '</span>';
        }
    }

    // MARK: - Table generation
    function generate_table_header() {
        global $current_page;

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
                '</tr>
            </thead>
        ';
    }
    function get_table_row($order) {
        global $current_page;

        return '
            <tr>
                <td>' . htmlspecialchars($order['id_order']) . '</td>
                <td>' . htmlspecialchars($order['date_heure']) . '</td>'
                .
                ($current_page === 'orders' ? '<td>' . htmlspecialchars(get_account_by_id($order['id_client'])['lastname']) . ' ' . htmlspecialchars(get_account_by_id($order['id_client'])['firstname']) . '</td>' : '')
                .
                ($current_page === 'orders' ? '<td>' . htmlspecialchars($order['adresse'] ?? 'À emporter') . '</td>' : '')
                .
                '<td>' . format_order_details($order['details']) . '</td>
                <td class="col__centered">' . number_format($order['total'], 2, '.', '') . '€</td>
                <td class="col__centered">' . format_order_status($order) . '</td>'
                .
                ($current_page === 'orders' ? '<td class="col__centered">' . htmlspecialchars($order['livreur'] ?? '-') . '</td>' : '')
                .
            '</tr>
        ';
    }
    
    // MARK: - Display orders table
    if(empty($orders)) {
        echo '<p>Vous n\'avez pas encore passé de commande</p>';
    } else {
        echo '<table class="orders-table">' . generate_table_header() . '<tbody>';
        foreach ($orders as $order) echo get_table_row($order);
        echo ' </tbody></table>';
    }
?>