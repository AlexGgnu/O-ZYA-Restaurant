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

    // MARK: - Data rendering
    function render_dish_card($product) {
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

    function render_special_dish() {
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
    function render_successed_dishes() {
        $special_dishes = get_successed_dish_data();

        foreach($special_dishes as $dish) {
            echo render_dish_card($dish);
        }
    }
    function render_category_dish($category) {
        $products_data = get_decoded_products_data();

        foreach ($products_data['products'] as $country_name => $country_data) {
            if (!empty($country_data[$category])) {
                
                echo '
                    <section class="w-full">
                        <h1 class="mb-24 ml-40">' . $country_name . '</h1>
                        <div class="cards__wrapper">
                        <div class="cards__track gap-24 ph-40 lg-grid-cols-2">
                ';

                foreach ($country_data[$category] as $item) {
                    echo render_dish_card($item);
                }

                echo '
                            </div>
                        </div>
                    </section>
                ';
            }
        }
    }
?>