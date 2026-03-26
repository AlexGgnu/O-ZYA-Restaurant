<?php
    session_start();

    function is_logged() {
        if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) return true;
        else false;
    }

    function get_accounts_data() {
        $data = file_get_contents("../data/accounts.json");
        return json_decode($data, true);
    }

    function create_session($uuid, $role, $redirection = "/") {
        $_SESSION['uuid'] = $uuid;
        $_SESSION['role'] = $role;
        $_SESSION['logged_in'] = true;

        header("Location: " . $redirection);
        exit();
    }
    function get_access($autorized_role, $redirect = false) {
        if (!is_logged() || $_SESSION["role"] !== $autorized_role) {
            if($redirect) header("Location: ". $_SERVER['HTTP_REFERER']);
            return false;
        }
        else return true;
    }

    function sign_up() {
        $account_data = get_accounts_data();
        $hash_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        foreach ($account_data as $account) {
            if ($account["email"] == $_POST["email"]) {
                echo "Email déjà utilisé";
                exit();
            }
        }

        if (!password_verify($_POST["confirme-pwd"], $hash_password)) {
            echo "Les mots de passe ne correspondent pas";
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

        array_push($account_data, $new_account_data);
        file_put_contents("../data/accounts.json", json_encode($account_data, JSON_PRETTY_PRINT));

        create_session($new_account_data['id'], $new_account_data['role']);
    }
    function log_in() {
        $accounts_data = get_accounts_data();
        $founded_account = false;

        foreach ($accounts_data as $account) {
            if ($account["email"] == $_POST["email"] && password_verify($_POST["password"], $account["password"])) {
                create_session($account["id"], $account["role"], $_SERVER['HTTP_REFERER']);
                $founded_account = true;
                exit();
            }
        }

        if (!$founded_account) echo "Email ou mot de passe incorrect";
    }

    if(isset($_GET['auth_method']) && $_GET['auth_method'] == "log_in") log_in();
    else if(isset($_GET['auth_method']) && $_GET['auth_method'] == "sign_up") sign_up();
?>