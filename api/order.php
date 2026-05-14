<?php
    if(!function_exists('is_logged') || !function_exists('get_account_by_id')) require_once(__DIR__ . '/account.php');
    if(!function_exists('get_basket_data') || !function_exists('get_basket_total')) require_once(__DIR__ . '/basket.php');
    if(!function_exists('get_product_by_id')) require_once(__DIR__ . '/products.php');
    
    $orders_file_path = __DIR__ . '/../data/orders.json';
    if(!file_exists($orders_file_path)) file_put_contents($orders_file_path, json_encode([], JSON_PRETTY_PRINT));

    function get_orders_data() {
        global $orders_file_path;
        $datas = file_get_contents($orders_file_path);
        return json_decode($datas, true);
    }

    function save_order($total = null) {
        global $orders_file_path;
        if (!is_logged()) return false;

        $basket_items = get_basket_data()['items'];
        if(empty($basket_items)) return false;
        
        if($total === null) $total = get_basket_total();

        $delivery_type = isset($_SESSION['delivery_type']) ? $_SESSION['delivery_type'] : 'takeaway';
        
        if ($delivery_type === 'delivery') {
            $account = get_account_by_id($_SESSION['uuid']);
            if ($account != null && isset($account['address']) && !empty($account['address'])) $adresse = $account['address'];
        } else $adresse = '';

        $new_orders = [
            'id_order' => strtolower(uniqid()),
            'id_client' => $_SESSION['uuid'],
            'adresse' => $adresse,
            'details' => $basket_items,
            'total' => number_format($total, 2, '.', ''),
            'statut' => 'payé',
            'date_heure' => date('Y-m-d H:i:s')
        ];

        $orders_data = get_orders_data();
        array_push($orders_data, $new_orders);
        file_put_contents($orders_file_path, json_encode($orders_data, JSON_PRETTY_PRINT));
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
?>