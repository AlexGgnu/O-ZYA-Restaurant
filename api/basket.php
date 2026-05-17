<?php
    if(!function_exists('is_logged')) require_once(__DIR__ . '/account.php');
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
            "title" => "Mode de livraison mis à jour",
            "message" => "Votre mode de livraison à été mis à jour avec succès"
        ];
    }

    // MARK: - Promo code management functions
    function get_promo_by_code($code) {
        global $promotions_file_path;
        $promo_codes = json_decode(file_get_contents($promotions_file_path), true);

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

        return [
            "title" => "Panier vidé",
            "message" => "Votre panier à été vidé avec succès"
        ];
    }

    // MARK: - API endpoints
    if(isset($_GET['add'])) {
        $title = "Ajouté avec succès";
        $message = "L'article à été ajouté avec succès au panier";
        
        if(!add_to_basket($_GET['add'])) {
            $title = "Erreur";
            $message = "Impossible d'ajouté cet article au panier";
        }
            
        echo json_encode([
            "title" => $title,
            "message" => $message
        ]);
    } else if(isset($_GET['remove'])) {
        $title = "Retiré avec succès";
        $message = "L'article à été retiré du panier avec succès";

        if(!remove_from_basket($_GET['remove'])) {
            echo json_encode([
                "title" => "Erreur",
                "message" => "Impossible de retirer cet article du panier"
            ]);
        }
        
        echo json_encode([
            "title" => $title,
            "message" => $message
        ]);
    }
    else if(isset($_GET['empty'])) echo json_encode(empty_basket());
    else if(isset($_GET['get'])) echo json_encode(get_basket_data());
    else if(isset($_GET['update_delivery'])) echo json_encode(update_delivery_type($_GET['update_delivery']));
    else if(isset($_GET['promo_code'])) echo json_encode(get_promo_by_code($_GET['promo_code']));
?>