<?php
    if(!function_exists('is_logged') || !function_exists('get_account_by_id')) require_once(__DIR__ . '/account.php');
    if(!function_exists('get_basket_data') || !function_exists('get_basket_total') || !function_exists('generate_promo_code') || !function_exists('delete_promo_code')) require_once(__DIR__ . '/basket.php');
    if(!function_exists('get_product_by_id')) require_once(__DIR__ . '/products.php');
    
    $orders_file_path = __DIR__ . '/../data/orders.json';
    if(!file_exists($orders_file_path) || filesize($orders_file_path) === 0) file_put_contents($orders_file_path, json_encode([], JSON_PRETTY_PRINT));

    $order_status = [
        'unpaid' => 'Non payée',
        'paid' => 'Payée',
        'waiting' => 'En attente de préparation',
        'preparing' => 'En préparation',
        'ready' => 'En attente {de livraison|de récupération}',
        'delivered' => '{livré|récupéré}',
        'cancelled' => 'Annulée'
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
    function get_order_by_id($order_id) {
        $orders = get_orders_data();

        foreach ($orders as $order) {
            if ($order["id_order"] == $order_id && $order["id_client"] == $_SESSION['uuid']) return $order;
        }

        return null;
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

        $pickup_datetime = $_SESSION['pickup_datetime'] ?? null;
        if(empty($_SESSION['pickup_datetime'])) {
            return false;
        }

        $new_orders = [
            'id_order' => strtolower(uniqid()),
            'id_client' => $_SESSION['uuid'],
            'address' => $address,
            'details' => $basket_items,
            'total' => number_format($total, 2, '.', ''),
            'status' => 'paid',
            'date_heure' => date('Y-m-d H:i:s'),
            'pickup_datetime' => $pickup_datetime
        ];

        $orders_data = get_orders_data();
        array_push($orders_data, $new_orders);
        file_put_contents($orders_file_path, json_encode($orders_data, JSON_PRETTY_PRINT));
        
        delete_promo_code($_SESSION['uuid'], $_SESSION['promo_code']);
    }

    function format_details($details) {
        $formatted_details = [];

        foreach ($details as $product) {
            if (get_product_by_id($product['id'])) $formatted_details[$product['id']] = $product['quantity'];
        }

        return $formatted_details;
    }
    function calcul_total($order) {
        $total = 0;
        foreach ($order['details'] as $product_id => $quantity) {
            $product = get_product_by_id($product_id);
            if ($product) $total += $product['price'] * $quantity;
        }
        return number_format($total, 2, '.', '');
    }
    function update_order($order_id, $new_details) {
        global $orders_file_path;

        $orders = get_orders_data();
        $is_updated = false;

        foreach ($orders as &$order) {
            if ($order['id_order'] === $order_id && $order['id_client'] === $_SESSION['uuid']) {
                if(!in_array($order['status'], ['preparing', 'ready', 'delivered', 'cancelled']) && !empty($new_details) && $new_details !== $order['details']) {
                    $order['details'] = format_details($new_details);

                    $old_total = $order['total'];
                    $new_total = calcul_total($order);
                    $total_diff = $new_total - $old_total;

                    if($total_diff <= 0) generate_promo_code($_SESSION['uuid'], $total_diff * -1);
                    else if($total_diff > 0) $order['old_total'] = $old_total;

                    $order['total'] = $new_total;
                    if ($total_diff > 0) $order['status'] = 'unpaid';
                    $is_updated = true;
                }
                break;
            }
        }

        if ($is_updated) file_put_contents($orders_file_path, json_encode($orders, JSON_PRETTY_PRINT));
        return $is_updated;
    }

    function update_order_status($order_id, $new_status) {
        global $orders_file_path;

        $orders = get_orders_data();
        $is_updated = false;

        if($new_status === 'cancelled' && in_array($orders['status'], ['preparing', 'ready', 'delivered', 'cancelled']) && !get_access(["admin"], false)) {
            return json_encode([
                "type" => "error",
                "title" => "Mise à jour échouée",
                "message" => "Vous ne pouvez pas annuler cette commande",
            ]);
        }

        foreach ($orders as &$order) {
            if ($order['id_order'] === $order_id) {
                if($order['status'] !== 'delivered' && $order['status'] !== 'cancelled') {
                    $order['status'] = $new_status;
                    $is_updated = true;
                }
                break;
            }
        }

        error_log(print_r($orders, true));

        if (!$is_updated) {
            return json_encode([
                "type" => "error",
                "title" => "Mise à jour échouée",
                "message" => "La mise à jour du statut de la commande a échoué. Assurez-vous que la commande n'est pas déjà livrée ou annulée.",
                "status" => $order['status']
            ]);
        }
        if ($is_updated) {
            file_put_contents($orders_file_path, json_encode($orders, JSON_PRETTY_PRINT));
            return json_encode([
                "type" => "success",
                "title" => "Statut mis à jour",
                "message" => "Le statut de la commande a été mis à jour avec succès.",
                "status" => $order['status']
            ]);
        }
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
    if(isset($_POST['action']) && $_POST['action'] === 'get_order_details' && isset($_POST['order_id'])) {
        $type = 'error';
        $title = 'Commande introuvable';
        $message = 'La commande demandée est introuvable';
        $order = null;

        if($order = get_order_by_id($_POST['order_id'])) {
            $type = 'success';
            $title = 'Détails de la commande';
            $message = "La commande a été trouvée avec succès";
        }

        echo json_encode([
            "type" => $type,
            "title" => $title,
            "message" => $message,
            "order_data" => $order
        ]);
    } else if(isset($_POST['action']) && $_POST['action'] === 'update_order' && isset($_POST['order_id']) && isset($_POST['value'])) {
        $type = "error";
        $title = "Modification échouée";
        $message = "La modification de la commande a échoué. Assurez-vous que la commande n'est pas déjà livrée ou annulée.";

        $new_details = json_decode($_POST['value'], true)['new_details'];
        if(update_order($_POST['order_id'], $new_details)) {
            $type = "success";
            $title = "Commande modifiée";
            $message = "La commande a été modifiée avec succès.";
        }

        echo json_encode([
            "type" => $type,
            "title" => $title,
            "message" => $message
        ]);
    }
    else if(isset($_POST['action']) && $_POST['action'] === 'update_status' && isset($_POST['order_id']) && isset($_POST['value'])) echo update_order_status($_POST['order_id'], $_POST['value']);
    else if(isset($_POST['action']) && $_POST['action'] === 'cancel_order' && isset($_POST['order_id'])) echo update_order_status($_POST['order_id'], 'cancelled');
?>