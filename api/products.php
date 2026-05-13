<?php
    header('Content-Type: application/json');
    $productsData = file_get_contents( __DIR__ . '/../data/products.json');

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


    if ($_GET['action'] === 'getAllProducts') {
        echo $productsData;
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