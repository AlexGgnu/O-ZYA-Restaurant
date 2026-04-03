<?php
    session_start();

    function get_basket() {
        if(!isset($_SESSION['basket']) || !is_array($_SESSION['basket'])) return [];
        return $_SESSION['basket'];
    }

    function add_to_basket($dish_id) {
        $basket = get_basket();
        
        if(isset($basket[$dish_id])) $basket[$dish_id]++;
        else $basket[$dish_id] = 1;

        $_SESSION['basket'] = $basket;
    }

    function remove_from_basket($dish_id) {
        $basket = get_basket();
        
        if(isset($basket[$dish_id])) {
            if($basket[$dish_id] <= 0) unset($basket[$dish_id]);

            $basket[$dish_id]--;
        }

        $_SESSION['basket'] = $basket;
    }

    function empty_basket() {
        $_SESSION['basket'] = [];
    }

    if(isset($_GET['dish_id'])) add_to_basket($_GET['dish_id']);
    
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
?>