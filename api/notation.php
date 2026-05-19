<?php
    if(session_status() === PHP_SESSION_NONE) session_start();
    if(!function_exists("is_logged")) require_once(__DIR__ . '/account.php');
    if(!function_exists("get_orders_by_user")) require_once(__DIR__ . '/order.php');

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

        if (!is_logged()) {
            $_SESSION['error'] = 'Veuillez vous connecter pour laisser un avis';
            $_SESSION['comment_temp'] = $_POST['commentaire'] ?? '';
            header('Location: /sign_in.php?redirection=notation');
            exit();
        }

        if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
            error_log('Order ID is missing in the notation submission.');
            header('Location: /profile.php');
            exit();
        }

        $order_id = $_POST['order_id'];

        if (isset($_POST['commentaire']) && trim($_POST['commentaire']) !== '') {
            $commentaire = trim($_POST['commentaire']);
        } else {
            $_SESSION['comment_temp'] = $_POST['commentaire'] ?? '';
            $_SESSION['error'] = 'Le commentaire est obligatoire';
            header('Location: /notation.php?order_id=' . urlencode($order_id));
            exit();
        }

        if (isset($_POST['rating']) && $_POST['rating'] > 0 && $_POST['rating'] <= 5) {
            $rating = $_POST['rating'];
        } else {
            $_SESSION['comment_temp'] = $_POST['commentaire'] ?? '';
            $_SESSION['error'] = 'La note est obligatoire';
            header('Location: /notation.php?order_id=' . urlencode($order_id));
            exit();
        }

        $orders = get_orders_by_user($_SESSION['uuid']);
        $order_found = false;
        foreach ($orders as $order) {
            if ($order['id_order'] === $order_id && $order['status'] === 'delivered') {
                $order_found = true;
            }
        }

        if (!$order_found) {
            header('Location: /profile.php');
            exit();
        }

        foreach ($notations as $note) {
            if (isset($note['order_id']) && isset($note['id_client']) && $note['order_id'] === $order_id && $note['id_client'] === $_SESSION['uuid']) {
                error_log('User ' . $_SESSION['uuid'] . ' has already submitted a review for order ' . $order_id);
                header('Location: /profile.php');
                exit();
            }
        }

        $new_notation = [
            'id' => uniqid(),
            'id_client' => $_SESSION['uuid'],
            'order_id' => $order_id,
            'commentaire' => $commentaire,
            'note' => $rating,
            'date_heure' => date('Y-m-d H:i:s')
        ];

        array_push($notations, $new_notation);
        file_put_contents($notations_file_path, json_encode($notations, JSON_PRETTY_PRINT));

        header('Location: /profile.php');
        exit();
    }

    // MARK: - API endpoints
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['add']) && isset($_POST['order_id'])) save_notation();
?>