<?php
    require_once(__DIR__ . '/products.php');

    function get_basket() {
        if(!isset($_SESSION['basket']) || !is_array($_SESSION['basket'])) return [];
        return $_SESSION['basket'];
    }

    function add_to_basket($product_id) {
        if(!get_product_by_id($product_id)) return null;

        $basket = get_basket();
        
        if(isset($basket[$product_id])) $basket[$product_id]++;
        else $basket[$product_id] = 1;

        $_SESSION['basket'] = $basket;
        return true;
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
        $_SESSION['basket'] = [];
    }

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
        if(!remove_from_basket($_GET['remove'])) {
            echo json_encode([
                "title" => "Erreur",
                "message" => "Impossible de retirer cet article du panier"
            ]);
        }
    }
?>