<?php
    if(session_status() === PHP_SESSION_NONE) session_start();
    if(!function_exists("is_logged")) require_once(__DIR__ . '/account.php');

    $notations_file_path = __DIR__ . '/../data/notations.json';
    if(!file_exists($notations_file_path)) file_put_contents($notations_file_path, json_encode([], JSON_PRETTY_PRINT));

    function get_notations_data() {
        global $notations_file_path;
        $content = file_get_contents($notations_file_path);
        $data = json_decode($content, true);

        if (is_array($data)) return $data;
        else return [];
    }

    function save_notation() {
        global $notations_file_path;
        $notations = get_notations_data();

        if (!is_logged() || !isset($_SESSION['uuid'])) {
            $_SESSION['error'] = urlencode('Veuillez vous connecter pour laisser un avis');
            $_SESSION['comment_temp'] = isset($_POST['commentaire']) ? urlencode($_POST['commentaire']) : '';

            header('Location: /sign_in.php?redirection=notation');
            exit();
        }

        if (isset($_POST['commentaire']) && $commentaire !== '') $commentaire = trim($_POST['commentaire']);
        else {
            $_SESSION['comment_temp'] = isset($_POST['commentaire']) ? urlencode($_POST['commentaire']) : '';
            $_SESSION['error'] = urlencode('Le commentaire est obligatoire');

            header('Location: /notation.php');
            exit();
        }

        if (isset($_POST['rating']) && $_POST['rating'] > 0 && $_POST['rating'] <= 5) $rating = $_POST['rating'];
        else {
            $_SESSION['comment_temp'] = urlencode($commentaire);
            $_SESSION['error'] = urlencode('La note est obligatoire');

            header('Location: /notation.php');
            exit();
        }

        $new_notation = [
            'id' => uniqid(),
            'id_client' => $_SESSION['uuid'],
            'commentaire' => $commentaire ?? '',
            'note' => $rating > 0 && $rating <= 5 ? $rating : 1,
            'date_heure' => date('Y-m-d H:i:s')
        ];
        $notations[] = $new_notation;

        file_put_contents($notations_file_path, json_encode($notations, JSON_PRETTY_PRINT));

        header('Location: /notation.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['add'])) save_notation();
?>