<?php

function lireCommandes($fichier) {
    if ($fichier == '') {
        $fichier = './data/orders.json';
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
                <td colspan="6" class="text-center">Aucune commande trouvée</td>
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
?>