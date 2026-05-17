<?php
    if(!function_exists('get_account_by_role')) require __DIR__ . '/account.php';
    if(!function_exists('get_orders_data') || !isset($orders_file_path)) require __DIR__ . '/order.php';

    // MARK: - Delivery people management
    function get_all_delivery_people() {
        return get_account_by_role('delivery');
    }

    function get_occupied_delivery_people() {
        $delivery_people = get_account_by_role('delivery');

        // NOTE: get occupied delivery people
        $occupied_delivery_people = [];
        foreach (get_orders_data() as $order) {
            $delivery_person_id = $order['delivery_person_id'] ?? null;
            if ($delivery_person_id !== null && $order['status'] !== 'delivered' && $order['status'] !== 'cancelled') {
                if($delivery_person = get_account_by_id($delivery_person_id)) {
                    $occupied_delivery_people[] = $delivery_person ?? null;
                }
            }
        }

        return $occupied_delivery_people;
    }

    function assign_delivery_person($order_id, $delivery_person_id) {
        global $orders_file_path;

        $orders = get_orders_data();
        $is_updated = false;

        foreach ($orders as &$order) {
            if ($order['id_order'] === $order_id && $delivery_account = get_account_by_id($delivery_person_id)) {
                error_log(print_r(get_account_by_id($delivery_person_id), true));
                if($delivery_account['state'] === 'unblocked' && $delivery_account['role'] === 'delivery' && $order['delivery_person_id'] !== $delivery_person_id) {
                    $order['delivery_person_id'] = $delivery_person_id;
                    $is_updated = true;
                }
                break;
            }
        }

        if ($is_updated) file_put_contents($orders_file_path, json_encode($orders, JSON_PRETTY_PRINT));
        return $order['delivery_person_id'] ?? null;
    }

    // MARK: - API Endpoints
    if (isset($_POST['action']) && $_POST['action'] === 'assign_delivery_person') echo json_encode(assign_delivery_person($_POST['value'], $_POST['delivery_id']));