<?php
    session_start();

    function get_product_by_id($id) {
        $json = file_get_contents(__DIR__ . '/../data/products.json');
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
        if (!file_exists(__DIR__ . '/../data/promocodes.json')) {
            return [];
        }

        $json = file_get_contents(__DIR__ . '/../data/promocodes.json');
        $data = json_decode($json, true);

        if (is_array($data) && isset($data['coupons']) && is_array($data['coupons'])) {
            return $data['coupons'];
        }

        return [];
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
                    <div class='flex-row items-center gap-16'>
                        <a href='./php/function_basket.php?action=remove&dish_id={$id}' class='btn btn-qty'>-</a>
                        <p class='font-600 text-lg'>x{$quantite}</p>
                        <a href='./php/function_basket.php?action=add&dish_id={$id}' class='btn btn-qty'>+</a>
                    </div>
                    <p class='font-600 text-primary text-right'>{$prix_ligne} €</p>
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
                <p class='font-600 text-right'>{$sous_total_fmt} €</p>
            </div>
            <div class='flex-row justify-between items-center gap-24'>
                <p>Livraison</p>
                <p class='font-600 text-right'>2,99 €</p>
            </div>
            <div class='flex-row justify-between items-center gap-24'>
                <p>Réduction</p>
                <p class='font-600 text-right'>-{$reduction_fmt} €</p>
            </div>
            <hr />
            <div class='flex-row justify-between items-center gap-24'>
                <p class='font-600'>Total</p>
                <p class='font-600 text-primary text-right'>{$total_fmt} €</p>
            </div>
        </div>
        ";
    }

    function get_basket() {
        if(!isset($_SESSION['basket']) || !is_array($_SESSION['basket'])) return [];
        return $_SESSION['basket'];
    }

    function add_to_basket($dish_id) {
        $basket = get_basket();
        
        if(isset($basket[$dish_id])) $basket[$dish_id]++;
        else $basket[$dish_id] = 1;

        $_SESSION['basket'] = $basket;
    }

    function remove_from_basket($dish_id) {
        $basket = get_basket();
        
        if(isset($basket[$dish_id])) {
            if ($basket[$dish_id] > 1) {
                $basket[$dish_id]--;
            } else {
                unset($basket[$dish_id]);
            }
        }

        $_SESSION['basket'] = $basket;
    }

    function empty_basket() {
        $_SESSION['basket'] = [];
    }

    if (isset($_GET['dish_id'])) {
        if (isset($_GET['action']) && $_GET['action'] == 'remove') {
            remove_from_basket($_GET['dish_id']);
        } else {
            add_to_basket($_GET['dish_id']);
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit();
    }
?>