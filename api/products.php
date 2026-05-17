<?php

    function get_products_data() {
        $products_data_json = file_get_contents(__DIR__ . '/../data/products.json');
        return json_decode($products_data_json, true);
    }
    
    function get_product_by_id($id) {
        $products_data = get_products_data();

        foreach ($products_data['products'] as $country) {
            foreach ($country as $category => $dishes) {
                if (!is_array($dishes)) {
                    continue;
                }

                foreach ($dishes as $dish) {
                    if (isset($dish['id']) && $dish['id'] == $id) {
                        return $dish;
                    }
                }
            }
        }

        return null;
    }

    function get_special_dish($productsData) {
        foreach ($productsData['products'] as $countryProducts) {
            foreach ($countryProducts as $category => $items) {
                foreach ($items as $item) {
                    if (isset($item['isSpecialDish']) && $item['isSpecialDish'] === true) {
                        return $item;
                    }
                }
            }
        }

        return null;
    }

    function get_successed_dishes($productsData) {
        $successedDishes = [];

        foreach ($productsData['products'] as $countryProducts) {
            foreach ($countryProducts as $category => $items) {
                foreach ($items as $item) {
                    if (isset($item['isSuccessed']) && $item['isSuccessed'] === true) {
                        $successedDishes[] = $item;
                    }
                }
            }
        }

        return $successedDishes;
    }

    // MARK: - API endpoints
    if(isset($_GET['action'])) {
        if ($_GET['action'] === 'getAllProducts') {
            echo json_encode(get_products_data());
        } elseif ($_GET['action'] === 'getSpecialDish') {
            $specialDish = get_special_dish(get_products_data());
            echo json_encode($specialDish);
        } elseif ($_GET['action'] === 'getSuccessedDishes') {
            $successedDishes = get_successed_dishes(get_products_data());
            echo json_encode($successedDishes);
        } else {
            echo json_encode([
                "type" => "error",
                "title" => "Action non reconnue",
                "message" => "L'action demandée n'est pas reconnue. Veuillez vérifier votre requête."
            ]);
        }
    }
?>