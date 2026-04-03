<?php
require_once('./php/functions_basket.php');

function get_product_by_id($id) {
    $json = file_get_contents('./data/products.json');
    $data = json_decode($json, true);

    foreach ($data['products'] as $pays => $categories) {
        foreach ($categories as $categorie => $produits) {
            if (is_array($produits)) {
                foreach ($produits as $produit) {
                    if (isset($produit['id']) && $produit['id'] == $id) {
                        return $produit;
                    }
                }
            }
        }
    }
    return null;
}

function get_coupons() {
    return [
        'OZYA10' => 10,
        'BIENVENUE' => 15,
        'ETE2025' => 5,
    ];
}

function appliquer_coupon($code, $sous_total) {
    $coupons = get_coupons();
    $code = strtoupper(trim($code));

    if (isset($coupons[$code])) {
        $reduction = $coupons[$code];
        $montant_reduit = $sous_total * ($reduction / 100);
        return $montant_reduit;
    }
    return 0;
}

function afficher_panier() {
    $panier = get_basket();
    $frais_livraison = 2.99;

    $code_promo = '';
    $reduction = 0;

    if (isset($_POST['code_promo'])) {
        $code_promo = $_POST['code_promo'];
    }

    if (empty($panier)) {
        echo '<p class="text-center">Votre panier est vide.</p>';
        return;
    }

    $sous_total = 0;
    $articles = [];

    foreach ($panier as $id => $quantite) {
        $produit = get_product_by_id($id);
        if ($produit != null) {
            $prix_total_ligne = $produit['price'] * $quantite;
            $sous_total = $sous_total + $prix_total_ligne;
            $articles[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'prix_total_ligne' => $prix_total_ligne,
            ];
        }
    }

    if ($code_promo != '') {
        $reduction = appliquer_coupon($code_promo, $sous_total);
    }

    $total = $sous_total + $frais_livraison - $reduction;

    foreach ($articles as $article) {
        $produit = $article['produit'];
        $quantite = $article['quantite'];
        $prix_ligne = number_format($article['prix_total_ligne'], 2, ',', '');
        $prix_unitaire = number_format($produit['price'], 2, ',', '');
        $id = $produit['id'];

        echo "
        <hr />
        <div class='flex-row justify-between items-center gap-24'>
            <div class='flex-row items-center gap-14'>
                <img src='{$produit['image']}' alt='{$produit['name']}' width='56' height='56'>
                <div class='flex-col gap-6'>
                    <p class='font-600'>{$produit['name']}</p>
                    <p class='text-sm'>{$prix_unitaire} €</p>
                </div>
            </div>
            <div class='flex-col items-end gap-6'>
                <div class='flex-row items-center gap-10'>
                    <a href='./php/functions_basket.php?action=remove&dish_id={$id}' class='btn btn-secondary'>-</a>
                    <p class='font-600'>x{$quantite}</p>
                    <a href='./php/functions_basket.php?action=add&dish_id={$id}' class='btn btn-secondary'>+</a>
                </div>
                <p class='font-600 text-primary'>{$prix_ligne} €</p>
            </div>
        </div>
        ";
    }

    echo '<hr />';

    $sous_total_fmt = number_format($sous_total, 2, ',', '');
    $reduction_fmt = number_format($reduction, 2, ',', '');
    $total_fmt = number_format($total, 2, ',', '');

    echo "
    <div class='flex-col gap-14'>
        <div class='flex-row justify-between items-center gap-24'>
            <p>Sous-total</p>
            <p class='font-600'>{$sous_total_fmt} €</p>
        </div>
        <div class='flex-row justify-between items-center gap-24'>
            <p>Livraison</p>
            <p class='font-600'>2,99 €</p>
        </div>
        <div class='flex-row justify-between items-center gap-24'>
            <p>Réduction</p>
            <p class='font-600'>-{$reduction_fmt} €</p>
        </div>
        <hr />
        <div class='flex-row justify-between items-center gap-24'>
            <p class='font-600'>Total</p>
            <p class='font-600 text-primary'>{$total_fmt} €</p>
        </div>
    </div>
    ";
}
?>