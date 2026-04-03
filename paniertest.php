<?php
    require_once('./php/function_panier.php');
    require_once('./php/footer.php');
    require_once('./php/function_paiments.php');

    $params = get_paiment_params();
?>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Panier - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/main.css">
    </head>
    <body>