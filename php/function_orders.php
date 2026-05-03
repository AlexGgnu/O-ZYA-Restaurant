<?php
    require_once(__DIR__ . '/function_basket.php');
    require_once(__DIR__ . '/function_account.php');

    function lireCommandes($fichier) {
        if ($fichier == '') {
            $fichier = __DIR__ . '/../data/orders.json';
        }

        if (!file_exists($fichier)) {
            return [];
        }

        $json = file_get_contents($fichier);
        $commandes = json_decode($json, true);

        if (is_array($commandes)) {
            return $commandes;
        } else {
            return [];
        }
    }

    function getLibelleStatut($statut) {
        if ($statut == 'preparing') {
            return 'En cours de préparation';
        } else if ($statut == 'delivery') {
            return 'En cours de livraison';
        } else if ($statut == 'finished') {
            return 'Livrée';
        } else if ($statut == 'waiting') {
            return 'En attente';
        } else {
            return 'Statut inconnu';
        }
    }

    function getLibellePaiement($statutPaiement) {
        if ($statutPaiement == 'paye') {
            return 'Payé';
        } else if ($statutPaiement == 'en_attente') {
            return 'En attente';
        } else {
            return 'Non renseigné';
        }
    }

    function afficherLignesCommandes($commandes) {
        if (empty($commandes)) {
            echo '
                <tr>
                    <td colspan="5" class="text-center">Vous n\'avez aucune commande dans votre historique</td>
                </tr>
            ';
            return;
        }

        foreach ($commandes as $commande) {

            if (isset($commande['id'])) {
                $id = htmlspecialchars($commande['id']);
            } else {
                $id = '';
            }

            $account_data = get_account_by_id($commande['id_client']);
            $account = $account_data['lastname'] . ' ' . $account_data['firstname'];

            if (!empty($commande['adresse'])) {
                $adresse = htmlspecialchars($commande['adresse']);
            } else {
                $adresse = 'À emporter';
            }

            $details = 'Aucun menu';
            if (isset($commande['details']) && is_array($commande['details']) && !empty($commande['details'])) {
                $details = implode(', ', $commande['details']);
            }

            if (isset($commande['statut_paiement'])) {
                $statutPaiement = getLibellePaiement($commande['statut_paiement']);
            } else {
                $statutPaiement = getLibellePaiement('');
            }

            if (isset($commande['date_heure'])) {
                $dateHeure = $commande['date_heure'];
            } else {
                $dateHeure = 'Non renseigné';
            }

            $detailComplet =
                '<strong>Menus :</strong> ' . htmlspecialchars($details) . '<br>' .
                '<strong>Paiement :</strong> ' . htmlspecialchars($statutPaiement) . '<br>' .
                '<strong>Date :</strong> ' . htmlspecialchars($dateHeure);

            if (isset($commande['total'])) {
                $total = htmlspecialchars($commande['total']);
            } else {
                $total = '0';
            }

            if (isset($commande['statut'])) {
                $statut = htmlspecialchars($commande['statut']);
            } else {
                $statut = 'waiting';
            }

            $libelleStatut = htmlspecialchars(getLibelleStatut($statut));

            echo "
                <tr>
                    <td>#{$id}</td>
                    <td>{$account}</td>
                    <td>{$adresse}</td>
                    <td>{$detailComplet}</td>
                    <td>{$total}€</td>
                    <td class=\"text-center\">
                        <button class=\"btn btn-status\" data-status=\"{$statut}\" disabled>
                            {$libelleStatut}
                        </button>
                    </td>
                </tr>
            ";
        }
    }

    function save_order() {
        if (!is_login()) return false;

        $basket = get_basket();
        if (empty($basket)) return false;
        
        $details = [];
        $total = 0;

        foreach ($basket as $product_id => $quantitie) {
            $product = get_product_by_id($product_id);
            if ($product != null) {
                $details[] = $product['name'];
                $total += $product['price'] * $quantitie;
            }
        }

        $total += 2.99;

        $adresse = 'À Emporté';
        $delivery_type = isset($_SESSION['delivery_type']) ? $_SESSION['delivery_type'] : 'takeaway';
        
        if ($delivery_type === 'delivery') {
            $account = get_account_by_id($_SESSION['uuid']);
            if ($account != null && isset($account['address']) && !empty($account['address'])) $adresse = $account['address'];
        }

        $nouvelle_commande = [
            'id_order' => strtolower(uniqid()),
            'id_client' => $_SESSION['uuid'],
            'adresse' => $adresse,
            'details' => $details,
            'total' => number_format($total, 2, '.', ''),
            'statut' => 'waiting',
            'statut_paiement' => 'paye',
            'date_heure' => date('d/m/Y H:i:s')
        ];

        $fichier_commandes = __DIR__ . '/../data/orders.json';
        $commandes = lireCommandes($fichier_commandes);
        $commandes[] = $nouvelle_commande;

        $json = json_encode($commandes, JSON_PRETTY_PRINT);
        file_put_contents($fichier_commandes, $json);

        return true;
    }

?>