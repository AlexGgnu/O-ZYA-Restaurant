<?php
    if(!function_exists('is_logged') || !function_exists('get_account_by_id')) require_once(__DIR__ . '/account.php');
    require_once(__DIR__ . '/products.php');

    if(session_status() === PHP_SESSION_NONE) session_start();

    $promotions_file_path = __DIR__ . '/../data/promotions.json';
    if(!file_exists($promotions_file_path)) file_put_contents($promotions_file_path, json_encode(['public' => []]));

    function get_basket() {
        if(!isset($_SESSION['basket']) || !is_array($_SESSION['basket'])) return $_SESSION['basket'] = [];
        return $_SESSION['basket'];
    }

    // MARK: - Delivery type management functions
    function update_delivery_type($delivery_type) {
        $valid_delivery_types = ['takeaway', 'delivery'];

        if(in_array($delivery_type, $valid_delivery_types)) $_SESSION['delivery_type'] = $delivery_type;
        else $_SESSION['delivery_type'] = null;

        return [
            "type" => "success",
            "title" => "Mode de livraison mis à jour",
            "message" => "Votre mode de livraison à été mis à jour avec succès"
        ];
    }

    // MARK: - Promo code management functions
    function get_promo_code_data() {
        global $promotions_file_path;
        $promo_codes = json_decode(file_get_contents($promotions_file_path), true);
        return $promo_codes;
    }
    function get_promo_by_account_id($account_id) {
        $promo_codes = get_promo_code_data();
        return $promo_codes[$account_id] ?? [];
    }
    function get_promo_by_code($code) {
        $promo_codes = get_promo_code_data();

        if(is_logged()) {
            $user_uuid = $_SESSION['uuid'];

            if(isset($promo_codes[$user_uuid][$code])) $reduction = $promo_codes[$user_uuid][$code];
            else if(isset($promo_codes['public'][$code])) $reduction = $promo_codes['public'][$code];
        } else {
            if(isset($promo_codes['public'][$code])) $reduction = $promo_codes['public'][$code];
        }

        $_SESSION['promo_code'] = $code;
        return $reduction ?? null;
    }
    function generate_promo_code($account_id, $reduction) {
        global $promotions_file_path;
        $promo_codes = get_promo_code_data();
        $is_created = false;

        if(get_account_by_id($account_id)) {
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
            $promo_codes[$account_id][$code] = number_format($reduction, 2, '.', '');
            $is_created = true;
        }

        if($is_created) file_put_contents($promotions_file_path, json_encode($promo_codes, JSON_PRETTY_PRINT));
        return $is_created ? $code : null;
    }
    function delete_promo_code($account_id, $code) {
        global $promotions_file_path;
        $promo_codes = get_promo_code_data();
        $is_deleted = false;

        if(get_account_by_id($account_id) && isset($promo_codes[$account_id][$code])) {
            unset($promo_codes[$account_id][$code]);
            $is_deleted = true;
        }

        if($is_deleted) file_put_contents($promotions_file_path, json_encode($promo_codes, JSON_PRETTY_PRINT));
        return $is_deleted;
    }

    // MARK: - Basket management functions
    function add_to_basket($product_id) {
        if(!get_product_by_id($product_id)) return null;
        
        $basket = get_basket();
        
        if(isset($basket[$product_id])) $basket[$product_id]++;
        else $basket[$product_id] = 1;

        $_SESSION['basket'] = $basket;
        return true;
    }

    function get_basket_data() {
        $basket = get_basket();
        $basketItemsData = [];

        foreach ($basket as $product_id => $quantity) {
            $productData = get_product_by_id($product_id);
            if ($productData) {
                $productData['quantity'] = $quantity;
                $basketItemsData[] = $productData;
            }
        }

        return [
            "items" => $basketItemsData,
            "delivery_type" => $_SESSION['delivery_type'] ?? null,
            "pickup_datetime" => $_SESSION['pickup_datetime'] ?? null,
            "promo_code" => isset($_SESSION['promo_code']) && !empty($_SESSION['promo_code']) ? get_promo_by_code($_SESSION['promo_code']) : null
        ];
    }

    function get_basket_total() {
        $basket = get_basket();
        $total = 0;

        foreach ($basket as $product_id => $quantity) {
            $product = get_product_by_id($product_id);
            if ($product) $total += $product['price'] * $quantity;
        }

        if(isset($_SESSION['promo_code'])) {
            $reduction = get_promo_by_code($_SESSION['promo_code']);
            if($reduction) $total -= $total * ($reduction / 100);
        }

        return $total;
    }

    function update_pickup_datetime($pickup_datetime) {
    $timestamp = strtotime($pickup_datetime);

    if(!$timestamp) {
        return [
            "type" => "error",
            "title" => "Date invalide",
            "message" => "La date sélectionnée est invalide"
        ];
    }

    $minimum_time = strtotime('+30 minutes');

    if($timestamp < $minimum_time) {
        return [
            "type" => "error",
            "title" => "Date invalide",
            "message" => "La récupération doit être au minimum dans 30 minutes"
        ];
    }

    $_SESSION['pickup_datetime'] = date('Y-m-d H:i:s', $timestamp);

    return [
        "type" => "success",
        "title" => "Date enregistrée",
        "message" => "Date de récupération enregistrée"
    ];
    }
    
    function remove_from_basket($product_id) {
        if(!get_product_by_id($product_id)) return null;

        $basket = get_basket();
        
        if(isset($basket[$product_id])) {
            if ($basket[$product_id] > 1) $basket[$product_id]--;
            else unset($basket[$product_id]);
        }

        $_SESSION['basket'] = $basket;
        return true;
    }

    function empty_basket() {
        unset($_SESSION['basket']);
        unset($_SESSION['delivery_type']);
        unset($_SESSION['promo_code']);
        unset($_SESSION['pickup_datetime']);

        return [
            "type" => "success",
            "title" => "Panier vidé",
            "message" => "Votre panier à été vidé avec succès"
        ];
    }

    // MARK: - API endpoints
    if($_SERVER['SCRIPT_FILENAME'] === __FILE__) {
        if(isset($_GET['add'])) {
            $type = "success";
            $title = "Ajouté avec succès";
            $message = "L'article à été ajouté avec succès au panier";
            
            if(!add_to_basket($_GET['add'])) {
                $type = "error";
                $title = "Erreur";
                $message = "Impossible d'ajouté cet article au panier";
            }
                
            echo json_encode([
                "type" => $type,
                "title" => $title,
                "message" => $message
            ]);
        } else if(isset($_GET['remove'])) {
            $type = "success";
            $title = "Retiré avec succès";
            $message = "L'article à été retiré du panier avec succès";

            if(!remove_from_basket($_GET['remove'])) {
                $type = "error";
                $title = "Erreur";
                $message = "Impossible de retirer cet article du panier";
            }
            
            echo json_encode([
                "type" => $type,
                "title" => $title,
                "message" => $message
            ]);
        }
        else if(isset($_GET['empty'])) echo json_encode(empty_basket());
        else if(isset($_GET['get'])) echo json_encode(get_basket_data());
        else if(isset($_GET['update_delivery'])) echo json_encode(update_delivery_type($_GET['update_delivery']));
        else if(isset($_GET['update_pickup'])) echo json_encode(update_pickup_datetime($_GET['update_pickup']));
        else if(isset($_GET['promo_code'])) echo json_encode(get_promo_by_code($_GET['promo_code']));
    }
?>