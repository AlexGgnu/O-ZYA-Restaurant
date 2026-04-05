<?php
    session_start();

    function is_logged() {
        if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) return true;
        else return false;
    }

    function get_accounts_data() {
        $datas = file_get_contents(__DIR__ . "/../data/accounts.json");
        return json_decode($datas, true);
    }
    function get_account_by_id($id) {
        $accounts_data = get_accounts_data();

        foreach ($accounts_data as $account) {
            if ($account["id"] == $id) return $account;
        }

        return null;
    }

    function get_orders_data() {
        $datas = file_get_contents(__DIR__ . "/../data/orders.json");
    return json_decode($datas, true);
    }

    function get_orders_by_user($user_id) {
        $orders = get_orders_data();
        $result = [];

        foreach ($orders as $order) {
            if ($order["id_client"] == $user_id) {
                $result[] = $order;
            }
        }

        return $result;
    }

    function create_session($uuid, $role, $redirection = "/") {
        $_SESSION['uuid'] = $uuid;
        $_SESSION['role'] = $role;
        $_SESSION['logged_in'] = true;

        header("Location: " . $redirection);
    }
    function get_access($autorized_role, $redirect = false) {
        if (!is_logged() || $_SESSION["role"] !== $autorized_role && $_SESSION["role"] !== "admin") {
            if($redirect) header("Location: /");
            return false;
        } else return true;
    }
    function log_out() {
        session_destroy();
        header("Location: /");
    }

    function sign_up() {
        $accounts_data = get_accounts_data();
        $hash_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        foreach ($accounts_data as $account) {
            if ($account["email"] == $_POST["email"]) {
                header("Location: /registration.php?error=" . urlencode("Email deja utilise"));
                exit();
            }
        }

        if (!password_verify($_POST["confirme-pwd"], $hash_password)) {
            header("Location: /registration.php?error=" . urlencode("Les mots de passe ne correspondent pas"));
            exit();
        }

        $new_account_data = [
            "id" => uniqid(),
            "gender" => $_POST["gender"],
            "lastname" => $_POST["lastname"],
            "firstname" => $_POST["firstname"],
            "email" => $_POST["email"],
            "password" => $hash_password,
            "phone" => $_POST["phone"],
            "address" => $_POST["address"],
            "role" => "client"
        ];

        array_push($accounts_data, $new_account_data);
        file_put_contents(__DIR__ . "/../data/accounts.json", json_encode($accounts_data, JSON_PRETTY_PRINT));

        create_session($new_account_data['id'], $new_account_data['role']);
    }
    function log_in() {
        $accounts_data = get_accounts_data();
        $founded_account = false;

        foreach ($accounts_data as $account) {
            if ($account["email"] == $_POST["email"] && password_verify($_POST["password"], $account["password"])) {
                create_session($account["id"], $account["role"]);
                $founded_account = true;
                exit();
            }
        }

        if (!$founded_account) {
            header("Location: /connection.php?error=" . urlencode("Email ou mot de passe incorrect"));
            exit();
        }
    }

    if(isset($_GET['auth_method']) && $_GET['auth_method'] == "log_in") log_in();
    else if(isset($_GET['auth_method']) && $_GET['auth_method'] == "sign_up") sign_up();
    else if(isset($_GET['auth_method']) && $_GET['auth_method'] == "log_out") log_out();
?>