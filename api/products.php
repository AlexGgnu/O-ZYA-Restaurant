<?php
    header('Content-Type: application/json');
    $productsData = file_get_contents( __DIR__ . '/../data/products.json');

    function getProductsByCountry($productsData, $country) {
        $products = [];

        foreach ($productsData['products'] as $countryName => $countryProducts) {
            if (strtolower($countryName) === strtolower($country)) {
                foreach ($countryProducts as $category => $items) {
                    foreach ($items as $item) {
                        $products[] = $item;
                    }
                }
            }
        }

        return $products;
    }

    function getSpecialDish($productsData) {
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

    function getSuccessedDishes($productsData) {
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


    if ($_GET['action'] === 'getProductsByCountry' && isset($_GET['country'])) {
        $country = $_GET['country'];
        $products = getProductsByCountry(json_decode($productsData, true), $country);
        echo json_encode($products);
    } elseif ($_GET['action'] === 'getSpecialDish') {
        $specialDish = getSpecialDish(json_decode($productsData, true));
        echo json_encode($specialDish);
    } elseif ($_GET['action'] === 'getSuccessedDishes') {
        $successedDishes = getSuccessedDishes(json_decode($productsData, true));
        echo json_encode($successedDishes);
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
?>