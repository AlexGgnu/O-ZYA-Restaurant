<?php
    require_once(__DIR__ . '/function_products.php');
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

    function formatDetailsCommande($details) {
        if (!is_array($details) || empty($details)) {
            return 'Aucun menu';
        }

        $lignes = [];
        $isSequential = array_keys($details) === range(0, count($details) - 1);

        if ($isSequential) {
            foreach ($details as $detail) {
                if (is_string($detail) && trim($detail) !== '') {
                    $lignes[] = $detail;
                }
            }
        } else {
            foreach ($details as $dishId => $quantite) {
                $dish = get_product_by_id($dishId);
                if ($dish != null) {
                    $lignes[] = $dish['name'] . ' x' . max(1, intval($quantite));
                }
            }
        }

        if (empty($lignes)) {
            return 'Aucun menu';
        }

        return implode(', ', $lignes);
    }

    function afficherLignesCommandes($commandes, $contexte = 'orders') {
        if (empty($commandes)) {
            $colspan = ($contexte === 'profile') ? 5 : 6;

            echo '
                <tr>
                    <td colspan="' . $colspan . '" class="text-center">Vous n\'avez aucune commande dans votre historique</td>
                </tr>
            ';
            return;
        }

        foreach ($commandes as $commande) {
            $idCommande = '';
            if (isset($commande['id_order'])) {
                $idCommande = htmlspecialchars($commande['id_order']);
            } else if (isset($commande['id'])) {
                $idCommande = htmlspecialchars($commande['id']);
            }

            $account = '';
            if (isset($commande['id_client'])) {
                $account_data = get_account_by_id($commande['id_client']);
                if (is_array($account_data)) {
                    $lastname = isset($account_data['lastname']) ? $account_data['lastname'] : '';
                    $firstname = isset($account_data['firstname']) ? $account_data['firstname'] : '';
                    $account = trim($lastname . ' ' . $firstname);
                }
            }

            if ($account === '') {
                $account = 'Client inconnu';
            } else {
                $account = htmlspecialchars($account);
            }

            if (!empty($commande['adresse'])) {
                $adresse = htmlspecialchars($commande['adresse']);
            } else {
                $adresse = 'À emporter';
            }

            $details = isset($commande['details']) ? formatDetailsCommande($commande['details']) : 'Aucun menu';

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
                $statut = 'preparing';
            }

            $libelleStatut = htmlspecialchars(getLibelleStatut($statut));

            if ($contexte === 'profile') {
                echo "
                    <tr>
                        <td>{$dateHeure}</td>
                        <td>#{$idCommande}</td>
                        <td>{$detailComplet}</td>
                        <td>{$total}€</td>
                        <td class=\"text-center\">
                            <button class=\"btn btn-status\" data-status=\"{$statut}\" disabled>
                                {$libelleStatut}
                            </button>
                        </td>
                    </tr>
                ";
            } else {
                echo "
                    <tr>
                        <td>#{$idCommande}</td>
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
    }

    function save_order() {
        if (!is_logged()) return false;

        $basket = get_basket();
        if (empty($basket)) return false;
        
        $total = 0;
        $details = [];

        foreach ($basket as $product_id => $quantitie) {
            $product = get_product_by_id($product_id);
            if ($product != null) {
                $details[$product_id] = $quantitie;
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
            'statut' => 'preparing',
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