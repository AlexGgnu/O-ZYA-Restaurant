<?php
    // MARK: - Data processing
    function get_decoded_products_data() {
        $products_data_json = file_get_contents(__DIR__ . '/../data/products.json');
        return json_decode($products_data_json, true);
    }
    function get_product_by_id($id) {
        $products_data = get_decoded_products_data();

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

    // MARK: - Data rendering
    function render_special_dish() {
        $special_dish = get_special_dish_data();

        $desc = !empty($special_dish['longDescription']) ? $special_dish['longDescription'] : $special_dish['shortDescription'];

        echo '
            <div>
                <h2 class="text-primary font-600">' . $special_dish['name'] . '</h2>
                <p>' . $desc . '</p>
            </div>
            <a class="btn btn-primary" href="./php/function_basket.php?dish_id=' . $special_dish['id'] . '">
                JE COMMANDE • ' . $special_dish['price'] . '€
            </a>
        ';
    }
?>