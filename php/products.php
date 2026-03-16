<?php
    // MARK: - Data processing
    function get_decoded_products_data() {
        $products_data_json = file_get_contents('./data/products.json');
        return json_decode($products_data_json, true);
    }
    function get_special_dish_data() {
        $products_data = get_decoded_products_data();

        $all_dishes = [];
        foreach ($products_data['products'] as $country) {
            $all_dishes = array_merge($all_dishes, $country['dishes']);
        }

        $special_dishes = array_filter($all_dishes, function($dish) {
            return isset($dish['isSpecialDish']) && $dish['isSpecialDish'] == true;
        });
        
        return reset($special_dishes);
    }
    function get_successed_dish_data() {
        $products_data = get_decoded_products_data();

        $all_dishes = [];
        foreach ($products_data['products'] as $country) {
            $all_dishes = array_merge($all_dishes, $country['dishes']);
        }

        $successed_dishes = array_filter($all_dishes, function($dish) {
            return isset($dish['isSuccessed']) && $dish['isSuccessed'] == true;
        });
        
        return $successed_dishes;
    }

    // MARK: - Data displaying
    function display_card_dish($product) {
        echo '
            <div class="dish__card">
                <h3>' . $product['name'] . '</h3>
                <img class="w-full min-h-0 object-contain object-center filter-drop-shadow" src="' . $product['image'] . '" alt="' . $product['name'] . '">
                <p class="text-sm text-center">' . $product['shortDescription'] . '</p>
                <button class="btn btn-primary" data-product-id="' . $product['id'] . '">
                    JE COMMANDE • ' . $product['price'] . '€
                </button>
            </div>
        ';
    }

    function display_special_dish() {
        $special_dish = get_special_dish_data();

        $desc = !empty($special_dish['longDescription']) ? $special_dish['longDescription'] : $special_dish['shortDescription'];

        echo '
            <div>
                <h2 class="text-primary font-600">' . $special_dish['name'] . '</h2>
                <p>' . $desc . '</p>
            </div>
            <button class="btn btn-primary" data-product-id="' . $special_dish['id'] . '">
                JE COMMANDE • ' . $special_dish['price'] . '€
            </button>
        ';
    }
    function display_successed_dish() {
        $special_dishes = get_successed_dish_data();

        foreach($special_dishes as $dish) {
            echo display_card_dish($dish);
        }
    }

?>