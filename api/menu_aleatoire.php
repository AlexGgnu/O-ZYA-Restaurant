<?php
header('Content-Type: application/json');

$jsonData = file_get_contents('../data/products.json');
$data = json_decode($jsonData, true);

$allProducts = [];

foreach ($data['products'] as $country) {
    foreach ($country as $category => $items) {
        if (is_array($items)) {
            foreach ($items as $item) {
                $allProducts[] = $item;
            }
        }
    }
}

if (!empty($allProducts)) {
    $random = $allProducts[array_rand($allProducts)];
    echo json_encode($random);
} else {
    echo json_encode(["error" => "Aucun produit trouvé"]);
}
?>