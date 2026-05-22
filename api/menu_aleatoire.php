<?php
header('Content-Type: application/json');

$jsonData = file_get_contents('../data/products.json');
$products = json_decode($jsonData, true);

if (!empty($products)) {
    $randomKey = array_rand($products);
    $itemAleatoire = $products[$randomKey];
    
    echo json_encode($itemAleatoire);
} else {
    echo json_encode(["error" => "Aucun produit trouvé"]);
}
?>