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

    function getLibelleStatut($statut, $isTakeaway = false) {
        if ($isTakeaway) {
            if ($statut == 'preparing') {
                return 'En cours de préparation';
            } else if ($statut == 'waiting') {
                return 'En attente de récupération';
            } else if ($statut == 'finished') {
                return 'Récupéré';
            } else {
                return 'Statut inconnu';
            }
        } else {
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

    function getOrderStatusOptions() {
        return [
            'preparing' => 'En cours de préparation',
            'waiting' => 'En attente de livraison',
            'delivery' => 'En cours de livraison',
            'finished' => 'Livrée',
        ];
    }

    function getTakeawayStatusOptions() {
        return [
            'preparing' => 'En cours de préparation',
            'waiting' => 'En attente de récupération',
            'finished' => 'Récupéré',
        ];
    }

    function isTakeawayOrder($commande) {
        if (!isset($commande['adresse'])) return true;

        $adresse = trim(mb_strtolower($commande['adresse']));
        if ($adresse === '' || $adresse === 'à emporter' || $adresse === 'à emporté') return true;
        
        return false;
    }

    function isDeliveryOrder($commande) {
        return !isTakeawayOrder($commande);
    }

    function get_available_livreurs($commandes, $currentLivreurId = '') {
        $accounts = get_accounts_data();
        $busyLivreurs = [];

        foreach ($commandes as $commande) {
            if (!isset($commande['statut']) || $commande['statut'] !== 'preparing') {
                continue;
            }

            if (isset($commande['id_livreur']) && !empty($commande['id_livreur'])) {
                $busyLivreurs[$commande['id_livreur']] = true;
            }
        }

        $availableLivreurs = [];

        foreach ($accounts as $account) {
            if (!isset($account['role'])) {
                continue;
            }

            $role = strtolower($account['role']);
            if ($role !== 'delivery') {
                continue;
            }

            $accountId = isset($account['id']) ? $account['id'] : '';
            if ($accountId === '') {
                continue;
            }

            if (isset($busyLivreurs[$accountId]) && $accountId !== $currentLivreurId) {
                continue;
            }

            $lastname = isset($account['lastname']) ? $account['lastname'] : '';
            $firstname = isset($account['firstname']) ? $account['firstname'] : '';
            $label = trim($lastname . ' ' . $firstname);

            if ($label === '') {
                $label = $accountId;
            }

            $availableLivreurs[$accountId] = $label;
        }

        return $availableLivreurs;
    }

    function get_delivery_order_for_livreur($livreurId) {
        $livreurId = trim($livreurId);
        if ($livreurId === '') {
            return null;
        }

        $commandes = lireCommandes('');

        foreach ($commandes as $commande) {
            if (!isset($commande['id_livreur']) || $commande['id_livreur'] !== $livreurId) {
                continue;
            }

            if (!isset($commande['statut']) || $commande['statut'] === 'finished') {
                continue;
            }

            return $commande;
        }

        return null;
    }

    function updateDeliveryOrderStatus($orderId, $livreurId, $status) {
        $commandes = lireCommandes('');
        $livreurId = trim($livreurId);

        $found = false;

        foreach ($commandes as &$commande) {
            if (!isset($commande['id_order']) || $commande['id_order'] !== $orderId) {
                continue;
            }

            if (!isset($commande['id_livreur']) || $commande['id_livreur'] !== $livreurId) {
                continue;
            }

            $isTakeaway = isTakeawayOrder($commande);
            if ($isTakeaway) {
                continue;
            }

            $statusOptions = getOrderStatusOptions();
            if (!isset($statusOptions[$status])) {
                $status = 'delivery';
            }

            $commande['statut'] = $status;
            $found = true;
            break;
        }
        unset($commande);

        if ($found) {
            $fichier_commandes = __DIR__ . '/../data/orders.json';
            $json_content = json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $result = file_put_contents($fichier_commandes, $json_content);

            if ($result === false) {
                error_log("Erreur lors de l'écriture du fichier orders.json: " . json_last_error_msg());
            }
        }

        return $found;
    }

    function updateOrderAdminData($orderId, $status, $livreurId = '') {
        $commandes = lireCommandes('');
        $livreurId = trim($livreurId);

        $found = false;
        foreach ($commandes as &$commande) {
            if (!isset($commande['id_order']) || $commande['id_order'] !== $orderId) {
                continue;
            }

            if (isset($commande['statut']) && $commande['statut'] === 'finished') {
                return;
            }

            $isTakeaway = isTakeawayOrder($commande);
            
            if ($isTakeaway) {
                $statusOptions = getTakeawayStatusOptions();
            } else {
                $statusOptions = getOrderStatusOptions();
            }
            
            $status = isset($statusOptions[$status]) ? $status : 'waiting';
            $commande['statut'] = $status;

            if ($isTakeaway) {
                if (isset($commande['id_livreur'])) {
                    unset($commande['id_livreur']);
                }
            } else {
                if ($livreurId !== '') {
                    $commande['id_livreur'] = $livreurId;
                } else if (!isset($commande['id_livreur'])) {
                    $commande['id_livreur'] = '';
                }
            }
            $found = true;
            break;
        }
        unset($commande);

        if ($found) {
            $fichier_commandes = __DIR__ . '/../data/orders.json';
            $json_content = json_encode($commandes, JSON_PRETTY_PRINT);
            $result = file_put_contents($fichier_commandes, $json_content);
            
            if ($result === false) {
                error_log("Erreur lors de l'écriture du fichier orders.json: " . json_last_error_msg());
            }
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
            $colspan = ($contexte === 'profile') ? 5 : 7;

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

            $isTakeaway = isTakeawayOrder($commande);
            $libelleStatut = htmlspecialchars(getLibelleStatut($statut, $isTakeaway));
            
            if ($isTakeaway) {
                $statusOptions = getTakeawayStatusOptions();
            } else {
                $statusOptions = getOrderStatusOptions();
            }
            
            $isDeliveryOrder = !$isTakeaway;
            $currentLivreurId = '';
            $availableLivreurs = [];

            if ($contexte === 'orders') {
                $currentLivreurId = isset($commande['id_livreur']) ? $commande['id_livreur'] : '';
                $availableLivreurs = $isDeliveryOrder ? get_available_livreurs($commandes, $currentLivreurId) : [];

                if ($isDeliveryOrder && $currentLivreurId !== '' && !isset($availableLivreurs[$currentLivreurId])) {
                    $currentLivreur = get_account_by_id($currentLivreurId);
                    if (is_array($currentLivreur)) {
                        $lastname = isset($currentLivreur['lastname']) ? $currentLivreur['lastname'] : '';
                        $firstname = isset($currentLivreur['firstname']) ? $currentLivreur['firstname'] : '';
                        $currentLivreurLabel = trim($lastname . ' ' . $firstname);

                        if ($currentLivreurLabel === '') {
                            $currentLivreurLabel = $currentLivreurId;
                        }

                        $availableLivreurs = [$currentLivreurId => $currentLivreurLabel] + $availableLivreurs;
                    }
                }
            }

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
                $isFinished = ($statut === 'finished');

                if ($isFinished) {
                    $statusSelectHtml = '<button class="btn btn-status" data-status="finished" disabled>' . htmlspecialchars($libelleStatut) . '</button>';
                } else {
                    $statusSelectHtml = '<form method="POST" class="flex-col items-stretch gap-10">';
                    $statusSelectHtml .= '<input type="hidden" name="order_id" value="' . htmlspecialchars($idCommande) . '">';
                    $statusSelectHtml .= '<input type="hidden" name="id_livreur" value="' . htmlspecialchars($currentLivreurId) . '">';
                    $statusSelectHtml .= '<select class="form-control" name="statut" onchange="this.form.submit()">';

                    foreach ($statusOptions as $statusKey => $statusLabel) {
                        $selected = ($statusKey === $statut) ? ' selected' : '';
                        $statusSelectHtml .= '<option value="' . htmlspecialchars($statusKey) . '"' . $selected . '>' . htmlspecialchars($statusLabel) . '</option>';
                    }

                    $statusSelectHtml .= '</select>';
                    $statusSelectHtml .= '</form>';
                }

                $livreurHtml = '<span>-</span>';
                if ($isDeliveryOrder && !$isFinished) {
                    $livreurHtml = '<form method="POST" class="flex-col items-stretch gap-10">';
                    $livreurHtml .= '<input type="hidden" name="order_id" value="' . htmlspecialchars($idCommande) . '">';
                    $livreurHtml .= '<input type="hidden" name="statut" value="' . htmlspecialchars($statut) . '">';
                    $livreurHtml .= '<select class="form-control" name="id_livreur" onchange="this.form.submit()">';
                    $livreurHtml .= '<option value="">Sélectionner un livreur</option>';

                    if (empty($availableLivreurs)) {
                        $livreurHtml .= '<option value="" disabled>Aucun livreur disponible</option>';
                    }

                    foreach ($availableLivreurs as $livreurId => $livreurLabel) {
                        $selected = ($livreurId === $currentLivreurId) ? ' selected' : '';
                        $livreurHtml .= '<option value="' . htmlspecialchars($livreurId) . '"' . $selected . '>' . htmlspecialchars($livreurLabel) . '</option>';
                    }

                    $livreurHtml .= '</select>';
                    $livreurHtml .= '</form>';
                }

                echo '
                    <tr>
                        <td>#' . $idCommande . '</td>
                        <td>' . $account . '</td>
                        <td>' . $adresse . '</td>
                        <td>' . $detailComplet . '</td>
                        <td>' . $total . '€</td>
                        <td class="text-center">' . $statusSelectHtml . '</td>
                        <td class="text-center">' . $livreurHtml . '</td>
                    </tr>
                ';
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