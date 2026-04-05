<?php
    require_once('./function_account.php');

    function get_notations_data() {
        $file = __DIR__ . '/../data/notations.json';

        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        if (is_array($data)) {
            return $data;
        }

        return [];
    }

    function save_notation() {
        if (!is_logged() || !isset($_SESSION['uuid'])) {
            header('Location: /connection.php?error=' . urlencode('Veuillez vous connecter pour laisser un avis'));
            exit();
        }

        $commentaire = '';
        if (isset($_POST['commentaire'])) {
            $commentaire = trim($_POST['commentaire']);
        }

        if ($commentaire === '') {
            header('Location: /notation.php?error=' . urlencode('Le commentaire est obligatoire'));
            exit();
        }

        $notations = get_notations_data();

        $new_notation = [
            'id' => uniqid(),
            'id_client' => $_SESSION['uuid'],
            'commentaire' => $commentaire,
            'note_produits' => 5,
            'note_livraison' => 5,
            'date_heure' => date('Y-m-d H:i:s')
        ];

        $notations[] = $new_notation;

        file_put_contents(
            __DIR__ . '/../data/notations.json',
            json_encode($notations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        header('Location: /notation.php?success=' . urlencode('Merci pour votre avis'));
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        save_notation();
    }
?>
