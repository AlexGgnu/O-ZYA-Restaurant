<?php
    function lireCommandes(string $fichier = './data/orders.json'): array {
        if (!file_exists($fichier)) {
            return [];
        }

        $json = file_get_contents($fichier);
        $commandes = json_decode($json, true);

        return is_array($commandes) ? $commandes : [];
    }

    function getLibelleStatut(string $statut): string {
        switch ($statut) {
            case 'preparing':
                return 'En cours de préparation';
            case 'delivery':
                return 'En cours de livraison';
            case 'finished':
                return 'Livrée';
            case 'waiting':
                return 'En attente';
            default:
                return 'Statut inconnu';
        }
    }

    function getLibellePaiement(string $statutPaiement): string {
        switch ($statutPaiement) {
            case 'paye':
                return 'Payé';
            case 'en_attente':
                return 'En attente';
            default:
                return 'Non renseigné';
        }
    }

    function format_status($status) {
    switch ($status) {
        case "preparing": return "En préparation";
        case "delivery": return "En livraison";
        case "finished": return "Livrée";
        default: return $status;
    }
    }

    function afficherLignesCommandes(array $commandes): void {
        if (empty($commandes)) {
            echo '
                <tr>
                    <td colspan="6" class="text-center">Aucune commande trouvée</td>
                </tr>
            ';
            return;
        }

        foreach ($commandes as $commande) {
            $id = htmlspecialchars((string)($commande['id'] ?? ''));
            $account_data = get_account_by_id($commande['id_client']);
            $account = $account_data['lastname'] . ' ' . $account_data['firstname'];

            $adresse = !empty($commande['adresse'])
                ? htmlspecialchars((string)$commande['adresse'])
                : 'À emporter';

            $details = 'Aucun menu';
            if (isset($commande['details']) && is_array($commande['details']) && !empty($commande['details'])) {
                $details = implode(', ', $commande['details']);
            }

            $statutPaiement = getLibellePaiement((string)($commande['statut_paiement'] ?? ''));
            $dateHeure = (string)($commande['date_heure'] ?? 'Non renseigné');

            $detailComplet =
                '<strong>Menus :</strong> ' . htmlspecialchars($details) . '<br>' .
                '<strong>Paiement :</strong> ' . htmlspecialchars($statutPaiement) . '<br>' .
                '<strong>Date :</strong> ' . htmlspecialchars($dateHeure);

            $total = htmlspecialchars((string)($commande['total'] ?? '0'));
            $statut = htmlspecialchars((string)($commande['statut'] ?? 'waiting'));
            $libelleStatut = htmlspecialchars(getLibelleStatut((string)($commande['statut'] ?? 'waiting')));

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