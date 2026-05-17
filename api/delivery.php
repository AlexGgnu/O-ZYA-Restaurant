<?php
    if(!function_exists('get_account_by_role')) require __DIR__ . '/account.php';
    if(!function_exists('get_orders_data') || !isset($orders_file_path)) require __DIR__ . '/order.php';

    // MARK: - Delivery people management
    function get_free_delivery_people() {
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

        // NOTE: Remove in occupied delivery people in delivery people list
        foreach ($occupied_delivery_people as $occupied_person) {
            foreach ($delivery_people as $key => $person) {
                if ($person['id'] === $occupied_person['id']) {
                    unset($delivery_people[$key]);
                    break;
                }
            }
        }

        // NOTE: Format free delivery people for select options
        $free_delivery_people = [];
        foreach ($delivery_people as $person) $free_delivery_people[$person['id']] = $person['lastname'] . ' ' . $person['firstname'];
        
        return $free_delivery_people;
    }

    function get_current_delivery($order_id) {
        $orders = get_orders_data();

        foreach ($orders as $order) {
            if ($order['id_order'] === $order_id) {
                error_log("Order found: " . print_r($order, true));
                if (isset($order['delivery_person_id'])) {
                    $delivery_person = get_account_by_id($order['delivery_person_id']);
                    if ($delivery_person) return ['id' => $delivery_person['id'], 'name' => $delivery_person['lastname'] . ' ' . $delivery_person['firstname']];
                }
                break;
            }
        }

        return ['id' => null, 'name' => null];
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
        return $is_updated;
    }

    // MARK: - API Endpoints
    if(isset($_POST['action']) && $_POST['action'] === 'get_delivery_people') echo json_encode(get_free_delivery_people());
    else if (isset($_POST['action']) && $_POST['action'] === 'get_current_delivery') echo json_encode(get_current_delivery($_POST['value']));
    else if (isset($_POST['action']) && $_POST['action'] === 'assign_delivery_person') echo json_encode(assign_delivery_person($_POST['value'], $_POST['delivery_id']));