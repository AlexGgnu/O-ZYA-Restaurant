<?php
    if(!function_exists('is_logged') || !function_exists('get_account_by_id')) require_once(__DIR__ . '/account.php');
    if(!function_exists('get_basket_data') || !function_exists('get_basket_total')) require_once(__DIR__ . '/basket.php');
    if(!function_exists('get_product_by_id')) require_once(__DIR__ . '/products.php');
    
    $orders_file_path = __DIR__ . '/../data/orders.json';
    if(!file_exists($orders_file_path) || filesize($orders_file_path) === 0) file_put_contents($orders_file_path, json_encode([], JSON_PRETTY_PRINT));

    $order_status = [
        'unpaid' => 'Non payé',
        'paid' => 'Payé',
        'waiting' => 'En attente de préparation',
        'preparing' => 'En préparation',
        'ready' => 'En attente {de livraison|de récupération}',
        'delivered' => '{livré|récupéré}',
        'cancelled' => 'Annulé'
    ];

    // MARK: - Data handling
    function get_orders_data() {
        global $orders_file_path;
        $datas = file_get_contents($orders_file_path);
        return json_decode($datas, true);
    }
    function get_orders_by_user($user_id) {
        $orders = get_orders_data();
        $result = [];

        foreach ($orders as $order) {
            if ($order["id_client"] == $user_id) {
                $result[] = $order;
            }
        }

        return $result;
    }

    // MARK: - Orders management
    function save_order($total = null) {
        global $orders_file_path;
        if (!is_logged()) return false;

        $basket_items = [];
        foreach (get_basket_data()['items'] as $item) {
            $product = get_product_by_id($item['id']);
            if ($product) $basket_items[$item['id']] = $item['quantity'];
        }
        
        if($total === null) $total = get_basket_total();

        $delivery_type = isset($_SESSION['delivery_type']) ? $_SESSION['delivery_type'] : 'takeaway';
        
        if ($delivery_type === 'delivery') {
            $account = get_account_by_id($_SESSION['uuid']);
            if ($account != null && isset($account['address']) && !empty($account['address'])) $address = $account['address'];
        } else $address = '';

        $new_orders = [
            'id_order' => strtolower(uniqid()),
            'id_client' => $_SESSION['uuid'],
            'address' => $address,
            'details' => $basket_items,
            'total' => number_format($total, 2, '.', ''),
            'status' => 'paid',
            'date_heure' => date('Y-m-d H:i:s')
        ];

        $orders_data = get_orders_data();
        array_push($orders_data, $new_orders);
        file_put_contents($orders_file_path, json_encode($orders_data, JSON_PRETTY_PRINT));
    }

    function update_order_status($order_id, $new_status) {
        global $orders_file_path;

        $orders = get_orders_data();
        $is_updated = false;

        foreach ($orders as &$order) {
            if ($order['id_order'] === $order_id) {
                if($order['status'] !== 'delivered' && $order['status'] !== 'cancelled') {
                    $order['status'] = $new_status;
                    $is_updated = true;
                }
                break;
            }
        }

        if ($is_updated) file_put_contents($orders_file_path, json_encode($orders, JSON_PRETTY_PRINT));
        return json_encode(['success' => true, 'status' => $order['status']]);
    }

    function get_assigned_order($delivery_person_id) {
        $orders = get_orders_data();

        foreach ($orders as $order) {
            if (isset($order['delivery_person_id']) && $order['delivery_person_id'] === $delivery_person_id && $order['status'] !== 'delivered' && $order['status'] !== 'cancelled') {
                return $order;
            }
        }
        return null;
    }

    // MARK: - API Endpoint handling
    if($_POST['action'] === 'update_status' && isset($_POST['order_id']) && isset($_POST['value'])) echo update_order_status($_POST['order_id'], $_POST['value']);
?>